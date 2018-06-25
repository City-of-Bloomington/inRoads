<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Validate;

use Domain\People\Entities\Person;

class Validate
{
    public function __invoke(Person $person): ValidateResponse
    {
        $errors = [];
        if (empty($person->firstname) || empty($person->lastname)) {
            $errors[] = 'missingRequiredFields';
        }
        return new ValidateResponse($person, $errors);
    }
}
