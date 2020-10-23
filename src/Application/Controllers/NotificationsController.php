<?php
/**
 * @copyright 2018-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Event;
use Application\Views\Notifications\ListView;
use Application\Views\Notifications\Preview;
use Application\Views\Notifications\UpdateView;

use Application\Block;
use Application\Controller;
use Application\Database;
use Application\Template;

use Domain\Notifications\DataStorage\ZendDbNotificationsRepository;
use Domain\Notifications\Metadata as Notification;
use Domain\Notifications\UseCases\Delete\Delete;
use Domain\Notifications\UseCases\Find\Find;
use Domain\Notifications\UseCases\Load\Load;
use Domain\Notifications\UseCases\Update\Update;
use Domain\Notifications\UseCases\Update\UpdateRequest;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;

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

    public function send()
    {
        if (!empty($_REQUEST['event_id'])) {
            try { $event = new \Application\Models\Event($_REQUEST['event_id']); }
            catch (\Exception $e) { }
        }

        if (isset($event) && !empty($_REQUEST['type'])) {
            if (!empty($_POST['event_id']) && !empty($_POST['type'])) {
                self::sendNotifications($event, $_POST['type']);

                header('Location: '.BASE_URL.'/events/view?id='.$event->getId());
                exit();
            }
            return new Preview($event, $_REQUEST['type']);
        }
        else { return new \Application\Views\NotFoundView(); }
    }

	public static function sendNotifications(Event $event, string $type)
	{
        $template = new Template('default', 'txt');
        $block    = new Block("notifications/$type.inc", ['event'=>$event]);

        $message  = new Message();
        $message->setBody($block->render('txt', $template));
        $message->setSubject(sprintf($template->_('notification_subject %s', 'messages'), APPLICATION_NAME));
        $message->setFrom(NOTIFICATIONS_EMAIL, APPLICATION_NAME);
        $message->addTo(GOOGLE_GROUP);

        $smtp = new Smtp(new SmtpOptions(['host' => SMTP_HOST, 'port' => SMTP_PORT]));

        $repo = new ZendDbNotificationsRepository(Database::getConnection());
        $find = new Find($repo);
        $res  = $find($type);
        foreach ($res->notifications as $n) {
            $message->setTo($n->email);
            $smtp->send($message);
        }
	}
}
