<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Jurisdiction extends ActiveRecord
{
    protected $tablename = 'jurisdictions';

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
                $sql = 'select * from jurisdictions where id=?';
                $result = $zend_db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('jurisdiction/unknown');
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
        if (!$this->getName()) { $errors['name'][] = 'missingRequiredFields'; }
        if (!$this->getDomain()) { $errors['domain'][] = 'missingRequiredFields'; }
        if (!$this->getEmail()) { $errors['email'][] = 'missingRequiredFields'; }

        if (count($errors)) {
            return ['jurisdiction' => $errors];
        }

    }

    /**
     * @return array Errors
     */
    public function save() { return parent::save(); }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()          { return parent::get('id');          }
    public function getName()        { return parent::get('name');        }
    public function getDomain()      { return parent::get('domain');      }
    public function getEmail()       { return parent::get('email');       }
    public function getPhone()       { return parent::get('phone');       }
    public function getDescription() { return parent::get('description'); }

    public function setName       ($s) { parent::set('name',        $s); }
    public function setDomain     ($s) { parent::set('domain',      $s); }
    public function setEmail      ($s) { parent::set('email',       $s); }
    public function setPhone      ($s) { parent::set('phone',       $s); }
    public function setDescription($s) { parent::set('description', $s); }

    public function handleUpdate($post)
    {
        $fields = ['name', 'domain', 'email', 'phone', 'description'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
    }
}