<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Views;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

class LoginView extends Template
{
    public function __construct(array $vars=null)
    {
        parent::__construct('admin', 'html', $vars);

        $this->blocks[] = new Block('loginForm.inc', ['return_url'=>$this->return_url]);
    }
}
