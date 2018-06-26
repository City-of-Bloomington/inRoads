<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\DataStorage;

use Domain\People\Entities\Person;
use Domain\People\UseCases\Search\SearchRequest;

interface PeopleRepository
{
    // Read function
    public function load(int $person_id): Person;
    public function search(SearchRequest $req): array;

    // Write functions
    public function save         (Person $person): int;
    public function updateAccount(Person $person): int;
}
