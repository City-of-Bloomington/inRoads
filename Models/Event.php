<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
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
    protected $jurisdiction;

    public static $STATUS = ['ACTIVE', 'ARCHIVED'];
    public static $TYPES  = [
        'CONSTRUCTION'      => 'planned road work',
        'SPECIAL_EVENT'     => 'special events (fair, sport event, etc.)',
        'INCIDENT'          => 'accidents and other unexpected events',
        'WEATHER_CONDITION' => 'Weather condition affecting the road',
        'ROAD_CONDITION'    => 'Status of the road that might affect travelers.'
    ];
    public static $SEVERITIES = [
        'MINOR'    => 'the event has very limited impact on traffic.',
        'MODERATE' => 'the event will have a visible impact on traffic but should not create significant delay; if there is a delay, it should be small and local.',
        'MAJOR'    => 'the event will have a significant impact on traffic, probably on a large scale.',
        'UNKNOWN'  => 'the impact is unknown, for example in the case of an accident that has been recorded without any precise description.'
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
                $sql = "select id,jurisdiction_id, eventType, severity, status, created, updated,
                               headline, description, detour, AsText(geography) geography
                        from events where id=?";
                $result = $zend_db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('events/unknownEvent');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
            $this->setCreated('now');
            $this->setStatus(self::$STATUS[0]);
        }
    }

    public function validate()
    {
        if ( !$this->getJurisdiction_id()
            || !$this->getEventType()
            || !$this->getSeverity()
            || !$this->getStatus()
            || !$this->getHeadline()) {
            throw new \Exception('missingRequiredFields');
        }
        if (!array_key_exists($this->getEventType(), self::$TYPES     )) { throw new \Exception('events/unknownType'    ); }
        if (!array_key_exists($this->getSeverity(),  self::$SEVERITIES)) { throw new \Exception('events/unknownSeverity'); }
        if (        !in_array($this->getStatus(),    self::$STATUS    )) { throw new \Exception('events/unknownStatus'  ); }
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
    public function getId()          { return parent::get('id');          }
    public function getEventType()   { return parent::get('eventType');   }
    public function getSeverity()    { return parent::get('severity');    }
    public function getStatus()      { return parent::get('status');      }
    public function getHeadline()    { return parent::get('headline');    }
    public function getDescription() { return parent::get('description'); }
    public function getDetour()      { return parent::get('detour');      }
    public function getGeography()   { return parent::get('geography');   }
    public function getCreated($f=null, $tz=null) { return parent::getDateData('created', $f, $tz); }
    public function getUpdated($f=null, $tz=null) { return parent::getDateData('updated', $f, $tz); }
    public function getJurisdiction_id() { return parent::get('jurisdiction_id'); }
    public function getJurisdiction()    { return parent::getForeignKeyObject(__namespace__.'\Jurisdiction', 'jurisdiction_id'); }

    public function setEventType  ($s) { parent::set('eventType',   $s); }
    public function setSeverity   ($s) { parent::set('severity',    $s); }
    public function setStatus     ($s) { parent::set('status',      $s); }
    public function setHeadline   ($s) { parent::set('headline',    $s); }
    public function setDescription($s) { parent::set('description', $s); }
    public function setDetour     ($s) { parent::set('detour',      $s); }
    public function setGeography  ($s) { parent::set('geography', preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $s)); }
    public function setCreated($d) { parent::setDateData('created', $d); }
    public function setJurisdiction_id($i) { parent::setForeignKeyField (__namespace__.'\Jurisdiction', 'jurisdiction_id', $i); }
    public function setJurisdiction   ($o) { parent::setForeignKeyObject(__namespace__.'\Jurisdiction', 'jurisdiction_id', $o); }

    public function handleUpdate($post)
    {
        $fields = [
            'eventType', 'severity', 'status', 'headline', 'description', 'detour', 'jurisdiction_id', 'geography'
        ];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
    }
}