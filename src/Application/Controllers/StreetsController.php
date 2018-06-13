<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\AddressService;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class StreetsController extends Controller
{
    public function index()
    {
		return $this->template;
    }

    public function search()
    {
        if (isset($_REQUEST['popup'])) {
            $this->template->setFilename('popup');
        }
        $results = !empty($_GET['street'])
            ? AddressService::searchStreets($_GET['street'])
            : [];

        $this->template->blocks[] = new Block('streets/results.inc', ['results'=>$results]);
		return $this->template;
    }

    public function view()
    {
		return $this->template;
    }
}
