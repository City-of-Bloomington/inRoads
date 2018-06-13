<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Events;

use Application\Models\Event;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

class UpdateView extends Template
{
    public function __construct(Event $event)
    {
        parent::__construct('default', 'html');

        $this->setFilename('eventEdit');
        $this->title = $event->getId()
            ? $this->_('event_edit')
            : $this->_('event_add');
        $this->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'=>$event]);
        $this->blocks['panel-one'][] = new Block('events/updateForm.inc',        ['event'=>$event]);
        $this->blocks[]              = new Block('events/mapEditor.inc',         ['event'=>$event]);
    }
}
