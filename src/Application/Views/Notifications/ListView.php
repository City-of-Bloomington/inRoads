<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Views\Notifications;

use Application\Block;
use Application\Template;

use Domain\Notifications\UseCases\Find\FindResponse;

class ListView extends Template
{
    /**
     * @param array $types  An array of FindResponses indexed by type
     */
    public function __construct(array $responses)
    {
        parent::__construct('admin', 'html');

        $this->vars['title'] = $this->_(['notification', 'notifications', 10]);

        foreach ($responses as $type=>$response) {
            $this->blocks[] = new Block('notifications/list.inc', [
                'notifications' => $response->notifications,
                'type'          => $type
            ]);
        }
    }
}
