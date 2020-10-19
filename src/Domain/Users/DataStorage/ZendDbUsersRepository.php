<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\DataStorage;

use Domain\ZendDbRepository;
use Domain\Users\Entities\User;
use Domain\Users\UseCases\Search\SearchRequest;

use Laminas\Db\Sql\Select;

class ZendDbUsersRepository extends ZendDbRepository implements UsersRepository
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
        'department_id'   => ['prefix'=>'p', 'dbName'=>'department_id'],
        'department_name' => ['prefix'=>'d', 'dbName'=>'name'         ],
        'username'        => ['prefix'=>'p', 'dbName'=>'username'     ],
        'role'            => ['prefix'=>'p', 'dbName'=>'role'         ],
        'authenticationMethod' => ['prefix'=>'p', 'dbName'=>'authenticationMethod']
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
        return $this->sql->select()
                         ->columns($this->columns())
                         ->from(['p'=>self::TABLE])
                         ->join(['d'=>'departments'], 'p.department_id=d.id', ['department_name'=>'name'], Select::JOIN_LEFT)
                         ->where('p.username is not null');
    }
    private function loadByKey(string $key, $value): ?User
    {
        $select = $this->baseSelect();
        $select->where([$key=>$value]);
        $result = $this->performSelect($select);
        if ( count($result['rows'])) {
            return new User($result['rows'][0]);
        }
    }
    public function loadById      (int    $id      ): ?User { return $this->loadByKey('p.id',       $id); }
    public function loadByUsername(string $username): ?User { return $this->loadByKey('p.username', $username); }
    /**
     * Perform a person query with exact matching
     */
    public function find(SearchRequest $req): array
    {
        $select = $this->baseSelect();
        foreach (self::$fieldmap as $f=>$m) {
            if (isset($req->$f)) {
                $column = "$m[prefix].$m[dbName]";
                $select->where([$column=>$req->$f]);
            }
        }
        $select->order(self::$DEFAULT_SORT);
        $result = $this->performSelect($select, $req->itemsPerPage, $req->currentPage);

        $users = [];
        foreach ($result['rows'] as $r) { $users[] = new User($r); }
        $result['rows'] = $users;
        return $result;
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
                    case 'p.department_id':
                        $select->where([$column=>$req->$f]);
                    break;

                    default:
                        $select->where->like($column, $req->$f.'%');
                }
            }
        }
        $select->order(self::$DEFAULT_SORT);
        $result = $this->performSelect($select, $req->itemsPerPage, $req->currentPage);

        $users = [];
        foreach ($result['rows'] as $r) { $users[] = new User($r); }
        $result['rows'] = $users;
        return $result;
    }

    /**
     * Saves a person and returns the person ID
     */
    public function save(User $u): int
    {
        return parent::saveToTable([
            'id'                   => $u->id,
            'firstname'            => $u->firstname,
            'lastname'             => $u->lastname,
            'email'                => $u->email,
            'phone'                => $u->phone,
            'department_id'        => $u->department_id,
            'username'             => $u->username,
            'role'                 => $u->role,
            'authenticationMethod' => $u->authenticationMethod

        ], self::TABLE);
    }

    public function delete(int $id)
    {
        $sql = 'update '.self::TABLE."
                set username=null, password=null, role=null, authenticationMethod=null
                where id=?";
        $this->zend_db->query($sql, [$id]);
    }

    public function departments(): array
    {
        $depts  = [];
        $sql    = 'select id, name from departments order by name';
        $result = $this->zend_db->query($sql)->execute();
        foreach ($result as $row) { $depts[] = $row; }
        return $depts;
    }
}
