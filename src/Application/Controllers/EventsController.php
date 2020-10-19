<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Event;
use Application\Models\EventType;
use Application\Models\GoogleGateway;
use Application\Models\Person;
use Application\Models\PeopleTable;
use Application\Template\Helpers\ButtonLink;

use Application\ActiveRecord;
use Application\Block;
use Application\Controller;
use Application\Database;
use Application\Template;

use Domain\Notifications\Metadata as Notification;

class EventsController extends Controller
{
    const DATE_FORMAT = 'Y-m-d';

    const RANGE_TODAY    = 'today';
    const RANGE_TOMORROW = 'tomorrow';
    const RANGE_WEEK     = 'nextWeek';
    const RANGE_MONTH    = 'nextMonth';

    /**
     * Creates variables from the searchForm submission
     *
     * @param  array Default date ranges to use
     * @return array
     */
    private function getSearchParameters(array $timePeriods): array
    {

        if ($this->template->outputFormat === 'waze' || $this->template->outputFormat === 'trafficcast') {
            $default = self::RANGE_MONTH;
        }
        elseif (isset($_GET['week'])) { $default = self::RANGE_WEEK; }
        elseif (isset($_GET['month'])) { $default = self::RANGE_MONTH; }
        else { $default = self::RANGE_TODAY; }

        if (!empty($_GET['start'])) {
            $start = \DateTime::createFromFormat('!'.self::DATE_FORMAT, $_GET['start']);
        }
        if (!empty($_GET['end'])) {
            $end = \DateTime::createFromFormat('!'.self::DATE_FORMAT, $_GET['end']);
        }
        if (!isset($start) || !$start) { $start = $timePeriods[$default]['start']; }
        if (!isset($end  ) || !$end  ) { $end   = $timePeriods[$default]['end'  ]; }

        $filters['eventTypes'] = [];
        if (!empty($_GET['eventTypes'])) {
            $filters['eventTypes'] = $_GET['eventTypes'];
        }
        else {
            foreach (EventType::types() as $type) {
                if ($type->isDefaultForSearch()) { $filters['eventTypes'][] = $type->getCode(); }
            }
        }
        if (!empty($_GET['department_id'])) { $filters['department_id'] = (int)$_GET['department_id']; }

        return ['start'=>$start, 'end'=>$end, 'filters'=>$filters];
    }

    /**
     * Returns an array of start/end DateTime objects
     *
     * @return array
     */
    private static function timePeriods(): array
    {
        $oneDay   = new \DateInterval('P1D' );
        $oneWeek  = new \DateInterval('P7D' );
        $oneMonth = new \DateInterval('P30D');

        $today_start = new \DateTime(date(self::DATE_FORMAT));
        $today_end   = clone($today_start);
        $today_end->add($oneDay);

        $tomorrow_start = clone($today_start);
        $tomorrow_end   = clone($today_end);
        $tomorrow_start->add($oneDay);
        $tomorrow_end  ->add($oneDay);

        $week_start = clone($today_start);
        $week_end   = clone($today_end);
        $week_end->add($oneWeek);

        $month_start = clone($today_start);
        $month_end   = clone($today_end);
        $month_end->add($oneMonth);

        return [
            self::RANGE_TODAY     => ['start'=>   $today_start, 'end'=>   $today_end],
            self::RANGE_TOMORROW  => ['start'=>$tomorrow_start, 'end'=>$tomorrow_end],
            self::RANGE_WEEK      => ['start'=>    $week_start, 'end'=>    $week_end],
            self::RANGE_MONTH     => ['start'=>   $month_start, 'end'=>   $month_end]
        ];
    }

    public function index(): Template
    {
        $format      = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';
        $timePeriods = self::timePeriods();
        $search      = $this->getSearchParameters($timePeriods);
        $events      = [];
        if (defined('GOOGLE_CALENDAR_ID')) {
            $events = GoogleGateway::getEvents(
                GOOGLE_CALENDAR_ID,
                $search['start'  ],
                $search['end'    ],
                $search['filters'],
                // Waze and Trafficcase need individual event recurrences
                ($format=='waze' || $format=='trafficcast')
            );
        }

        if ($format == 'html') {
            return (!empty($_GET['view']) && $_GET['view'] == 'schedule')
                ? new \Application\Views\Events\ScheduleView($events, $search, $timePeriods)
                : new \Application\Views\Events\MapView     ($events, $search, $timePeriods);
        }

        return new \Application\Views\Events\ListView($events);
    }

    public function view(): Template
    {
        if (!empty($_GET['id'])) {
            try { $event = new Event($_GET['id']); }
            catch (\Exception $e) { }
        }
        return isset($event)
            ? new \Application\Views\Events\SingleView($event)
            : new \Application\Views\NotFoundView();
    }

    public function notify(): Template
    {
        if (!empty($_GET['id'])) {
            try { $event = new Event($_GET['id']); }
            catch (\Exception $e) { }
        }
        if (isset($event)) {
            self::sendNotifications($event, Notification::TYPE_EMERGENCY);
            $_SESSION['errorMessages'] = ['notification/sent'];
            header('Location: '.BASE_URL.'/events/view?id='.$event->getId());
            exit();
        }
        else { return new \Application\Views\NotFoundView(); }
    }

    public function update(): Template
    {
        if (!empty($_REQUEST['id'])) {
            try { $event = new Event($_REQUEST['id']); }
            catch (\Exception $e) { }
        }
        else { $event = new Event(); }

        if (!isset($event)) {
            return new \Application\Views\NotFoundView();
        }

        if (empty($_SESSION['USER']) || !$event->permitsEditingBy($_SESSION['USER'])) {
            return new \Application\Views\ForbiddenView();
        }

        if (isset($_POST['id'])) {
            try {
                $existingEventId = $event->getId();

                // Calls save() automatically
                $event->handleUpdate($_POST);

                if (defined('NOTIFICATIONS_ENABLED') && NOTIFICATIONS_ENABLED) {
                    NotificationsController::sendNotifications($event, Notification::TYPE_UPDATES);
                }

                $url = $existingEventId
                    ? BASE_URL.'/events/view?id='   .$event->getId()
                    : BASE_URL.'/segments?event_id='.$event->getId();
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        return new \Application\Views\Events\UpdateView($event);
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

    public function history(): Template
    {
        if (!empty($_GET['id'])) {
            try { $event = new Event($_GET['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        return isset($event)
            ? new \Application\Views\Events\HistoryView($event->getHistory())
            : new \Application\Views\NotFoundView();
    }
}
