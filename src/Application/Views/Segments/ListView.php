<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Views\Segments;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

use Application\Models\Event;

class ListView extends Template
{
    public function __construct(Event $event)
    {
        parent::__construct('eventEdit', 'html');
        $this->vars['title'] = $event->getEventType();

        $this->blocks = [
            'headerBar' => [
                new Block('events/headerBars/update.inc', ['event'=>$event])
            ],
            'panel-one' => [
                new Block('segments/list.inc', [
                    'event'    => $event,
                    'segments' => $event->getSegments()
                ])
            ],
            new Block('events/map.inc', ['event'=>$event])
        ];
    }
}
