<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Event;
use Application\Models\EventType;
use Application\Models\GoogleGateway;
use Application\Models\Person;
use Application\Template\Helpers\ButtonLink;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class EventsController extends Controller
{
    /**
     * Creates variables from the searchForm submission
     *
     * @return array
     */
    private function getSearchParameters()
    {
        $defaultRange = ($this->template->outputFormat === 'waze' || $this->template->outputFormat === 'trafficcast')
            ? '+30 days'
            : null;

        if (!empty($_GET['start'])) {
            try { $start = ActiveRecord::parseDate($_GET['start'], DATE_FORMAT); }
            catch (\Exception $e) { }
        }
        if (!empty($_GET['end'])) {
            try { $end = ActiveRecord::parseDate($_GET['end'], DATE_FORMAT); }
            catch (\Exception $e) { }
        }
        if (!isset($start)) { $start = new \DateTime(); }
        if (!isset($end  )) { $end   = new \DateTime($defaultRange); }

        $start->setTime(0,  0);
        $end  ->setTime(23, 59);

        $filters['eventTypes'] = [];
        if (!empty($_GET['eventTypes'])) {
            $filters['eventTypes'] = $_GET['eventTypes'];
        }
        else {
            foreach (EventType::types() as $type) {
                if ($type->isDefaultForSearch()) { $filters['eventTypes'][] = $type->getCode(); }
            }
        }
        return ['start'=>$start, 'end'=>$end, 'filters'=>$filters];
    }

    public function index()
    {
        $search = $this->getSearchParameters();
        $events = GoogleGateway::getEvents(
            GOOGLE_CALENDAR_ID,
            $search['start'],
            $search['end'],
            $search['filters'],
            // Waze and Trafficcase need individual event recurrences
            ($this->template->outputFormat==='waze' || $this->template->outputFormat === 'trafficcast')
        );

        $this->template->title = $this->template->_('application_title');
        $eventListBlock = new Block('events/list.inc', ['events'=>$events]);

        if ($this->template->outputFormat === 'html') {
            $scheduleBlock   = new Block('events/schedule.inc',   ['events'=>$events]);
            $searchFormBlock = new Block('events/searchForm.inc', ['start'=>$search['start'], 'end'=>$search['end'], 'filters'=>$search['filters']]);
            $mapBlock        = new Block('events/map.inc',        ['events'=>$events]);

            $this->template->blocks['headerBar'][] = new Block('events/headerBars/viewToggle.inc');
            $this->template->blocks['panel-one'][] = $searchFormBlock;

            if (!empty($_GET['view']) && $_GET['view'] === 'schedule') {
                $this->template->setFilename('schedule');
                $this->template->blocks[] = $scheduleBlock;
            }
            else {
                $this->template->blocks['panel-two'][] = $eventListBlock;
                $this->template->blocks[]              = $mapBlock;
            }
        }
        else {
            $this->template->blocks[] = $eventListBlock;
        }

    }

    public function view()
    {
        if (!empty($_GET['id'])) {
            try { $event = new Event($_GET['id']); }
            catch (\Exception $e) { }
        }
        if (!isset($event)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        if ($this->template->outputFormat === 'html') {

            $this->template->setFilename('viewSingle');
            $this->template->title = $event->getEventType();

            $this->template->blocks['headerBar'][] = new Block('events/headerBars/viewSingle.inc', ['event'=>$event]);
            $this->template->blocks['panel-one'][] = new Block('events/single.inc',                ['event'=>$event]);
            $this->template->blocks[]              = new Block('events/map.inc',                   ['event'=>$event]);
        }
        else {
            $this->template->blocks[] = new Block('events/single.inc', ['event'=>$event]);
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['id'])) {
            try { $event = new Event($_REQUEST['id']); }
            catch (\Exception $e) { }
        }
        else { $event = new Event(); }

        if (!isset($event)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        if (!$event->permitsEditingBy($_SESSION['USER'])) {
            $_SESSION['errorMessages'][] = new \Exception('noAccessAllowed');
            header('Location: '.BASE_URL.'/events');
            exit();
        }

        if (isset($_POST['id'])) {
            try {
                $event->handleUpdate($_POST);
                $event->save();
                header('Location: '.BASE_URL.'/segments?event_id='.$event->getId());
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        $this->template->setFilename('eventEdit');
        $this->template->title = $event->getId()
            ? $this->template->_('event_edit')
            : $this->template->_('event_add');
        $this->template->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'=>$event]);
        $this->template->blocks['panel-one'][] = new Block('events/updateForm.inc', ['event'=>$event]);
        $this->template->blocks[]              = new Block('events/mapEditor.inc',  ['event'=>$event]);
    }

    public function delete()
    {
        if (!empty($_REQUEST['id'])) {
            try {
                $event = new Event($_REQUEST['id']);
                $event->delete();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }
        header('Location: '.BASE_URL.'/events');
        exit();
    }
}
