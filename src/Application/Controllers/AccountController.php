<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Views\Account\InfoView;
use Application\Views\Account\UpdateView;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Database;

use Domain\People\DataStorage\ZendDbPeopleRepository;
use Domain\People\UseCases\Info\Info;
use Domain\People\UseCases\Info\InfoRequest;
use Domain\Users\DataStorage\ZendDbUsersRepository;
use Domain\People\UseCases\UpdateAccount\UpdateAccount;
use Domain\People\UseCases\UpdateAccount\UpdateAccountRequest;

class AccountController extends Controller
{
    private $repo;

    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
        $this->repo = new ZendDbPeopleRepository(Database::getConnection());
    }

    // View my account info
    public function index()
    {
        if (isset($_SESSION['USER'])) {
            $info = new Info($this->repo);
            $req  = new InfoRequest($_SESSION['USER']->id);
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
        if (isset($_SESSION['USER'])) {
            $request = new UpdateAccountRequest($_SESSION['USER']->id, $_REQUEST);

            if (isset($_POST['firstname'])) {
                $update = new UpdateAccount($this->repo);
                $response = $update($request);
                if (!$response->errors) {
                    self::refreshSessionForUser($response->id);
                    header('Location: '.BASE_URL.'/account');
                    exit();
                }
            }

            // Populate the request with current person information
            $load = new \Domain\People\UseCases\Load\Load($this->repo);
            $res  = $load($_SESSION['USER']->id);
            foreach ($request as $k=>$v) {
                if (!$v) { $request->$k = $res->person->$k; }
            }
            return new UpdateView($request, isset($response) ? $response : null);
        }
        else {
            return new \Application\Views\ForbiddenView();
        }
    }

    private static function refreshSessionForUser(int $id)
    {
        $repo = new ZendDbUsersRepository(Database::getConnection());
        $load = new \Domain\Users\UseCases\Load\Load($repo);
        $res  = $load($id);
        $_SESSION['USER'] = $res->user;
    }
}
