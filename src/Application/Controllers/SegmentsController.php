<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\AddressService;
use Application\Models\Event;
use Application\Auth;
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
            return new \Application\Views\NotFoundView();
        }

        $this->template->setFilename('eventEdit');
        $this->template->title = $event->getEventType();

        if (Auth::isAllowed('segments', 'update')) {
            $segment = new Segment();
            $segment->setEvent($event);

            $this->template->blocks['panel-one'][] = new Block('segments/searchForm.inc', ['segment'=>$segment]);
        }

        $this->template->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'=>$event]);
        $this->template->blocks['panel-one'][] = new Block('segments/list.inc', ['segments'=>$event->getSegments()]);
        $this->template->blocks[]              = new Block('events/map.inc',               ['event'=>$event]);
		return $this->template;
    }

    public function view()
    {
		return $this->template;
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
            return new \Application\Views\NotFoundView();
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
		return $this->template;
    }

    public function delete()
    {
        try {
            $segment = new Segment($_GET['id']);
            $event = $segment->getEvent();

            $segment->delete();
            header('Location: '.BASE_URL.'/segments?event_id='.$event->getId());
            exit();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
        }
        return new \Application\Views\NotFoundView();
    }
}
