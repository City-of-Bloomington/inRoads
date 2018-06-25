<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Users;

use Application\Paginator;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

use Domain\Users\UseCases\Search\SearchResponse;

class SearchView extends Template
{
    public function __construct(SearchResponse $response, int $itemsPerPage, int $currentPage)
    {
        parent::__construct('admin', 'html');

        $this->vars['title'] = $this->_('users_search');
        if ($response->errors) {
            $_SESSION['errorMessages'] = $response->errors;
        }

        $this->blocks[] = new Block('users/findForm.inc', ['users'=>$response->users]);

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
