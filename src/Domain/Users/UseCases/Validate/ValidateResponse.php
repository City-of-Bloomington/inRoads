<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\UseCases\Validate;

use Domain\Users\Entities\User;

class ValidateResponse
{
    public $user;
    public $errors = [];

    public function __construct(User $user, ?array $errors=null)
    {
        $this->user = $user;
        if ($errors) { $this->errors = $errors; }
    }
}
