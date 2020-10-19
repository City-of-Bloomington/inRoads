<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Events;

use Application\Models\Event;

use Application\Block;
use Application\Template;

class SingleView extends Template
{
    public function __construct(Event $event)
    {
        $format = !empty($_GET['format']) ? $_GET['format'] : 'html';

        if ($format == 'html') {
            parent::__construct('viewSingle', 'html');

            $this->title = $event->getEventType();
            $this->blocks['headerBar'][] = new Block('events/headerBars/viewSingle.inc', ['event'=>$event]);
            $this->blocks['panel-one'][] = new Block('events/single.inc',                ['event'=>$event]);
            $this->blocks[]              = new Block('events/map.inc',                   ['event'=>$event]);
        }
        else {
            parent::__construct('default', $format);
            $this->blocks[] = new Block('events/single.inc', ['event'=>$event]);
        }
    }
}
