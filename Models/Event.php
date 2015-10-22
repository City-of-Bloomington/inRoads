<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Zend\Db\Sql\Expression;
use Recurr\Rule;
use Recurr\Transformer\TextTransformer;

class Event extends ActiveRecord
{
	protected $tablename = 'events';

	protected $department;
	protected $eventType;

	private $modified = [];
	public $recurrence;

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
                                title, description, created, updated
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
		}
    }

    public function validate()
    {
        if (!$this->getCreated()) { parent::setDateData('created', 'now'); }

        if (!$this->getStartDate() || !$this->getEndDate()
            || !$this->getGeography_description()
            || !$this->getDescription()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    /**
     * Saves the event data to both the database and to Google.
     *
     * This function does the work of synchronizing the  data between
     * our local database and Google.
     * The data in our local database is the master copy.  We only use Google
     * as a kind of search engine for the events.
     */
    public function save()
    {
        parent::setDateData('updated', 'now');
        $this->data['geography'] = new Expression("GeomFromText('{$this->getGeography()}')");

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
                throw new \Exception('google/saveError');
            }
        }

        parent::save();
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
    public function getDescription()           { return parent::get('description');           }
    public function getGeography_description() { return parent::get('geography_description'); }
    public function getGeography()             { return parent::get('geography');             }
    public function getDepartment()            { return parent::getForeignKeyObject(__namespace__.'\Department', 'department_id'); }
    public function getEventType()             { return parent::getForeignKeyObject(__namespace__.'\EventType',  'eventType_id' ); }
    public function getCreated  ($f=null, $tz=null) { return parent::getDateData('created',   $f, $tz); }
    public function getUpdated  ($f=null, $tz=null) { return parent::getDateData('updated',   $f, $tz); }
    public function getStartDate($f=null, $tz=null) { return parent::getDateData('startDate', $f, $tz); }
    public function getEndDate  ($f=null, $tz=null) { return parent::getDateData('endDate',   $f, $tz); }
    public function getStartTime($f=null, $tz=null) { return parent::getDateData('startTime', $f, $tz); }
    public function getEndTime  ($f=null, $tz=null) { return parent::getDateData('endTime',   $f, $tz); }
    public function getRRule()
    {
        $r = parent::get('rrule');
        if ($r) {
            $rrule = new Rule($r);
            return $rrule;
        }
    }

    public function setGoogle_event_id($s) { parent::set('google_event_id', $s); }
    public function setGeography      ($s) { parent::set('geography', preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $s)); }

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

    public function setStartDate($d)
    {
        $prev = parent::get('startDate');
        parent::setDateData('startDate', $d, DATE_FORMAT, ActiveRecord::MYSQL_DATE_FORMAT);
        if (parent::get('startDate') !== $prev) { $this->modified['start'] = true; }
    }

    public function setEndDate($d)
    {
        $prev = parent::get('endDate');
        parent::setDateData('endDate', $d, DATE_FORMAT, ActiveRecord::MYSQL_DATE_FORMAT);
        if (parent::get('endDate') !== $prev) { $this->modified['end'] = true; }
    }

    public function setStartTime($t)
    {
        $prev = parent::get('startTime');
        parent::setDateData('startTime', $t, TIME_FORMAT, ActiveRecord::MYSQL_TIME_FORMAT);
        if (parent::get('startTime') !== $prev) { $this->modified['start'] = true; }
    }

    public function setEndTime($t)
    {
        $prev = parent::get('endTime');
        parent::setDateData('endTime', $t, TIME_FORMAT, ActiveRecord::MYSQL_TIME_FORMAT);
        if (parent::get('endTime') !== $prev) { $this->modified['end'] = true; }
    }

    public function setRRule(Rule $rule)
    {
        $r = $rule->getString(Rule::TZ_FIXED);
        if ($r !== parent::get('rrule')) { $this->modified['recurrence'] = true; }
        parent::set('rrule', $r);
    }

    public function handleUpdate($post)
    {
        $fields = [
            'department_id', 'eventType_id', 'google_event_id', 'title',
            'description', 'geography', 'geography_description'
        ];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }

        $this->setStartDate($post['start']['date']);
        $this->setStartTime($post['start']['time']);
        $this->setEndDate($post['end']['date']);
        $this->setEndTime($post['end']['time']);

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
            $this->setRRule($recur);
        }
    }

	//----------------------------------------------------------------
	// Custom functions
    //----------------------------------------------------------------
    /**
     * @return bool
     */
    public function isAllDay() { return $this->getStartTime() ? false : true; }

    /**
     * Combines startDate and startTime into a single datetime output
     *
     * This function reads from event data stored in the local database
     *
     * @param string $format
     * @return string
     */
    public function getStart($format=DATETIME_FORMAT)
    {
        $d = new \DateTime("{$this->getStartDate()} {$this->getStartTime()}");
        return $d->format($format);
    }
    public function getEnd($format=DATETIME_FORMAT)
    {
        $d = new \DateTime("{$this->getEndDate()} {$this->getEndTime()}");
        return $d->format($format);
    }

    /**
     * @param string $dateFormat
     * @param string $timeFormat
     * @return string
     */
    public function getHumanReadableDuration($dateFormat=DATE_FORMAT, $timeFormat=TIME_FORMAT)
    {
        $startDate = $this->getStartDate($dateFormat);
          $endDate = $this->getEndDate  ($dateFormat);

        if (!$this->isAllDay()) {
            $startTime = $this->getStartTime($timeFormat);
              $endTime = $this->getEndTime  ($timeFormat);
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
            // We have to send start and end if there's an RRULE
            $this->modified['start'] = true;
            $this->modified['end']   = true;

            $rrule = 'RRULE:'.$this->getRRule()->getString(Rule::TZ_FIXED);
            $patch->setRecurrence([$rrule]);
        }

        if (!empty($this->modified['start']) || !empty($this->modified['end'])) {
            $timezone = ini_get('date.timezone');

            if ($this->isAllDay()) {
                $startDate = $this->getStartDate(GoogleGateway::DATE_FORMAT);
                $endDate   = $this->getEndDate  (GoogleGateway::DATE_FORMAT);
                if (!$startDate || !$endDate) {
                    throw new \Exception('events/invalidDate');
                }

                $patch->setStart(new \Google_Service_Calendar_EventDateTime([
                    'date'     => $startDate,
                    'dateTime' => \Google_Model::NULL_VALUE,
                    'timeZone' => $timezone
                ]));
                $patch->setEnd  (new \Google_Service_Calendar_EventDateTime([
                    'date'     => $endDate,
                    'dateTime' => \Google_Model::NULL_VALUE,
                    'timeZone' => $timezone
                ]));
            }
            else {
                $startDate = \DateTime::createFromFormat(ActiveRecord::MYSQL_DATETIME_FORMAT, "{$this->getStartDate()} {$this->getStartTime()}");
                $endDate   = \DateTime::createFromFormat(ActiveRecord::MYSQL_DATETIME_FORMAT, "{$this->getEndDate()} {$this->getEndTime()}");
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
}
