<?php
/**
 * @copyright 2012-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;
use Application\Models\Person;
use Application\Models\PeopleTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class PeopleController extends Controller
{
    public function __construct(&$template)
    {
        $template->setFilename('full-width');
        parent::__construct($template);
    }

    private function loadPerson($id)
    {
        try {
            return new Person($id);
        }
        catch (\Exception $e) {
            $this->template->setFlashMessages($e, 'errorMessages');
            header('Location: '.BASE_URL.'/people');
            exit();
        }
    }
	public function index()
	{
		$table = new PeopleTable();
		$people = $table->find(null, null, true);

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$people->setCurrentPageNumber($page);
		$people->setItemCountPerPage(20);

		$this->template->blocks[] = new Block('people/list.inc',    ['people'   =>$people]);
		$this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$people]);
	}

	public function view()
	{
        $person = $this->loadPerson($_REQUEST['person_id']);
        $this->template->blocks[] = new Block('people/info.inc',array('person'=>$person));
	}

	public function update()
	{
        $person = !empty($_REQUEST['person_id'])
            ? $this->loadPerson($_REQUEST['person_id'])
            : new Person();

        $return_url = !empty($_REQUEST['return_url'])
            ? $_REQUEST['return_url']
            : null;

		if (isset($_POST['firstname'])) {
			$person->handleUpdate($_POST);
			$errors = $person->save();
			if (!count($errors)) {
				if (!$return_url) { $return_url = BASE_URL."/people/view?person_id={$person->getId()}"; }
				header("Location: $return_url");
				exit();
			}
			else {
                $this->template->setFlashMessages($errors, 'errorMessages');
			}
		}

		$this->template->blocks[] = new Block('people/updateForm.inc', ['person'=>$person, 'return_url'=>$return_url]);
	}
}
