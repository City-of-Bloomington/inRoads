<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Account;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

use Domain\People\UseCases\UpdateAccount\UpdateAccountRequest;
use Domain\People\UseCases\UpdateAccount\UpdateAccountResponse;

class UpdateView extends Template
{
    public function __construct(UpdateAccountRequest $request, ?UpdateAccountResponse $response)
    {
        parent::__construct('admin', 'html');

        if ($response && count($response->errors)) {
            $_SESSION['errorMessages'] = $response->errors;
        }

        $this->vars['title'] = $this->_('account');

        $this->blocks[] = new Block('account/updateForm.inc', [
            'firstname' => parent::escape($request->firstname),
            'lastname'  => parent::escape($request->lastname),
            'email'     => parent::escape($request->email),
            'phone'     => parent::escape($request->phone),

            'title'  => $this->vars['title']
        ]);
    }
}
