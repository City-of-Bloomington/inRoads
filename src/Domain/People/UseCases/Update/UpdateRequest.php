<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Update;

class UpdateRequest
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;

    public function __construct(?array $data=null)
    {
        if ($data) {
            if (!empty($data['id'        ])) { $this->id         = (int)$data['id'   ]; }
            if (!empty($data['firstname' ])) { $this->firstname  = $data['firstname' ]; }
            if (!empty($data['lastname'  ])) { $this->lastname   = $data['lastname'  ]; }
            if (!empty($data['email'     ])) { $this->email      = $data['email'     ]; }
            if (!empty($data['phone'     ])) { $this->phone      = $data['phone'     ]; }
        }
    }
}
