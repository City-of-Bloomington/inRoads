<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Event;
use Application\Models\EventType;
use Application\Models\GoogleGateway;
use Application\Models\Notifications;
use Application\Models\Person;
use Application\Models\PeopleTable;
use Application\Template\Helpers\ButtonLink;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

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
    private function getSearchParameters(array $timePeriods)
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

    public function index()
    {
        $timePeriods = self::timePeriods();
        $search      = $this->getSearchParameters($timePeriods);
        $events = GoogleGateway::getEvents(
            GOOGLE_CALENDAR_ID,
            $search['start'  ],
            $search['end'    ],
            $search['filters'],
            // Waze and Trafficcase need individual event recurrences
            ($this->template->outputFormat==='waze' || $this->template->outputFormat === 'trafficcast')
        );

        $this->template->title = $this->template->_('application_title');
        $eventListBlock = new Block('events/list.inc', ['events'=>$events]);

        if ($this->template->outputFormat === 'html') {
            $mapBlock        = new Block('events/map.inc',        ['events'=>$events]);
            $scheduleBlock   = new Block('events/schedule.inc',   ['events'=>$events]);
            $searchFormBlock = new Block('events/searchForm.inc', [
                'start'   => $search['start'  ],
                'end'     => $search['end'    ],
                'filters' => $search['filters'],
                'presets' => $timePeriods
            ]);

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
        return $this->template;
    }

    public function view()
    {
        if (!empty($_GET['id'])) {
            try { $event = new Event($_GET['id']); }
            catch (\Exception $e) { }
        }
        if (!isset($event)) {
            return new \Application\Views\NotFoundView();
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
        return $this->template;
    }

    public function update()
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
                    self::sendNotifications($event, Notifications::emailAddresses(Notifications::TYPE_UPDATES));
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

        $this->template->setFilename('eventEdit');
        $this->template->title = $event->getId()
            ? $this->template->_('event_edit')
            : $this->template->_('event_add');
        $this->template->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'=>$event]);
        $this->template->blocks['panel-one'][] = new Block('events/updateForm.inc', ['event'=>$event]);
        $this->template->blocks[]              = new Block('events/mapEditor.inc',  ['event'=>$event]);
        return $this->template;
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

    public function history()
    {
        if (!empty($_GET['id'])) {
            try { $event = new Event($_GET['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($event)) {
            $this->template->setFilename('admin');
            $this->template->blocks[] = new Block('events/history.inc', ['history' => $event->getHistory()]);
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
    }

	public static function sendNotifications(Event $event, array $emailAddresses)
	{
        $template     = new Template('default', 'txt');
        $block        = new Block('events/notification.inc', ['event'=>$event]);

        $message      = $block->render('txt', $template);
        $subject      = sprintf($template->_('notification_subject %s', 'messages'), APPLICATION_NAME);
        $name         = preg_replace('/[^a-zA-Z0-9]+/','_',APPLICATION_NAME);
        $fromEmail    = "$name@". BASE_HOST;
        $fromFullname = APPLICATION_NAME;

        foreach ($emailAddresses as $to) {
            $from = "From: $fromFullname <$fromEmail>\r\nReply-to: ".ADMINISTRATOR_EMAIL;
            mail($to, $subject, $message, $from, '-f'.ADMINISTRATOR_EMAIL);
        }
	}
}
