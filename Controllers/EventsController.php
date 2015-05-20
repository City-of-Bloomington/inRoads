<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
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
                $this->template->setFlashMessages($e, 'errorMessages');
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
        $table = new EventsTable();
        $list = $table->find();

        $this->template->blocks['panel-one'][] = new Block('events/list.inc', ['events'=>$list]);
        $this->template->blocks[]              = new Block('events/map.inc',  ['events'=>$list]);
    }

    public function view()
    {
        $this->template->setFilename('full-width');

        $event = $this->loadEvent($_GET['event_id']);
        $this->template->blocks[] = new Block('events/info.inc', ['event'=>$event]);
    }

    public function update()
    {
        $this->template->setFilename('full-width');

        $event =        !empty($_REQUEST['event_id'])
            ? $this->loadEvent($_REQUEST['event_id'])
            : new Event();

        if (isset($_POST['event_id'])) {
            $event->handleUpdate($_POST);
            $errors = $event->save();
            if (!count($errors)) {
                header('Location: '.BASE_URL.'/events/view?event_id='.$event->getId());
                exit();
            }
            else {
                $this->template->setFlashMessages($e, 'errorMessages');
            }
        }
        $this->template->blocks[] = new Block('events/updateForm.inc', ['event'=>$event]);
    }
}