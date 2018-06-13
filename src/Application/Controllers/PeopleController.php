<?php
/**
 * @copyright 2012-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
        $template->setFilename('admin');
        parent::__construct($template);
    }

    private function loadPerson($id)
    {
        try {
            return new Person($id);
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
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
		return $this->template;
	}

	public function view()
	{
        if (!empty($_REQUEST['person_id'])) {
            try { $person = new Person($_REQUEST['person_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($person)) {
            $this->template->blocks[] = new Block('people/info.inc',array('person'=>$person));
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
	}

	public function update()
	{
        if (!empty($_REQUEST['person_id'])) {
            try { $person = new Person($_REQUEST['person_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            $person = new Person();
        }

        if (isset($person)) {
            $return_url = !empty($_REQUEST['return_url'])
                        ? $_REQUEST['return_url']
                        : BASE_URL."/people/view?person_id={$person->getId()}";

            if (isset($_POST['firstname'])) {
                try {
                    $person->handleUpdate($_POST);
                    $person->save();
                    if ($_SESSION['USER']->getId() == $person->getId()) {
                        $_SESSION['USER'] = $person;
                    }

                    header("Location: $return_url");
                    exit();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->blocks[] = new Block('people/updateForm.inc', ['person'=>$person, 'return_url'=>$return_url]);
            return $this->template;
        }
        else {
            return new \Application\Views\NotFoundView();
        }
	}
}
