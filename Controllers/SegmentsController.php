<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\AddressService;
use Application\Models\Event;
use Application\Models\Person;
use Application\Models\Segment;
use Application\Models\SegmentsTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class SegmentsController extends Controller
{
    /**
     * This is the screen for working with Segments on events
     *
     * If you're allowed to edit segments, you will see a search form.
     * This is the starting point.  You must search for a valid street.
     * When you have chosen a valid street, you will be redirected to
     * the actual Segment Update screen.
     */
    public function index()
    {
        if (!empty($_GET['event_id'])) {
            try { $event = new Event($_GET['event_id']); }
            catch (\Exception $e) { }
        }
        if (!isset($event)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        $this->template->setFilename('eventEdit');
        $this->template->title = $event->getEventType();

        if (Person::isAllowed('segments', 'update')) {
            $segment = new Segment();
            $segment->setEvent($event);

            $this->template->blocks['panel-one'][] = new Block('segments/searchForm.inc', ['segment'=>$segment]);
        }

        $this->template->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'=>$event]);
        $this->template->blocks['panel-one'][] = new Block('segments/list.inc', ['segments'=>$event->getSegments()]);
        $this->template->blocks[]              = new Block('events/map.inc',               ['event'=>$event]);
    }

    public function view()
    {
    }

    /**
     * The screen for adding/editing segments
     *
     * By the time a user comes here, they should have already chosen
     * a valid street and event_id for adding a new segment.
     * OR
     * If they're editing an existing segment, they must have a valid
     * segment_id
     */
    public function update()
    {
        if (isset($_REQUEST['segment_id'])) {
            try {
                $segment = new Segment($_REQUEST['segment_id']);
                $event   = $segment->getEvent();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (!isset($event) && !empty($_REQUEST['event_id']) && !empty($_REQUEST['street'])) {
            try {
                $event = new Event($_REQUEST['event_id']);
                $segment = new Segment();
                $segment->setEvent($event);
                $segment->setStreet($_REQUEST['street']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (!isset($segment)) {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        if (isset($_POST['segment_id'])) {
            try {
                $segment->handleUpdate($_POST);
                $segment->save();
                header('Location: '.BASE_URL.'/segments?event_id='.$segment->getEvent_id());
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        $this->template->setFilename('eventEdit');
        $this->template->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'   => $event]);
        $this->template->blocks['panel-one'][] = new Block('segments/updateForm.inc',      ['segment' => $segment]);
        $this->template->blocks['panel-one'][] = new Block('segments/list.inc',            ['segments'=> $event->getSegments()]);
        $this->template->blocks[]              = new Block('events/map.inc',               ['event'   => $event]);
    }

    public function delete()
    {
        try {
            $segment = new Segment($_GET['id']);
            $event = $segment->getEvent();

            $segment->delete();
            header('Location: '.BASE_URL.'/events/view?id='.$event->getId());
            exit();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
        }
        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks[] = new Block('404.inc');
    }
}