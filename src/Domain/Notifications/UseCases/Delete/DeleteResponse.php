<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Delete;

class DeleteResponse
{
    public $errors = [];

    public function __construct(?array $errors=null)
    {
        $this->errors = $errors;
    }
}
