<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Find;

class FindResponse
{
    public $notifications = [];
    public $errors        = [];

    public function __construct(?array $notifications=null, ?array $errors=null)
    {
        $this->notifications = $notifications;
        $this->errors        = $errors;
    }
}
