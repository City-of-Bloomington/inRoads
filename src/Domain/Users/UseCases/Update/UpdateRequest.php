<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\UseCases\Update;

class UpdateRequest
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $department_id;

    public $username;
    public $role;
    public $password;
    public $authenticationMethod;

    public function __construct(?array $data=null)
    {
        if ($data) {
            if (!empty($data['id'       ])) { $this->id        = (int)$data['id'  ]; }
            if (!empty($data['firstname'])) { $this->firstname = $data['firstname']; }
            if (!empty($data['lastname' ])) { $this->lastname  = $data['lastname' ]; }
            if (!empty($data['email'    ])) { $this->email     = $data['email'    ]; }
            if (!empty($data['department_id'])) { $this->department_id = (int)$data['department_id']; }

            if (!empty($data['username' ])) { $this->username  = $data['username' ]; }
            if (!empty($data['password' ])) { $this->password  = $data['password' ]; }
            if (!empty($data['role'     ])) { $this->role      = $data['role'     ]; }
            if (!empty($data['authenticationMethod'])) { $this->authenticationMethod = $data['authenticationMethod']; }
        }
    }
}
