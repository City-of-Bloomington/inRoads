<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;
use Application\Models\EventType;
use Application\Models\EventTypesTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class EventTypesController extends Controller
{
    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
    }

    private function loadEventType($id)
    {
        try {
            return new EventType($id);
        }
        catch (\Exception $e) {
            $this->template->setFlashMessages($e, 'errorMessages');
            header('Location: '.BASE_URL.'/eventTypes');
            exit();
        }
    }

    public function index()
    {
        $table = new EventTypesTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('eventTypes/list.inc', ['eventTypes'=>$list]);
    }

    public function view()
    {
        $type = $this->loadEventType($_REQUEST['eventType_id']);
        $this->template->blocks[] = new Block('eventTypes/info.inc', ['eventType'=>$type]);
    }

    public function update()
    {
        $type =             !empty($_REQUEST['eventType_id'])
            ? $this->loadEventType($_REQUEST['eventType_id'])
            : new EventType();

		if (isset($_POST['code'])) {
			$type->handleUpdate($_POST);
			$errors = $type->save();
			if (!count($errors)) {
				header('Location: '.BASE_URL.'/eventTypes');
				exit();
			}
			else {
                $this->template->setFlashMessages($errors, 'errorMessages');
			}
		}

		$this->template->blocks[] = new Block('eventTypes/updateForm.inc', ['eventType'=>$type]);
    }

    /**
     * Change the ordering of the eventTypes
     */
    public function order()
    {
        if (isset($_POST['sortingNumber'])) {
            EventType::handleOrderingUpdate($_POST);
        }
        header('Location: '.BASE_URL.'/eventTypes');
        exit();
    }
}