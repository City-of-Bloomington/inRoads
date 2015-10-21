<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\AddressService;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class StreetsController extends Controller
{
    public function index()
    {
    }

    public function search()
    {
        $results = AddressService::searchStreets($_GET['street']);
        $this->template->blocks[] = new Block('streets/results.inc', ['results'=>$results]);
    }

    public function view()
    {
    }
}