<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Notifications;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

use Domain\Notifications\UseCases\Update\UpdateRequest;
use Domain\Notifications\UseCases\Update\UpdateResponse;

class UpdateView extends Template
{
    public function __construct(UpdateRequest $req, ?UpdateResponse $res=null)
    {
        parent::__construct('admin', 'html');
        if ($res && $res->errors) {
            $_SESSION['errorMessages'] = $res->errors;
        }

        $this->vars['title'] = $req->id ? $this->_('notification_edit') : $this->_('notification_add');

        $this->blocks[] = new Block('notifications/updateForm.inc', [
            'id'    => $req->id,
            'type'  => $req->type,
            'email' => $req->email
        ]);
    }
}
