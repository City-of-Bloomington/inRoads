<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\People;

use Application\Block;
use Application\Template;

use Domain\People\Entities\Person;
use Domain\People\UseCases\Update\UpdateRequest;
use Domain\People\UseCases\Update\UpdateResponse;

class UpdateView extends Template
{
    public function __construct(UpdateRequest $request, ?UpdateResponse $response)
    {
        parent::__construct('admin', 'html');

        if ($response && count($response->errors)) {
            $_SESSION['errorMessages'] = $response->errors;
        }

        $this->vars['title'] = $request->id ? $this->_('person_edit') : $this->_('person_add');

        $this->blocks[] = new Block('people/updateForm.inc', [
            'id'        => $request->id,
            'firstname' => parent::escape($request->firstname),
            'lastname'  => parent::escape($request->lastname),
            'email'     => parent::escape($request->email),
            'phone'     => parent::escape($request->phone),
            'notify_updates'   => $request->notify_updates,
            'notify_emergency' => $request->notify_emergency,

            'title'  => $this->vars['title']
        ]);
    }
}
