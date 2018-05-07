<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Zend\Db\Sql\Expression;
use Recurr\Rule;
use Recurr\Transformer\TextTransformer;

class Event extends ActiveRecord
{
    const MAX_DESCRIPTION_LENGTH = 1000;

    const ERROR_INVALID_DATE = 'invalidDate';

	protected $tablename = 'events';

	protected $department;
	protected $eventType;

	private $modified = [];
	public $recurrence;

	// DateTime fields and the database formats expected
	private static $DATETIME_FIELDS = [
        'startDate' => 'Y-m-d',
          'endDate' => 'Y-m-d',
        'startTime' => 'H:i:s',
          'endTime' => 'H:i:s'
	];

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|string|array|Google_Service_Calendar_Event $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
            // Use the eventID for any GoogleEvent passed in
            if ($id instanceof \Google_Service_Calendar_Event) {
                $google_event = clone($id);
                if (!empty($id->recurringEventId)) {
                    $id =  $id->recurringEventId;
                    $this->recurrence = $google_event;
                }
                else  { $id = $id->id; }
            }

			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$sql = "select  id, department_id, google_event_id, eventType_id,
                                startDate, endDate, startTime, endTime, rrule,
                                AsText(geography) geography, geography_description,
                                title, primaryContact, description,
                                created, updated, constructionFlag
                        from events ";
                $sql.= ActiveRecord::isId($id)
                    ? 'where id=?'
                    : 'where google_event_id=?';

				$zend_db = Database::getConnection();
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
                    // We could not find an event in our local database

                    // If we have a google_event_id, we need to use the
                    // GoogleGateway to look up that event, and hydrate it
                    if (!ActiveRecord::isId($id)) {
                        $google_event = GoogleGateway::getEvent(GOOGLE_CALENDAR_ID, $id);
                    }

