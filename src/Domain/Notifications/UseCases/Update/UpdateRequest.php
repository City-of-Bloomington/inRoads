<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Update;

class UpdateRequest
{
    public $id;
    public $type;
    public $email;

    public function __construct(?array $data=null)
    {
        if ($data) {
            if (!empty($data['id'   ])) { $this->id    = (int)$data['id'   ]; }
            if (!empty($data['type' ])) { $this->type  =      $data['type' ]; }
            if (!empty($data['email'])) { $this->email =      $data['email']; }
        }
    }
}
