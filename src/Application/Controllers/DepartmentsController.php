<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;
use Application\Models\Department;
use Application\Models\DepartmentsTable;
use Application\Controller;
use Application\Block;

class DepartmentsController extends Controller
{
    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
    }

    public function index()
    {
        $table = new DepartmentsTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('departments/list.inc', ['departments'=>$list]);
        return $this->template;
    }

    public function view()
    {
        if (!empty($_REQUEST['department_id'])) {
            try { $department = new Department($_REQUEST['department_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        if (isset($department)) {
            $this->template->blocks[] = new Block('departments/info.inc', ['department'=>$department]);
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['department_id'])) {
            try { $department = new Department($_REQUEST['department_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            $department = new Department();
        }

        if (isset($department)) {
            if (isset($_POST['code'])) {
                try {
                    $department->handleUpdate($_POST);
                    $department->save();
                    header('Location: '.BASE_URL.'/departments');
                    exit();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->blocks[] = new Block('departments/updateForm.inc', ['department'=>$department]);
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
    }
}
