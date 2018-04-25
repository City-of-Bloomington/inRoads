<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Segment extends ActiveRecord
{
	protected $tablename = 'segments';

	protected $event;

	public static $directions = [
        'NB/SB', 'EB/WB', 'NB', 'SB', 'EB', 'WB'
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
                $sql = 'select * from segments where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('segments/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
    }

    public function validate()
    {
        $requiredFields = [
            'event_id', 'street', 'streetFrom', 'streetTo','direction'
        ];
        foreach ($requiredFields as $f) {
            $get = 'get'.ucfirst($f);
            if (!$this->$get()) { throw new \Exception("missingRequiredField/$f"); }
        }

        // latitude and longitude are required.  But they can be looked up from the AddressService
        if (   !$this->getStartLatitude() || !$this->getStartLongitude()
            || !$this->getEndLatitude()   || !$this->getEndLongitude()) {

            $start = AddressService::intersection($this->getStreet(), $this->getStreetFrom());
            $end   = AddressService::intersection($this->getStreet(), $this->getStreetTo());
            if ($start) {
                $this->setStartLatitude ($start->latitude);
                $this->setStartLongitude($start->longitude);
            }
            if ($end) {
                $this->setEndLatitude ($end->latitude);
                $this->setEndLongitude($end->longitude);
            }
        }

        $this->validateDirection();
    }

    public function save() { parent::save(); }

    public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId            () { return parent::get('id'            ); }
	public function getEvent_id      () { return parent::get('event_id'      ); }
	public function getStreet        () { return parent::get('street'        ); }
	public function getStreetFrom    () { return parent::get('streetFrom'    ); }
	public function getStreetTo      () { return parent::get('streetTo'      ); }
	public function getDirection     () { return parent::get('direction'     ); }
	public function getStartLatitude () { return parent::get('startLatitude' ); }
	public function getStartLongitude() { return parent::get('startLongitude'); }
	public function getEndLatitude   () { return parent::get('endLatitude'   ); }
	public function getEndLongitude  () { return parent::get('endLongitude'  ); }
    public function getEvent() { return parent::getForeignKeyObject(__namespace__.'\Event', 'event_id'); }

    public function setStreet        ($s) { parent::set('street',     $s); }
    public function setStreetFrom    ($s) { parent::set('streetFrom', $s); }
    public function setStreetTo      ($s) { parent::set('streetTo',   $s); }
    public function setDirection     ($s) { parent::set('direction',  $s); }
    public function setStartLatitude ($s) { parent::set('startLatitude',  (float)$s); }
    public function setStartLongitude($s) { parent::set('startLongitude', (float)$s); }
    public function setEndLatitude   ($s) { parent::set('endLatitude',    (float)$s); }
    public function setEndLongitude  ($s) { parent::set('endLongitude',   (float)$s); }
    public function setEvent_id($i) { parent::setForeignKeyField (__namespace__.'\Event', 'event_id', $i); }
    public function setEvent   ($i) { parent::setForeignKeyObject(__namespace__.'\Event', 'event_id', $i); }

    public function handleUpdate(array $post)
    {
        $fields = ['street', 'streetFrom', 'streetTo', 'direction'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
    }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function validateDirection()
	{
        // Make sure lat-long start and end match intersections of streets from and to
        $dir      = $this->getDirection();
        $startLat = $this->getStartLatitude();
        $startLon = $this->getStartLongitude();
        $endLat   = $this->getEndLatitude();
        $endLon   = $this->getEndLongitude();

        if (    (($dir === 'NB' || $dir === 'NB/SB') && $startLat > $endLat)
            ||  (($dir === 'SB')                     && $startLat < $endLat)
            ||  (($dir === 'EB' || $dir === 'EB/WB') && $startLon > $endLon)
            ||  (($dir === 'WB')                     && $startLon < $endLon)) {

            $this->flipFromAndTo();
        }
	}

	public function flipFromAndTo()
	{
        $startLat = $this->getStartLatitude();
        $startLon = $this->getStartLongitude();
        $endLat   = $this->getEndLatitude();
        $endLon   = $this->getEndLongitude();

        $from = $this->getStreetFrom();
        $to   = $this->getStreetTo();

        $this->setStreetFrom($to);
        $this->setStreetTo($from);

        $this->setStartLatitude($endLat);
        $this->setStartLongitude($endLon);
        $this->setEndLatitude($startLat);
        $this->setEndLongitude($startLon);
	}
}