<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Info;

use Domain\People\Entities\Person;

class InfoResponse
{
    public $person;
    public $errors = [];

    public function __construct(Person $person=null, ?array $errors=null)
    {
        if ($person) { $this->person = $person; }
        if ($errors) { $this->errors = $errors; }
    }
}
