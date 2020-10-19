<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Users;

use Application\Paginator;

use Application\Block;
use Application\Template;

use Domain\Users\UseCases\Search\SearchResponse;

class SearchView extends Template
{
    public function __construct(SearchResponse $response, int $itemsPerPage, int $currentPage)
    {
        $format = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';
        $layout = $format == 'html' ? 'admin' : 'default';
        parent::__construct($layout, $format);

        $this->vars['title'] = $this->_(['user', 'users', 10]);
        if ($response->errors) {
            $_SESSION['errorMessages'] = $response->errors;
        }

        if ($format == 'html') {
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
        else {
            $this->blocks[] = new Block('users/list.inc', ['users'=>$response->users]);
        }
    }
}
