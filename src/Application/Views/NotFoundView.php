<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Views;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

class NotFoundView extends Template
{
    public function __construct(array $vars=null)
    {
        header('HTTP/1.1 404 Not Found', true, 404);

        parent::__construct('admin', 'html', $vars);
        $this->blocks[] = new Block('404.inc');
    }
}
