<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\UseCases\Validate;

use Domain\Users\Entities\User;

class Validate
{
    public function __invoke(User $user): ValidateResponse
    {
        $errors = [];
        $requiredFields = ['firstname', 'lastname', 'email', 'username'];
        foreach ($requiredFields as $f) {
            if (empty($user->$f)) {
                $errors[] = "users/missing_$f";
            }
        }
        if (empty($user->role                 )) { $user->role                  = 'Public'; }
        if (empty($user->authentication_method)) { $user->authentication_method = 'local';  }

        return new ValidateResponse($user, $errors);
    }
}
