<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views;

use Application\Block;
use Application\Template;

class ValidationErrorView extends Template
{
    public function __construct(array $errors)
    {
        header('HTTP/1.1 422 Unprocessable Entity', true, 422);

        parent::__construct('admin', 'html', $vars);
        $this->blocks[] = new Block('errorMessages.inc', ['errorMessages' => $errors]);
    }
}
