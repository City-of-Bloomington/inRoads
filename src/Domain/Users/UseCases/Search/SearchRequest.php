<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\UseCases\Search;

class SearchRequest
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $department_id;
    public $username;
    public $role;
    public $authentication_method;

    public $order;
    public $itemsPerPage;
    public $currentPage;

    public function __construct(?array $data=null, ?array $order=null, ?int $itemsPerPage=null, ?int $currentPage=null)
    {
        if ($data) {
            if (!empty($data['id'       ])) { $this->id   = (int)$data['id'       ]; }
            if (!empty($data['firstname'])) { $this->firstname = $data['firstname']; }
            if (!empty($data['lastname' ])) { $this->lastname  = $data['lastname' ]; }
            if (!empty($data['email'    ])) { $this->email     = $data['email'    ]; }
            if (!empty($data['username' ])) { $this->username  = $data['username' ]; }
            if (!empty($data['role'     ])) { $this->role      = $data['role'     ]; }
            if (!empty($data['authentication_method'])) { $this->authentication_method = $data['authentication_method']; }
            if (!empty($data['department_id'])) { $this->department_id = (int)$data['department_id']; }
        }
        if ($order       ) { $this->order        = $order;        }
        if ($itemsPerPage) { $this->itemsPerPage = $itemsPerPage; }
        if ($currentPage ) { $this->currentPage  = $currentPage;  }
    }
}
