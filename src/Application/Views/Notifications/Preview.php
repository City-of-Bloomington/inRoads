<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Notifications;

use Application\Models\Event;
use Application\Block;
use Application\Template;

class Preview extends Template
{
    public function __construct(Event $event, string $type)
    {
        parent::__construct('admin', 'html');

        $this->blocks[] = new Block('notifications/sendForm.inc', [
            'event' => $event,
            'type'  => $type
        ]);
    }
}
