<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class EventType extends ActiveRecord
{
	protected $tablename = 'eventTypes';

    /**
     * @return array  An array of EventType objects
     */
    public static function types()
    {
        $table = new EventTypesTable();
        $list = $table->find();
        return $list;
    }

    /**
     * @return array An array of name strings
     */
    public static function names()
    {
        $names = [];

        $sql = 'select name from eventTypes order by name';
        $zend_db = Database::getConnection();
        $result = $zend_db->createStatement($sql)->execute();

        foreach ($result as $row) {
            $names[] = $row['name'];
        }
        return $names;
    }

    /**
     * @return array An array of code strings
     */
    public static function codes()
    {
        $codes = [];

        $sql = 'select code from eventTypes order by code';
        $zend_db = Database::getConnection();
        $result = $zend_db->createStatement($sql)->execute();

        foreach ($result as $row) {
            $codes[] = $row['code'];
        }
        return $codes;
    }
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
                $sql = ActiveRecord::isId($id)
					? 'select * from eventTypes where id=?'
					: 'select * from eventTypes where code=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('eventTypes/unknown');
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
    }

    public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId   () { return parent::get('id'   ); }
	public function getCode () { return parent::get('code' ); }
	public function getName () { return parent::get('name' ); }
	public function getColor() { return parent::get('color'); }
	public function getDescription() { return parent::get('description'); }

	public function setCode ($s) { parent::set('code',  $s); }
	public function setName ($s) { parent::set('name',  $s); }
	public function setDescription($s) { parent::set('description', $s); }

	/**
	 * Sets the user-entered hex value for the color
	 *
	 * @param string $hex
	 */
	public function setColor($hex)
	{
        $hex = preg_replace('/[^0-9a-f]/', '', strtolower($hex));
        if (strlen($hex) === 6) {
            parent::set('color', $hex);
        }
    }

	public function getIsDefault()   { return parent::get('isDefault'); }
	public function setIsDefault($b) { parent::set('isDefault', $b ? 1 : 0); }

	public function handleUpdate($post)
	{
        $fields = ['code', 'name', 'color', 'description', 'isDefault'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	public function isDefault() { return parent::get('isDefault') ? true : false; }
    public function isDefaultForSearch() { return $this->isDefault(); }
}
