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
            if (!empty($_REQUEST['street'])) {
                $segment->setStreet($_REQUEST['street']);
            }

            $b = $segment->getStreet() ? 'segments/updateForm.inc' : 'segments/searchForm.inc';
            $this->template->blocks['panel-one'][] = new Block($b, ['segment'=>$segment]);
        }

        $this->template->blocks['headerBar'][] = new Block('events/headerBars/update.inc', ['event'=>$event]);
        $this->template->blocks['panel-one'][] = new Block('segments/list.inc', ['segments'=>$event->getSegments()]);
        $this->template->blocks[]              = new Block('events/map.inc',               ['event'=>$event]);
    }

    public function view()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
        try {
            $segment = new Segment($_GET['id']);
            $event = $segment->getEvent();
            $segment->delete();
            header('Location: '.BASE_URI.'/events/view?id='.$event->getId());
            exit();
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
    }
}