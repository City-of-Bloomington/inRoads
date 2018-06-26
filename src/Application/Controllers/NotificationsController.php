<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Views\Notifications\ListView;
use Application\Views\Notifications\UpdateView;
use Blossom\Classes\Controller;
use Blossom\Classes\Database;
use Domain\Notifications\DataStorage\ZendDbNotificationsRepository;
use Domain\Notifications\Metadata as Notification;
use Domain\Notifications\UseCases\Delete\Delete;
use Domain\Notifications\UseCases\Find\Find;
use Domain\Notifications\UseCases\Load\Load;
use Domain\Notifications\UseCases\Update\Update;
use Domain\Notifications\UseCases\Update\UpdateRequest;


class NotificationsController extends Controller
{
    private $repo;

    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);

        $this->repo = new ZendDbNotificationsRepository(Database::getConnection());
    }

    public function index()
    {
        $find      = new Find($this->repo);
        $responses = [];
        foreach (Notification::$TYPES as $t) {
            $responses[$t] = $find($t);
        }
        return new ListView($responses);
    }

    public function update()
    {
        $request = new UpdateRequest($_REQUEST);

        if (isset($_POST['type'])) {
            $update   = new Update($this->repo);
            $response = $update($request);
            if (!$response->errors) {
                header('Location: '.BASE_URL.'/notifications');
                exit();
            }
        }
        elseif (!empty($_REQUEST['id'])) {
            $load = new Load($this->repo);
            $res  = $load((int)$_REQUEST['id']);
            if ($res->notification) {
                foreach ($request as $k=>$v) {
                    if (!$v) { $request->$k = $res->notification->$k; }
                }
            }
            else {
                $_SESSION['errorMessages'] = $res->errors;
                return new \Application\Views\NotFoundView();
            }
        }

        return new UpdateView($request, isset($response) ? $response : null);
    }

    public function delete()
    {
        if (!empty($_REQUEST['id'])) {
            $delete = new Delete($this->repo);
            $res    = $delete((int)$_REQUEST['id']);
            if ($res->errors) {
                $_SESSION['errorMessages'] = $res->errors;
            }
            header('Location: '.BASE_URL.'/notifications');
            exit();
        }
        return new \Application\Views\NotFoundView();
    }
}
