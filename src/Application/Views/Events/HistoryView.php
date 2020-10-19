<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Events;

use Application\Block;
use Application\Template;

class HistoryView extends Template
{
    /**
     * @param array $history  An array of EventHistory objects
     */
    public function __construct(array $history)
    {
        parent::__construct('admin', 'html');
        $this->blocks[] = new Block('events/history.inc', ['history'=>$history]);
    }
}