                    // If we have a Google Event, create a new local event
                    // from the Google Event data.
                    if (isset($google_event)) {
                        $this->data = GoogleGateway::createLocalEventData($google_event);
                    }
                    else {
                        throw new \Exception('events/unknown');
                    }
				}
			}
            // If there's not a Google entry for this local event,
            // set all the Google fields to modified, so that when
            // we save this event, we'll upload the event to Google.
			if (!$this->getGoogle_event_id()) {
                $this->modified = [
                    'summary'     => true,
                    'description' => true,
                    'start'       => true,
                    'end'         => true
                ];
                if ($this->getRRule()) { $this->modified['recurrence'] = true; }
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			parent::setDateData('created', 'now');
			$this->setConstructionFlag(true);
		}
    }

    /**
     * Populate object data from a raw row of data from the database
     */
    public function exchangeArray($row)
    {
        // Convert dates and times to PHP DateTime objects
        foreach (self::$DATETIME_FIELDS as $f => $format) {
            if (!empty($row[$f])) {
                $date    = \DateTime::createFromFormat($format, $row[$f]);
                $row[$f] = $date ? $date : null;
            }
        }

        $this->data = $row;
    }


    /**
     * WARNING:
     * This is a non-standard validate function.  It returns an Exception,
     * rather than throwing it.
     *
     * ActiveRecord::save() cannot be called with this function defined as it is.
     *
     * @return \Exception
     */
    public function validate()
    {
        if (!$this->getCreated()) { parent::setDateData('created', 'now'); }

        if (!$this->getGeography()) {
            return new \Exception('events/missingGeography');
        }

        if (!$this->getStartDate() || !$this->getEndDate()
            || !$this->getTitle()  || !$this->getGeography_description()
            || !$this->getDescription()) {
            return new \Exception('missingRequiredFields');
        }

        $start = (int)$this->getStartDate()->format('U');
        $end   = (int)$this->getEndDate  ()->format('U');
        if (($start < 0) || ($end < 0)) {
            return new \Exception('invalidDate');
        }

        if (strlen($this->getDescription()) > self::MAX_DESCRIPTION_LENGTH) {
            return new \Exception('description_length');
        }
    }

    /**
     * Saves the event data to both the database and to Google.
     *
     * This function does the work of synchronizing the  data between
     * our local database and Google.
     * The data in our local database is the master copy.  We only use Google
     * as a kind of search engine for the events.
     *
     * WARNING:
     * This is a very non-standard save function.  ActiveRecord::save() cannot
     * be called with the validation being done this way.
     *
     * We should consider changing the way validation happens in future Blossom
     */
    public function save()
    {
        parent::setDateData('updated', 'now');

        // Save the geography text version
        // We'll need to switch the data back, if there's a problem
        $geography = $this->getGeography();
        if ($geography) {
            $this->data['geography'] = new Expression("GeomFromText('$geography')");
        }
        else { $this->data['geography'] = null; }

		$exception = $this->validate();
		if (!$exception) {
            // If we've modified any Google fields, upload the event to Google
            if (count($this->modified)) {
                $patch = $this->createGooglePatch();
                $google_event_id = $this->getGoogle_event_id();

                $event = $google_event_id
                    ? GoogleGateway:: patchEvent(GOOGLE_CALENDAR_ID, $google_event_id, $patch)
                    : GoogleGateway::insertEvent(GOOGLE_CALENDAR_ID, $patch);

                if ($event instanceof \Google_Service_Calendar_Event) {
                    if (!$google_event_id) {
                        $google_event_id = !empty($event->recurringEventId)
                            ? $event->recurringEventId
                            : $event->id;
                        parent::set('google_event_id', $google_event_id);
                    }
                }
                else {
                    $exception = new \Exception('google/saveError');
                }
            }
        }

        if (!$exception) {
            // This is code from ActiveRecord::save()
            // We are overriding it here, because we are doing something special
            // with the validation.
            //
            // We have a geography field that has to be converted into an Expression for saving,
            // but has to be converted back to raw text before we try to do anything else.
            //
            // The implementation of this, for now, is that our $this->validate() functions
            // differently from the default ActiveRecord::save() expects
            //
            // In the future, we should look into making validate always return an array of
            // errors, instead of just throwing an exception on the first error it comes to.

            // Convert the DateTime objects back to strings
            foreach (self::$DATETIME_FIELDS as $f => $format) {
                if (!empty($this->data[$f])) {
                    $this->data[$f] = $this->data[$f]->format($format);
                }
            }

            $zend_db = Database::getConnection();
            $sql = new \Zend\Db\Sql\Sql($zend_db, $this->tablename);
            if ($this->getId()) {
                $update = $sql->update()
                    ->set($this->data)
                    ->where(['id'=>$this->getId()]);
                $sql->prepareStatementForSqlObject($update)->execute();
            }
            else {
                $insert = $sql->insert()->values($this->data);
                $sql->prepareStatementForSqlObject($insert)->execute();
                $this->data['id'] = $zend_db->getDriver()->getLastGeneratedValue();
            }
		}

        $this->data['geography'] = $geography;
        if ($exception) { throw $exception; }
    }

    public function delete()
    {
        $sql = 'delete from segments where event_id=?';
        $zend_db = Database::getConnection();
        $zend_db->query($sql, [$this->getId()]);

        GoogleGateway::deleteEvent(GOOGLE_CALENDAR_ID, $this->getGoogle_event_id());
        parent::delete();
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                    { return parent::get('id');                    }
	public function getDepartment_id()         { return parent::get('department_id');         }
    public function getEventType_id()          { return parent::get('eventType_id');          }
	public function getGoogle_event_id()       { return parent::get('google_event_id');       }
	public function getTitle()                 { return parent::get('title');                 }
	public function getPrimaryContact()        { return parent::get('primaryContact');        }
    public function getDescription()           { return parent::get('description');           }
    public function getGeography_description() { return parent::get('geography_description'); }
    public function getGeography()             { return parent::get('geography');             }
    public function getConstructionFlag()      { return parent::get('constructionFlag');      }
    public function getDepartment()            { return parent::getForeignKeyObject(__namespace__.'\Department', 'department_id'); }
    public function getEventType()             { return parent::getForeignKeyObject(__namespace__.'\EventType',  'eventType_id' ); }
    public function getCreated  ($f=null, $tz=null) { return parent::getDateData('created',   $f, $tz); }
    public function getUpdated  ($f=null, $tz=null) { return parent::getDateData('updated',   $f, $tz); }
    public function getStartDate()             { return parent::get('startDate'); }
    public function getEndDate  ()             { return parent::get(  'endDate'); }
    public function getStartTime()             { return parent::get('startTime'); }
    public function getEndTime  ()             { return parent::get(  'endTime'); }
    public function getRRule()
    {
        $r = parent::get('rrule');
        if ($r) {
            $rrule = new Rule($r);
            return $rrule;
        }
    }

    public function setPrimaryContact  ($s) { parent::set('primaryContact',  $s); }
    public function setGoogle_event_id ($s) { parent::set('google_event_id', $s); }
    public function setGeography       ($s) { parent::set('geography', preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $s)); }
    public function setConstructionFlag($b) { $this->data['constructionFlag'] = $b ? 1 : 0; }

    public function setDepartment_id($id)
    {
        if ($id !== parent::get('department_id')) { $this->modified['summary'] = true; }
        parent::setForeignKeyField (__namespace__.'\Department', 'department_id', $id);
    }

    public function setDepartment(Department $d)
    {
        if ($d->getId() !== parent::get('department_id')) { $this->modified['summary'] = true; }
        parent::setForeignKeyObject(__namespace__.'\Department', 'department_id', $d);
    }

    public function setEventType_id($id)
    {
        if ($id !== parent::get('eventType_id')) { $this->modified['summary'] = true; }
        parent::setForeignKeyField (__namespace__.'\EventType',  'eventType_id',  $id);
    }

    public function setEventType(EventType $t)
    {
        if ($t->getId() !== parent::get('eventType_id')) { $this->modified['summary'] = true; }
        parent::setForeignKeyObject(__namespace__.'\EventType',  'eventType_id',  $t);
    }

    public function setTitle($s)
    {
        if ($s !== $this->getTitle()) { $this->modified['summary'] = true; }
        parent::set('title', $s);
    }

    public function setDescription($s)
    {
        if ($s !== parent::get('description')) { $this->modified['description'] = true; }
        parent::set('description', $s);
    }

    public function setGeography_description($s)
    {
        if ($s !== parent::get('geography_description')) { $this->modified['location'] = true; }
        parent::set('geography_description', $s);
    }

    public function setStartDate(\DateTime $d=null)
    {
        if ($this->get ('startDate') !== $d) {
            $this->data['startDate'] =   $d;
            $this->modified['start'] = true;
        }
    }

    public function setEndDate(\DateTime $d=null)
    {
        if ($this->get ('endDate') !== $d) {
            $this->data['endDate'] =   $d;
            $this->modified['end'] = true;
        }
    }

    public function setStartTime(\DateTime $t=null)
    {
        if ($this->get ('startTime') !== $t) {
            $this->data['startTime'] =   $t;
            $this->modified['start'] = true;
        }
    }

    public function setEndTime(\DateTime $t=null)
    {
        if ($this->get ('endTime') !== $t) {
            $this->data['endTime'] =   $t;
            $this->modified['end'] = true;
        }
    }

    public function setRRule(Rule $rule=null)
    {
        $r = $rule ? $rule->getString(Rule::TZ_FIXED) : null;
        if ($r !== parent::get('rrule')) { $this->modified['recurrence'] = true; }
        parent::set('rrule', $r);
    }

    public function handleUpdate($post)
    {
        if (empty($post['id'])) {
            $action  =  EventHistory::ACTION_UPDATED;
            $changes = [EventHistory::STATE_ORIGINAL => $this->data];
        }
        else {
            $action  = EventHistory::ACTION_CREATED;
            $changes = [];
        }

        $fields = [
            'department_id', 'eventType_id', 'google_event_id',
            'title', 'primaryContact', 'description',
            'geography', 'geography_description'
        ];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
        $this->setConstructionFlag(isset($post['constructionFlag']) && $post['constructionFlag']);

        // Convert browser date and time strings to DateTime objects
        if (!empty($post['start']['date'])) {
            $d = \DateTime::createFromFormat('Y-m-d', $post['start']['date']);
            if ($d) { $this->setStartDate($d); }
            else { throw new \Exception(self::ERROR_INVALID_DATE); }
        }
        if (!empty($post['start']['time'])) {
            $d = \DateTime::createFromFormat('H:i', $post['start']['time']);
            if ($d) { $this->setStartTime($d); }
            else { throw new \Exception(self::ERROR_INVALID_DATE); }
        }
        if (!empty($post['end']['date'])) {
            $d = \DateTime::createFromFormat('Y-m-d', $post['end']['date']);
            if ($d) { $this->setEndDate($d); }
            else { throw new \Exception(self::ERROR_INVALID_DATE); }
        }
        if (!empty($post['end']['time'])) {
            $d = \DateTime::createFromFormat('H:i', $post['end']['time']);
            if ($d) { $this->setEndTime($d); }
            else { throw new \Exception(self::ERROR_INVALID_DATE); }
        }

        $this->setRRule($this->createRRule($post));
        $this->save();

        $changes[EventHistory::STATE_UPDATED] = $this->data;
        EventHistory::saveNewEntry($this->getId(), $action, $changes);
    }

    /**
     * Extracts information from POST and creates an RRULE
     *
     * @param  array $post  The $_POST array
     * @return Rule
     */
    private function createRRule(array $post)
    {
        $recur = null;
        if (!empty($post['frequency'])) {
            $recur = new Rule();
            $recur->setFreq($post['frequency']);
            switch ($post['frequency']) {
                case 'DAILY':
                    $recur->setInterval($post['DAILY']['interval']);
                    break;

                case 'WEEKLY':
                    $recur->setInterval(        $post['WEEKLY']['interval']);
                    $recur->setByDay(array_keys($post['WEEKLY']['byday']));
                    break;

                case 'MONTHLY':
                    switch ($post['MONTHLY']['type']) {
                        case 'BYMONTHDAY':
                            $recur->setByMonthDay([$post['MONTHLY']['bymonthday']['daylist']]);
                            $recur->setInterval  ( $post['MONTHLY']['bymonthday']['interval']);
                            break;
                        case 'BYDAY':
                            $day = $post['MONTHLY']['byday']['offset'].$post['MONTHLY']['byday']['day'];
                            $recur->setByDay([$day]);
                            $recur->setInterval($post['MONTHLY']['byday']['interval']);
                            break;
                    }
                    break;
            }
            switch ($post['RRULE_END']['type']) {
                case 'count':
                    $recur->setCount($post['RRULE_END']['count']);
                    break;
                case 'until':
                    $until = \DateTime::createFromFormat(DATE_FORMAT, $post['RRULE_END']['until']['date']);
                    $recur->setUntil($until);
                    break;
            }
        }
        return $recur;
    }

	//----------------------------------------------------------------
	// Custom functions
    //----------------------------------------------------------------
    /**
     * @return bool
     */
    public function isAllDay()       { return $this->getStartTime()        ? false : true;  }
    public function isConstruction() { return $this->getConstructionFlag() ? true  : false; }

    /**
     * Combines startDate and startTime into a single datetime output
     *
     * @return \DateTime
     */
    public function getStart()
    {
        $startDate = $this->getStartDate();
        $startTime = $this->getStartTime();
        if ($startDate) {
            $dateString = $startDate->format('Y-m-d');
            if ($startTime) {
                $dateString.= ' '.$startTime->format('H:i:s');
            }
            return new \DateTime($dateString);
        }
    }

    /**
     * Combines endDate and endTime into a single datetime output
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        $endDate = $this->getEndDate();
        if ($endDate) {
            $endTime = $this->isAllDay()
                ? '23:59:59'
                : $this->getEndTime()->format('H:i:s');

            $dateString = $endDate->format('Y-m-d').' '.$endTime;
            return new \DateTime($dateString);
        }
    }

    /**
     * @param string $dateFormat
     * @param string $timeFormat
     * @return string
     */
    public function getHumanReadableDuration($dateFormat=DATE_FORMAT, $timeFormat=TIME_FORMAT)
    {
        $startDate = $this->getStartDate()->format($dateFormat);
          $endDate = $this->getEndDate  ()->format($dateFormat);

        if (!$this->isAllDay()) {
            $startTime = $this->getStartTime()->format($timeFormat);
              $endTime = $this->getEndTime  ()->format($timeFormat);
        }
        else {
            $startTime = null;
              $endTime = null;
        }

                                           $text = $startDate;
        if ($startTime)                  { $text.= ' '.$startTime; }
        if ($endDate !== $startDate
            || $endTime) {
                                           $text.= ' to ';
            if ($endTime)                { $text.= ' '.$endTime; }
            if ($endDate !== $startDate) { $text.= ' '.$endDate; }
        }

        $rule = $this->getRRule();
        if ($rule) {
            $t = new TextTransformer();
            $text.= ' '.$t->transform($rule);
        }
        return $text;
    }

    /**
     * Checks whether a person is allowed to edit this event.
     *
     * Staff and Administrators should be able to work with any
     * event in the system.
     *
     * Public users should be able to create events;
     * but only be able to edit events for their own department.
     * Public users must have a department assigned, otherwise
     * they will not be able to create events.
     *
     * @param Person $person
     * @return bool
     */
    public function permitsEditingBy(Person $person)
    {
        if (   $person->getRole() === 'Administrator'
            || $person->getRole() === 'Staff') {
            return true;
        }

        if (   $person->getRole() === 'Public') {
            $personDepartment = $person->getDepartment();
             $eventDepartment =   $this->getDepartment();

            if ($personDepartment) {
                if (!$eventDepartment) { return true; }
                else {
                    return $personDepartment->getId() === $eventDepartment->getId();
                }
            }
        }
        return false;
    }

    /**
     * @return array An array of Department objects
     */
    public static function validDepartments(Person $person)
    {
        if ($person->getRole() === 'Public') {
            $d = $person->getDepartment();
            if ($d) { return [$d]; }
        }
        if (   $person->getRole() == 'Administrator'
            || $person->getRole() == 'Staff') {

            $table = new DepartmentsTable();
            return $table->find();
        }
        return [];
    }

    /**
     * @return Google_Service_Calendar_Event
     */
    private function createGooglePatch()
    {
        $patch = new \Google_Service_Calendar_Event();

        if (!empty($this->modified['summary'])) {
            $summary = $this->getTitle();
            if ($this->getEventType())  { $summary = "{$this->getEventType()}-$summary"; }
            if ($this->getDepartment()) { $summary = "{$this->getDepartment()->getCode()}-$summary"; }
            $patch->setSummary($summary);
        }

        if (!empty($this->modified['location'])) {
            $patch->setLocation($this->getGeography_description());
        }

        if (!empty($this->modified['description'])) {
            $patch->setDescription($this->getDescription());
        }

        if (!empty($this->modified['recurrence'])) {
            $recur = $this->getRRule();
            if ($recur) {
                // We have to send start and end if there's an RRULE
                $this->modified['start'] = true;
                $this->modified['end']   = true;

                $rrule = ['RRULE:'.$this->getRRule()->getString(Rule::TZ_FIXED)];
            }
            else {
                $rrule = \Google_Model::NULL_VALUE;
            }
            $patch->setRecurrence($rrule);
        }

        if (!empty($this->modified['start']) || !empty($this->modified['end'])) {
            $timezone = ini_get('date.timezone');

            if ($this->isAllDay()) {
                if (!$this->getStartDate() || !$this->getEndDate()) {
                    throw new \Exception('events/invalidDate');
                }

                $patch->setStart(new \Google_Service_Calendar_EventDateTime([
                    'date'     => $this->getStartDate()->format(GoogleGateway::DATE_FORMAT),
                    'dateTime' => \Google_Model::NULL_VALUE,
                    'timeZone' => $timezone
                ]));
                $patch->setEnd  (new \Google_Service_Calendar_EventDateTime([
                    'date'     => $this->getEndDate()->format(GoogleGateway::DATE_FORMAT),
                    'dateTime' => \Google_Model::NULL_VALUE,
                    'timeZone' => $timezone
                ]));
            }
            else {
                $startDate = $this->getStart();
                $endDate   = $this->getEnd();
                if (!$startDate || !$endDate) {
                    throw new \Exception('events/invalidDate');
                }

                $patch->setStart(new \Google_Service_Calendar_EventDateTime([
                    'date'     => \Google_Model::NULL_VALUE,
                    'dateTime' => $startDate->format(GoogleGateway::DATETIME_FORMAT),
                    'timeZone' => $timezone
                ]));
                $patch->setEnd  (new \Google_Service_Calendar_EventDateTime([
                    'date'     => \Google_Model::NULL_VALUE,
                    'dateTime' => $endDate->format(GoogleGateway::DATETIME_FORMAT),
                    'timeZone' => $timezone
                ]));
            }
        }

        return $patch;
    }

    /**
     * @return array Segments
     */
    public function getSegments()
    {
        $table = new SegmentsTable();
        return $table->find(['event_id'=>$this->getId()]);
    }

    /**
     * @return array  An array of EventHistory objects
     */
    public function getHistory(): array
    {
        $history = [];
        $zend_db = Database::getConnection();
        $sql     = 'select * from eventHistory where event_id=? order by date desc';
        $result  = $zend_db->query($sql)->execute([$this->getId()]);
        foreach ($result as $row) {
            $history[] = new EventHistory($row);
        }
        return $history;
    }
}
