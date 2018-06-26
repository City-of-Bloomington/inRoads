<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\People;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

use Domain\People\UseCases\Info\InfoResponse;

class InfoView extends Template
{
    public function __construct(InfoResponse $response)
    {
        parent::__construct('admin', 'html');

        if (count($response->errors)) {
            $_SESSION['errorMessages'] = $response->errors;
        }
        $person = $response->person;

        $this->vars['title'] = parent::escape($person->fullname());
        $vars = [
            'id'         => $person->id,
            'name'       => parent::escape($person->fullname()),
            'username'   => parent::escape($person->username),
            'department' => parent::escape($person->department_name),
            'email'      => parent::escape($person->email),
            'phone'      => parent::escape($person->phone),
            'notify_updates'   => $person->notify_updates,
            'notify_emergency' => $person->notify_emergency
        ];
		$this->blocks[] = new Block('people/info.inc', $vars);
    }
}
