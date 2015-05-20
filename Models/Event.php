<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Zend\Db\Sql\Expression;

class Event extends ActiveRecord
{
    protected $tablename = 'events';

    public static $TYPES  = [
        'ROAD CLOSED'        => 'expect to detour, signage in place',
        'LOCAL TRAFFIC ONLY' => 'expect delays, signage in place'
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
     * @param int|string|array $id (ID, email, username)
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $zend_db = Database::getConnection();
                $sql = "select id, eventType, created, updated, startDate, endDate
                        description, geography_description, AsText(geography) geography
                        from events where id=?";
                $result = $zend_db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('event/unknown');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
            $this->setCreated('now');
        }
    }

	/**
	 * Performs validation checks and returns any problems
	 *
	 * @return array Errors
	 */
    public function validate()
    {
        $errors = [];

        $requiredFields = [ 'eventType', 'startDate', 'endDate' ];
        foreach ($requiredFields as $f) {
            $get = ucfirst($f);
            if (!$this->$get()) { $errors[$f] = ['missingRequiredFields']; }
        }

        if (!array_key_exists($this->getEventType(), self::$TYPES)) {
            $errors['eventType'][] = 'unknown';
        }

        if (count($errors)) {
            return ['event' => $errors];
        }
    }

	/**
	 * @return array Errors
	 */
    public function save()
    {
        parent::setDateData('updated', 'now');
        $this->data['geography'] = new Expression("GeomFromText('{$this->getGeography()}')");

        return parent::save();
    }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()          { return parent::get('id');          }
    public function getEventType()   { return parent::get('eventType');   }
    public function getDescription() { return parent::get('description'); }
    public function getGeography()   { return parent::get('geography');   }
    public function getGeography_description() { return parent::get('geography_description'); }
    public function getCreated  ($f=null, $tz=null) { return parent::getDateData('created',   $f, $tz); }
    public function getUpdated  ($f=null, $tz=null) { return parent::getDateData('updated',   $f, $tz); }
    public function getStartDate($f=null, $tz=null) { return parent::getDateData('startDate', $f, $tz); }
    public function getEndDate  ($f=null, $tz=null) { return parent::getDateData('endDate',   $f, $tz); }

    public function setEventType  ($s) { parent::set('eventType',   $s); }
    public function setDescription($s) { parent::set('description', $s); }
    public function setGeography  ($s) { parent::set('geography', preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $s)); }
    public function setGeography_description($s) { parent::set('geography_description', $s); }
    public function setCreated  ($d) { parent::setDateData('created',   $d); }
    public function setStartDate($d) { parent::setDateData('startDate', $d); }
    public function setEndDate  ($d) { parent::setDateData('endDate',   $d); }

    public function handleUpdate($post)
    {
        $fields = [
            'eventType', 'startDate', 'endDate', 'description', 'geography', 'geography_description'
        ];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
    }
}