<?php
/**
 * @copyright 2012-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Views\People\InfoView;
use Application\Views\People\SearchView;
use Application\Views\People\UpdateView;

use Application\Controller;
use Application\Block;
use Application\Database;

use Domain\People\DataStorage\ZendDbPeopleRepository;
use Domain\People\UseCases\Info\Info;
use Domain\People\UseCases\Info\InfoRequest;
use Domain\People\UseCases\Search\Search;
use Domain\People\UseCases\Search\SearchRequest;
use Domain\People\UseCases\Update\Update;
use Domain\People\UseCases\Update\UpdateRequest;

class PeopleController extends Controller
{
    const ITEMS_PER_PAGE = 20;
    private $repo;

    public function __construct(&$template)
    {
        parent::__construct($template);

        $this->repo = new ZendDbPeopleRepository(Database::getConnection());
    }

	public function index()
	{
		$page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$search = new Search($this->repo);
		$req    = new SearchRequest($_GET, null, self::ITEMS_PER_PAGE, $page);
        $res    = $search($req);
        return new SearchView($res, self::ITEMS_PER_PAGE, $page);
	}

	public function view()
	{
        if (!empty($_GET['person_id'])) {
            $info = new Info($this->repo);
            $req  = new InfoRequest((int)$_GET['person_id']);
            $res  = $info($req);
            if ($res->person) {
                return new InfoView($res);
            }
            else {
                $_SESSION['errorMessages'] = $res->errors;
            }
        }
        return new \Application\Views\NotFoundView();
	}

	public function update()
	{
        $request = new UpdateRequest($_REQUEST);

        if (isset($_POST['firstname'])) {
            $update   = new Update($this->repo);
            $response = $update($request);
            if (!$response->errors) {
                // @TODO refresh the session if we edited the logged in person

                header('Location: '.BASE_URL.'/people/view?person_id='.$response->id);
                exit();
            }
        }
        elseif (!empty($_REQUEST['id'])) {
            // Populate any empty fields in the UpdateRequest with information
            // from the current Person record.
            $info = new Info($this->repo);
            $req  = new InfoRequest((int)$_REQUEST['id']);
            try {
                $res = $info($req);
                foreach ($request as $k=>$v) {
                    if (!$v) { $request->$k = $res->person->$k; }
                }
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'] = $res->errors;
                return new \Application\Views\NotFoundView();
            }
        }

        return new UpdateView($request, isset($response) ? $response : null);
	}
}
