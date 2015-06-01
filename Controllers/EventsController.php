<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Event;
use Application\Models\GoogleGateway;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class EventsController extends Controller
{
    private function loadEvent($id)
    {
        if ($id) {
            $event = GoogleGateway::getEvent(GOOGLE_CALENDAR_ID, $id);
            if ($event) {
                return new Event($event);
            }
            else {
                $this->template->setFlashMessages( ['event' => [0 => ['unknown']]], 'errorMessages' );
                header('Location: '.BASE_URL.'/events');
                exit();
            }
        }
        else {
            $this->template->setFlashMessages( ['event' => [0 => ['unknown']]], 'errorMessages' );
            header('Location: '.BASE_URL.'/events');
            exit();
        }
    }

    public function index()
    {
        $start = new \DateTime();
        $list = GoogleGateway::getEvents(GOOGLE_CALENDAR_ID, $start);

        $events = [];
        foreach ($list as $event) {
            $events[] = new Event($event);
        }

        $this->template->blocks['panel-one'][] = new Block('events/list.inc', ['events'=>$events]);
        $this->template->blocks[]              = new Block('events/map.inc',  ['events'=>$events]);
    }

    public function view()
    {
        $this->template->setFilename('full-width');

        $event = $this->loadEvent($_GET['id']);
        $this->template->blocks[] = new Block('events/info.inc', ['event'=>$event]);
    }

    public function update()
    {
        $this->template->setFilename('full-width');

        $event =        !empty($_REQUEST['id'])
            ? $this->loadEvent($_REQUEST['id'])
            : new Event();

        if (isset($_POST['id'])) {
            $event->handleUpdate($_POST);
            $errors = $event->save();
            if (!count($errors)) {
                header('Location: '.BASE_URL.'/events/view?id='.$event->getId());
                exit();
            }
            else {
                $this->template->setFlashMessages($e, 'errorMessages');
            }
        }
        $this->template->blocks[] = new Block('events/updateForm.inc', ['event'=>$event]);
    }
}