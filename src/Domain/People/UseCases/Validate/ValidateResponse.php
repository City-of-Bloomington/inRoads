<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Validate;

use Domain\People\Entities\Person;

class ValidateResponse
{
    public $person;
    public $errors = [];

    public function __construct(Person $person, ?array $errors=null)
    {
        $this->person = $person;
        if ($errors) { $this->errors = $errors; }
    }
}
