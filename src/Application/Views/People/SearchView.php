<?php
/**
 * @copyright 2017-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\People;

use Application\Paginator;

use Application\Block;
use Application\Template;
use Application\Url;

use Domain\People\UseCases\Search\SearchResponse;

class SearchView extends Template
{
    public function __construct(SearchResponse $response, int $itemsPerPage, int $currentPage)
    {
        parent::__construct('admin', 'html');

        $this->vars['title'] = $this->_('people_search');
        if ($response->errors) {
            $_SESSION['errorMessages'] = $response->errors;
        }

        $vars = ['people' => $response->people];

        $fields = ['firstname', 'lastname', 'email'];
        foreach ($fields as $f) {
            $vars[$f] = !empty($_GET[$f]) ? parent::escape($_GET[$f]) : '';
        }

        $this->blocks[] = new Block('people/findForm.inc', $vars);

        if ($response->total > $itemsPerPage) {
            $this->blocks[] = new Block('pageNavigation.inc', [
                'paginator' => new Paginator(
                    $response->total,
                    $itemsPerPage,
                    $currentPage
            )]);
        }
    }
}
