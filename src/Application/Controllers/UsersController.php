<?php
/**
 * @copyright 2012-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Views\Users\InfoView;
use Application\Views\Users\SearchView;

use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Database;

use Domain\Auth\AuthenticationService;
use Domain\Users\DataStorage\ZendDbUsersRepository;
use Domain\Users\Metadata;
use Domain\Users\UseCases\Delete\Delete;
use Domain\Users\UseCases\Delete\DeleteRequest;
use Domain\Users\UseCases\Info\Info;
use Domain\Users\UseCases\Info\InfoRequest;
use Domain\Users\UseCases\Search\Search;
use Domain\Users\UseCases\Search\SearchRequest;
use Domain\Users\UseCases\Update\Update;
use Domain\Users\UseCases\Update\UpdateRequest;

class UsersController extends Controller
{
    const ITEMS_PER_PAGE = 20;

    public function __construct(&$template)
    {
        parent::__construct($template);
        $this->repo = new ZendDbUsersRepository(Database::getConnection());
    }

	public function index()
	{
		$page   = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$search = new Search($this->repo);
		$req    = new SearchRequest($_GET, null, self::ITEMS_PER_PAGE, $page);
        $res    = $search($req);
        return new SearchView($res, self::ITEMS_PER_PAGE, $page);
	}

	public function update()
	{
        $request = new UpdateRequest($_REQUEST);

        if (isset($_POST['username'])) {
            global $AUTHENTICATION_METHODS;
            $auth   = new AuthenticationService($this->repo, $AUTHENTICATION_METHODS);
            $update = new Update($this->repo, $auth);
            $response = $update($request);
            if (!$response->errors) {
                // @TODO refresh the session if we edited the logged in person
                header('Location: '.BASE_URL.'/users');
                exit();
            }
        }
        elseif (!empty($_REQUEST['id'])) {
            // Populate any empty fields in the UpdateRequest with information
            // from the current Person record.
            $info = new Info($this->repo);
            $req  = new InfoRequest((int)$_REQUEST['id']);
            $res  = $info($req);
            if (!$res->errors) {
                foreach ($request as $k=>$v) {
                    if (!$v  &&  isset($res->user->$k)) {
                        $request->$k = $res->user->$k;
                    }
                }
            }
            else {
                $_SESSION['errorMessages'] = $res->errors;
                return new \Application\Views\NotFoundView();
            }
        }
        $metadata = new Metadata($this->repo);
        return new \Application\Views\Users\UpdateView($request, isset($response) ? $response : null, $metadata);
	}

	public function delete()
	{
        if (!empty($_REQUEST['id'])) {
            $delete = new Delete($this->repo);
            $req    = new DeleteRequest((int)$_REQUEST['id']);
            $res    = $delete($req);
            if (count($res->errors)) {
                $_SESSION['errorMessages'] = $res->errors;
            }
        }
        else {
            $_SESSION['errorMessages'][] = 'users/unknown';
        }

		header('Location: '.BASE_URL.'/users');
		exit();
	}
}
