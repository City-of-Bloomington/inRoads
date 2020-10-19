<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;
use Application\Models\EventType;
use Application\Models\EventTypesTable;
use Application\Controller;
use Application\Block;

class EventTypesController extends Controller
{
    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
    }

    public function index()
    {
        $table = new EventTypesTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('eventTypes/list.inc', ['eventTypes'=>$list]);
        return $this->template;
    }

    public function view()
    {
        if (isset($_REQUEST['eventType_id'])) {
            try { $type = new EventType($_REQUEST['eventType_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($type)) {
            $this->template->blocks[] = new Block('eventTypes/info.inc', ['eventType'=>$type]);
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['eventType_id'])) {
            try { $type = new EventType($_REQUEST['eventType_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            $type = new EventType();
        }

        if (isset($type)) {
            if (isset($_POST['code'])) {
                try {
                    $type->handleUpdate($_POST);
                    $errors = $type->save();
                    header('Location: '.BASE_URL.'/eventTypes');
                    exit();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->blocks[] = new Block('eventTypes/updateForm.inc', ['eventType'=>$type]);
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
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
