<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Load;

use Domain\People\DataStorage\PeopleRepository;

class Load
{
    private $repo;

    public function __construct(PeopleRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(int $person_id): LoadResponse
    {
        try {
            return new LoadResponse($this->repo->load($person_id));
        }
        catch (\Exception $e) {
            return new LoadResponse(null, [$e->getMessage()]);
        }
    }
}
