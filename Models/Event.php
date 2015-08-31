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
                $id = !empty($id->recurringEventId)
                    ? $id->recurringEventId
                    : $id->id;
            }

			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$sql = "select  id, department_id, google_event_id, eventType_id,
                                startDate, endDate, startTime, endTime, rrule,
                                AsText(geography) geography, geography_description,
                                description, created, updated
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
					throw new \Exception('events/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setDateData('created', 'now');
		}
    }

    public function validate()
    {
        if (!$this->getStartDate() || !$this->getEndDate()
            || !$this->getGeography_description()
            || !$this->getDescription()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function save()
    {
        parent::setDateData('updated', 'now');
        $this->data['geography'] = new Expression("GeomFromText('{$this->getGeography()}')");
        parent::save();
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()                    { return parent::get('id');                    }
	public function getDepartment_id()         { return parent::get('getDepartment_id');      }
    public function getEventType_id()          { return parent::get('eventType_id');          }
	public function getGoogle_event_id()       { return parent::get('google_event_id');       }
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

    public function setDepartment_id($i) { parent::setForeignKeyField (__namespace__.'\Department', 'department_id', $i); }
    public function setDepartment   ($i) { parent::setForeignKeyObject(__namespace__.'\Department', 'department_id', $i); }
    public function setEventType_id ($i) { parent::setForeignKeyField (__namespace__.'\EventType',  'eventType_id',  $i); }
    public function setEventType    ($i) { parent::setForeignKeyObject(__namespace__.'\EventType',  'eventType_id',  $i); }
    public function setGoogle_event_id      ($s) { parent::set('google_event_id',       $s); }
    public function setDescription          ($s) { parent::set('description',           $s); }
    public function setGeography_description($s) { parent::set('geography_description', $s); }
    public function setGeography            ($s) { parent::set('geography', preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $s)); }
    public function setCreated  ($d) { parent::setDateData('created',   $d); }
    public function setStartDate($d) { parent::setDateData('startDate', $d, DATE_FORMAT, ActiveRecord::MYSQL_DATE_FORMAT); }
    public function setEndDate  ($d) { parent::setDateData('endDate',   $d, DATE_FORMAT, ActiveRecord::MYSQL_DATE_FORMAT); }
    public function setStartTime($t) { parent::setDateData('startTime', $t, TIME_FORMAT, ActiveRecord::MYSQL_TIME_FORMAT); }
    public function setEndTime  ($t) { parent::setDateData('endTime',   $t, TIME_FORMAT, ActiveRecord::MYSQL_TIME_FORMAT); }

    public function handleUpdate($post)
    {
        $fields = [
            'department_id', 'eventType_id',
            'description', 'geography', 'geography_description',
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
     * @return Recurr\Rule
     */
    public function getRRule()
    {
        $r = parent::get('rrule');
        if ($r) {
            $rrule = new Rule($r);
            return $rrule;
        }
    }

    /**
     * @param Recurr\Rule $rule
     */
    public function setRRule(Rule $rule)
    {
        parent::set('rrule', $rule->getString(Rule::TZ_FIXED));
    }

    /**
     * @return bool
     */
    public function isAllDay() { return $this->getStartTime() ? true : false; }

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
            $d = $person->getDepartment();
            if ($d && $this->getDepartment() === $d->getCode()) {
                return true;
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
}