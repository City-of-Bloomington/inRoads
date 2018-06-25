<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Auth;

class ExternalIdentity
{
    public $username;
    public $firstname;
    public $lastname;
    public $email;

    public function __construct(?array $data)
    {
        if ($data) {
            if (!empty($data['username' ])) { $this->username  = $data['username' ]; }
            if (!empty($data['firstname'])) { $this->firstname = $data['firstname']; }
            if (!empty($data['lastname' ])) { $this->lastname  = $data['lastname' ]; }
            if (!empty($data['email'    ])) { $this->email     = $data['email'    ]; }
        }
    }
}
