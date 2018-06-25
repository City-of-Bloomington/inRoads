<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Search;

class SearchResponse
{
    public $people = [];
    public $errors = [];
    public $total  = 0;

    public function __construct(array $people, int $total=null, array $errors=null)
    {
        $this->people = $people;
        $this->total  = $total;
        $this->errors = $errors;
    }
}
