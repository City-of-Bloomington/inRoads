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
use Blossom\Classes\Database;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;

class UsersController extends Controller
{
    /**
     * @override
     */
    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
    }

	public function index()
	{
		$people = new PeopleTable();
		$users = $people->find(['user_account'=>true], null, true);

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$users->setCurrentPageNumber($page);
		$users->setItemCountPerPage(20);

		$this->template->blocks[] = new Block('users/list.inc',array('users'=>$users));
		$this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$users]);
	}

	public function update()
	{
        if (!empty($_REQUEST['user_id'])) {
            try { $person = new Person($_REQUEST['user_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            $person = new Person();
        }

        if (isset($person)) {
            if (isset($_POST['username'])) {
                try {
                    $person->handleUpdateUserAccount($_POST);
                    $person->save();
                    if ($_SESSION['USER']->getId() == $person->getId()) {
                        $_SESSION['USER'] = $person;
                    }
                    header('Location: '.BASE_URL.'/users');
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            if ($person->getId()) {
                $this->template->blocks[] = new Block('people/info.inc',array('person'=>$person));
            }
            $this->template->blocks[] = new Block('users/updateForm.inc',array('user'=>$person));
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
	}

	public function delete()
	{
		try {
			$person = new Person($_REQUEST['user_id']);
			$person->deleteUserAccount();
			$person->save();
		}
		catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/users');
		exit();
	}
}
