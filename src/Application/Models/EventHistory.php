<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class EventHistory extends ActiveRecord
{
    const STATE_ORIGINAL = 'original';
    const STATE_UPDATED  = 'updated';

    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';

    public static $states = [self::STATE_ORIGINAL, self::STATE_UPDATED];

	protected $tablename = 'eventHistory';

	protected $event;
	protected $person;

	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = "select * from {$this->tablename} where id=?";

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception("{$this->tablename}/unknown");
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['action_date'] = new \DateTime();
			$this->setPerson_id($_SESSION['USER']->id);
		}
	}

	/**
	 * @param array $row  Row of data from the database
	 */
	public function exchangeArray($row)
	{
        $row['action_date'] = new \DateTime($row['action_date']);
        $this->data = $row;
	}

	public function validate()
	{
        $requiredFields = ['action', 'event_id', 'person_id', 'changes'];
        foreach ($requiredFields as $f) {
            $field = ucfirst($f);
            $get   = 'get'.$field;
            if (!$this->$get()) {
                throw new \Exception("{$this->tablename}/missing$field");
            }
        }
	}

	public function save()
	{
        // Let MySQL generate the actual timestamp
        unset($this->data['action_date']);

        parent::save();
    }

    public static function saveNewEntry(int $event_id, string $action, array $changes)
    {
        $h = new EventHistory();
        $h->setEvent_id($event_id);
        $h->setAction  ($action  );
        $h->setChanges ($changes );
        $h->save();
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()          { return parent::get('id'         ); }
	public function getAction()      { return parent::get('action'     ); }
	public function getEvent_id()    { return parent::get('event_id'   ); }
	public function getPerson_id()   { return parent::get('person_id'  ); }
	public function getAction_date() { return parent::get('action_date'); }
	public function getEvent()       { return parent::getForeignKeyObject(__namespace__.'\Event',  'event_id' ); }
	public function getPerson()      { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id'); }
	public function getChanges() { return json_decode(parent::get('changes'), true); }

	public function setAction   ($s) { parent::set('action', $s); }
	public function setEvent_id ($i) { parent::setForeignKeyField (__namespace__.'\Event',  'event_id',  $i); }
	public function setPerson_id($i) { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $i); }
	public function setEvent    ($o) { parent::setForeignKeyObject(__namespace__.'\Event',  'event_id',  $o); }
	public function setPerson   ($o) { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $o); }
	public function setChanges   (array     $d=null) { parent::set('changes', json_encode($d)); }
}
