<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\EventTypesTable;
use Application\Models\GoogleGateway;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class TrafficcastController extends Controller
{
    const DATE_RANGE = '+30 days';

    public function __construct(Template &$template)
    {
        parent::__construct($template);

        $this->template->setOutputFormat('waze');
    }

    /**
     * Creates variables from the searchForm submission
     *
     * @return array
     */
    private function getSearchParameters()
    {
        $start = new \DateTime();
        $start->setTime(0,  0);

        $end   = new \DateTime(self::DATE_RANGE);
        $end->setTime(23, 59);

        $filters['eventTypes'] = [];
        $table = new EventTypesTable();
        $list  = $table->find(['cifs'=>true]);
        foreach ($list as $type) {
            if ($type->isDefaultForSearch()) { $filters['eventTypes'][] = $type->getCode(); }
        }
        return ['start'=>$start, 'end'=>$end, 'filters'=>$filters];
    }

    public function index()
    {
        $search = $this->getSearchParameters();
        $events = [];
        if (defined('GOOGLE_CALENDAR_ID')) {
            $events = GoogleGateway::getEvents(
                GOOGLE_CALENDAR_ID,
                $search['start'],
                $search['end'],
                $search['filters']
            );
        }

        $this->template->setOutputFormat('waze');
        $this->template->blocks[] = new Block('events/list.inc', ['events'=>$events]);
        return $this->template;
    }
}
