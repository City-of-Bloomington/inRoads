<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Event;
use Application\Models\EventsTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class EventsController extends Controller
{
    private function loadEvent($id)
    {
        if ($id) {
            try {
                return new Event($id);
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
                header('Location: '.BASE_URL.'/events');
                exit();
            }
        }
        else {
            $_SESSION['errorMessages'][] = new \Exception('events/unknownEvent');
            header('Location: '.BASE_URL.'/events');
            exit();
        }
    }

    public function index()
    {
        $table = new EventsTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('events/list.inc', ['events'=>$list]);
    }

    public function view()
    {
        $event = $this->loadEvent($_GET['event_id']);
        $this->template->blocks[] = new Block('events/info.inc', ['event'=>$event]);
    }

    public function update()
    {
        $event = !empty($_REQUEST['event_id'])
            ? $this->loadEvent($_REQUEST['event_id'])
            : new Event();

        if (isset($_POST['jurisdiction_id'])) {
            $event->handleUpdate($_POST);
            try {
                $event->save();
                header('Location: '.BASE_URL.'/events/view?event_id='.$event->getId());
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }
        $this->template->blocks[] = new Block('events/updateForm.inc', ['event'=>$event]);
    }
}