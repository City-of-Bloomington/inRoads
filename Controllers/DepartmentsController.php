<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;
use Application\Models\Department;
use Application\Models\DepartmentsTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class DepartmentsController extends Controller
{
    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
    }

    private function loadDepartment($id)
    {
        try {
            return new Department($id);
        }
        catch (\Exception $e) {
            $this->template->setFlashMessages($e, 'errorMessages');
            header('Location: '.BASE_URL.'/departments');
            exit();
        }
    }

    public function index()
    {
        $table = new DepartmentsTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('departments/list.inc', ['departments'=>$list]);
    }

    public function view()
    {
        $department = $this->loadDepartment($_REQUEST['department_id']);
        $this->template->blocks[] = new Block('departments/info.inc', ['department'=>$department]);
    }

    public function update()
    {
        $department =    !empty($_REQUEST['department_id'])
            ? $this->loadDepartment($_REQUEST['department_id'])
            : new Department();

		if (isset($_POST['code'])) {
			$department->handleUpdate($_POST);
			$errors = $department->save();
			if (!count($errors)) {
				header('Location: '.BASE_URL.'/departments');
				exit();
			}
			else {
                $this->template->setFlashMessages($errors, 'errorMessages');
			}
		}

		$this->template->blocks[] = new Block('departments/updateForm.inc', ['department'=>$department]);
    }
}