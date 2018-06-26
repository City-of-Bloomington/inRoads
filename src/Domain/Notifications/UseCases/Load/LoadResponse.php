<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Load;

use Domain\Notifications\Entities\Notification;

class LoadResponse
{
    public $notification;
    public $errors;

    public function __construct(?Notification $notification=null, ?array $errors=null)
    {
        $this->notification = $notification;
        $this->errors       = $errors;
    }
}
