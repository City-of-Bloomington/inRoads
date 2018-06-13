<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Events;

use Blossom\Classes\Block;
use Blossom\Classes\Template;


class MapView extends Template
{
    /**
     * @param array $events      An array of Event Objects to display
     * @param array $search      The search params that were used to generate the events
     * @param array $timePeriods The default set of predefined date ranges
     */
    public function __construct(array $events, array $search, array $timePeriods)
    {
        parent::__construct('default', 'html');
        $this->title = $this->_('application_title');

        $this->blocks['headerBar'][] = new Block('events/headerBars/viewToggle.inc');
        $this->blocks['panel-one'][] = new Block('events/searchForm.inc', [
            'start'   => $search['start'  ],
            'end'     => $search['end'    ],
            'filters' => $search['filters'],
            'presets' => $timePeriods
        ]);
        $this->blocks['panel-two'][] = new Block('events/list.inc', ['events'=>$events]);
        $this->blocks[]              = new Block('events/map.inc',  ['events'=>$events]);
    }
}
