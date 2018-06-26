<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\UpdateAccount;

class UpdateAccountRequest
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $notify_updates   = false;
    public $notify_emergency = false;

    public function __construct(int $id, array $data=null)
    {
        $this->id = $id;

        if ($data) {
            if (!empty($data['firstname'])) { $this->firstname = $data['firstname']; }
            if (!empty($data['lastname' ])) { $this->lastname  = $data['lastname' ]; }
            if (!empty($data['email'    ])) { $this->email     = $data['email'    ]; }
            if (!empty($data['phone'    ])) { $this->phone     = $data['phone'    ]; }
            if (!empty($data['notify_updates'  ])) { $this->notify_updates   = $data['notify_updates'  ] ? true : false; }
            if (!empty($data['notify_emergency'])) { $this->notify_emergency = $data['notify_emergency'] ? true : false; }
        }
    }
}
