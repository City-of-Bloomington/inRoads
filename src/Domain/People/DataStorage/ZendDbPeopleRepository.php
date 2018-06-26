<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\DataStorage;

use Domain\Notifications\Metadata as Notification;
use Domain\People\Entities\Person;
use Domain\People\UseCases\Search\SearchRequest;
use Domain\ZendDbRepository;

use Zend\Db\Sql\Literal;
use Zend\Db\Sql\Select;

class ZendDbPeopleRepository extends ZendDbRepository implements PeopleRepository
{
    const TABLE = 'people';

    public static $DEFAULT_SORT = ['p.lastname', 'p.firstname'];
    /**
     * Maps response fieldnames to the names used in the database
     */
    public static $fieldmap = [
        'id'              => ['prefix'=>'p', 'dbName'=>'id'           ],
        'firstname'       => ['prefix'=>'p', 'dbName'=>'firstname'    ],
        'lastname'        => ['prefix'=>'p', 'dbName'=>'lastname'     ],
        'email'           => ['prefix'=>'p', 'dbName'=>'email'        ],
        'phone'           => ['prefix'=>'p', 'dbName'=>'phone'        ],
        'username'        => ['prefix'=>'p', 'dbName'=>'username'     ],
        'department_id'   => ['prefix'=>'p', 'dbName'=>'department_id'],
        'department_name' => ['prefix'=>'d', 'dbName'=>'name'         ]
    ];

    public function columns(): array
    {
        static $cols = [];
        if (!$cols) {
            foreach (self::$fieldmap as $responseName=>$map) {
                if ($map['prefix'] == 'p') {
                    $cols[$responseName] = $map['dbName'];
                }
            }
        }
        return $cols;
    }

    private function baseSelect(): Select
    {
        $updates   = Notification::TYPE_UPDATES;
        $emergency = Notification::TYPE_EMERGENCY;

        return $this->sql->select()
                         ->columns($this->columns())
                         ->from(['p'=>self::TABLE])
                         ->join(['d'=>'departments'], 'p.department_id=d.id', ['department_name'=>'name'], Select::JOIN_LEFT)
                         ->join(['nu' => 'notificationEmails'],
                                 new Literal("p.email=nu.email and nu.type='$updates'"),
                                 ['notify_updates'=>new Literal('case when nu.email is not null then 1 else 0 end')],
                                 Select::JOIN_LEFT)
                         ->join(['ne' => 'notificationEmails'],
                                 new Literal("p.email=ne.email and ne.type='$emergency'"),
                                 ['notify_emergency'=>new Literal('case when ne.email is not null then 1 else 0 end')],
                                 Select::JOIN_LEFT);
    }

    public function load(int $person_id): Person
    {
        $select = $this->baseSelect();
        $select->where(['p.id' => $person_id]);

        $result = $this->performSelect($select);
        if (count($result['rows'])) {
            return new Person($result['rows'][0]);
        }
        throw new \Exception('person/unknown');
    }


    /**
     * Perform a person query using wildcard matching
     */
    public function search(SearchRequest $req): array
    {
        $select = $this->baseSelect();
        foreach (self::$fieldmap as $f=>$m) {
            if (isset($req->$f)) {
                $column = "$m[prefix].$m[dbName]";
                switch ($f) {
                    case 'user_account':
                        if ($req->$f) {
                            $select->where('p.username is not null');
                        }
                        else {
                            $select->where('p.username is null');
                        }

                    break;
                    case 'p.department_id':
                        $select->where([$column=>$req->$f]);

                    default:
                        $select->where->like($column, $req->$f.'%');
                }
            }
        }
        $select->order(self::$DEFAULT_SORT);
        $result = $this->performSelect($select, $req->itemsPerPage, $req->currentPage);

        $people = [];
        foreach ($result['rows'] as $r) { $people[] = new Person($r); }
        $result['rows'] = $people;
        return $result;
    }

    /**
     * Saves a person and returns the person ID
     */
    public function save(Person $p): int
    {
        $current = $p->id ? $this->load($p->id) : new Person();

        $id = parent::saveToTable([
            'id'              => $p->id,
            'firstname'       => $p->firstname,
            'lastname'        => $p->lastname,
            'email'           => $p->email,
            'phone'           => $p->phone
        ], self::TABLE);

        if ($p->email) {
            if ($current->notify_updates !== $p->notify_updates) {
                $type = Notification::TYPE_UPDATES;
                $sql  = $p->notify_updates
                    ? 'insert into notificationEmails set email=?, type=?'
                    : 'delete from notificationEmails where email=? and type=?';
                $this->zend_db->query($sql, [$p->email, $type]);
            }

            if ($current->notify_emergency !== $p->notify_emergency) {
                $type = Notification::TYPE_EMERGENCY;
                $sql  = $p->notify_emergency
                    ? 'insert into notificationEmails set email=?, type=?'
                    : 'delete from notificationEmails where email=? and type=?';
                $this->zend_db->query($sql, [$p->email, $type]);
            }
        }

        return $id;
    }

    /**
     * Only updates the fields a user can edit about themselves
     */
    public function updateAccount(Person $p): int
    {
        // For now, users can edit all the fields that are available
        // in the Person\UsesCases\Update action
        return $this->save($p);
    }
}
