<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Events;

use Blossom\Classes\Block;
use Blossom\Classes\Template;

class ListView extends Template
{
    public function __construct(array $events)
    {
        $format = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';
        parent::__construct('default', $format);

        $this->title = $this->_('application_title');
        $this->blocks[] = new Block('events/list.inc', ['events' => $events]);
    }
}
