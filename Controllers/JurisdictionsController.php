<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Jurisdiction;
use Application\Models\JurisdictionsTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class JurisdictionsController extends Controller
{
    private function loadJurisdiction($id)
    {
        try {
            return new Jurisdiction($id);
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
            header('Location: '.BASE_URL.'/jurisdictions');
            exit();
        }
    }

    public function index()
    {
        $table = new JurisdictionsTable();
        $list = $table->find();

        $this->template->blocks[] = new Block('jurisdictions/list.inc', ['jurisdictions'=>$list]);
    }

    public function view()
    {
        $jurisdiction = $this->loadJurisdiction($_GET['jurisdiction_id']);
        $this->template->blocks[] = new Block('jurisdictions/info.inc', ['jurisdiction'=>$jurisdiction]);
    }

    public function update()
    {
        $j = !empty($_REQUEST['jurisdiction_id'])
            ? $this->loadJurisdiction($_REQUEST['jurisdiction_id'])
            : new Jurisdiction();

        if (isset($_POST['name'])) {
            $j->handleUpdate($_POST);
            try {
                $j->save();
                header('Location: '.BASE_URL.'/jurisdictions/view?jurisdiction_id='.$j->getId());
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e;
            }
        }

        $this->template->blocks[] = new Block('jurisdictions/updateForm.inc', ['jurisdiction'=>$j]);
    }
}
