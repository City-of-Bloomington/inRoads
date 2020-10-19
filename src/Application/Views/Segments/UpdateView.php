<?php
/**
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Views\Segments;

use Application\Block;
use Application\Template;

use Application\Models\Segment;

class UpdateView extends Template
{
    public function __construct(Segment $segment,
                                ?int    $street_id    = null,
                                ?array  $crossStreets = null)
    {
        parent::__construct('eventEdit', 'html');

        $this->vars['title'] = $segment->getId()
                             ? $this->_('segment_edit')
                             : $this->_('segment_add');

        $vars = [
            'title'        => $this->vars['title'],
            'event_id'     => $segment->getEvent_id(),
            'segment_id'   => $segment->getId(),
            'directions'   => Segment::$directions,
            'street_id'    => $street_id,
            'crossStreets' => $crossStreets

        ];
        foreach (Segment::$fields as $f) {
            $get = 'get'.ucfirst($f);
            $vars[$f] = parent::escape($segment->$get());
        }

        $event = $segment->getEvent();
        $this->blocks = [
            'headerBar' => [
                new Block('events/headerBars/update.inc', ['event'=>$event])
            ],
            'panel-one' => [
                new Block('segments/updateForm.inc', $vars),
                new Block('segments/list.inc', [
                    'event'    => $event,
                    'segments' => $event->getSegments(),
                    'disableButtons' => true
                ])
            ],
            new Block('events/map.inc', ['event'=>$event])
        ];
    }
}
