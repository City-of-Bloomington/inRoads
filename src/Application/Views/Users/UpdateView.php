<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Users;

use Application\Block;
use Application\Template;

use Domain\Users\Entities\User;
use Domain\Users\Metadata;
use Domain\Users\UseCases\Update\UpdateRequest;
use Domain\Users\UseCases\Update\UpdateResponse;

class UpdateView extends Template
{
    public function __construct(UpdateRequest $request, ?UpdateResponse $response, Metadata $user)
    {
        parent::__construct('admin', 'html');

        if ($response && count($response->errors)) {
            $_SESSION['errorMessages'] = $response->errors;
        }

        $this->vars['title'] = $request->id ? $this->_('user_edit') : $this->_('user_add');

        $this->blocks[] = new Block('users/updateForm.inc', [
            'id'        => $request->id,
            'username'            => parent::escape($request->username),
            'password'            => parent::escape($request->password),
            'department_id'       => parent::escape($request->department_id),
            'role'                => parent::escape($request->role),
            'authenticationMethod'=> parent::escape($request->authenticationMethod),

            'firstname' => parent::escape($request->firstname),
            'lastname'  => parent::escape($request->lastname),
            'email'     => parent::escape($request->email),

            'title'       => $this->vars['title'],
            'departments' => $user->departments()
        ]);
    }
}
