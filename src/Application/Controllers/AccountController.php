<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Person;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class AccountController extends Controller
{
    public function __construct(&$template)
    {
        $template->setFilename('admin');
        parent::__construct($template);
    }

    // View my account info
    public function index()
    {
        $this->template->blocks[] = new Block('account/info.inc', ['person' => $_SESSION['USER']]);
    }

    public function update()
    {
        $person = new Person($_SESSION['USER']->getId());

        if (isset($_POST['firstname'])) {
            try {
                $person->handleUpdate($_POST);
                $person->save();
                $_SESSION['USER'] = $person;

                header('Location: '.BASE_URI.'/account');
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }
        $this->template->blocks[] = new Block('account/updateForm.inc', ['person' => $person]);
    }
}
