<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Department extends ActiveRecord
{
	protected $tablename = 'departments';

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
				if (ActiveRecord::isId($id)) {
					$sql = 'select * from departments where id=?';
				}
				else {
					$sql = 'select * from departments where code=?';
				}
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('departments/unknown');
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
        $errors = [];

		if (!$this->getCode()) { $errors['code'][] = 'missingRequiredField'; }
		if (!$this->getName()) { $errors['name'][] = 'missingRequiredField'; }

		if (count($errors)) {
            return ['departments' => $errors];
		}
    }

    public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId   () { return parent::get('id'   ); }
	public function getCode () { return parent::get('code' ); }
	public function getName () { return parent::get('name' ); }
	public function getPhone() { return parent::get('phone'); }

	public function setCode ($s) { parent::set('code',  $s); }
	public function setName ($s) { parent::set('name',  $s); }
	public function setPhone($s) { parent::set('phone', $s); }

	public function handleUpdate(array $post)
	{
        $this->setCode ($post['code' ]);
        $this->setName ($post['name' ]);
        $this->setPhone($post['phone']);
	}

	//----------------------------------------------------------------
	// Custom functions
    //----------------------------------------------------------------
    public function __toString() { return $this->getName(); }
    
    /**
     * @return array
     */
    public static function codes()
    {
        $out = [];

        $zend_db = Database::getConnection();
        $result = $zend_db->query('select code from departments order by code')->execute();
        foreach ($result as $row) {
            $out[] = $row['code'];
        }
        return $out;
    }
}