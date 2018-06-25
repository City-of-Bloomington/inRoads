<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\People\UseCases\Info;

use Domain\People\DataStorage\PeopleRepository;

class Info
{
    private $repo;

    public function __construct(PeopleRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(InfoRequest $req): InfoResponse
    {
        try {
            return new InfoResponse($this->repo->load($req->id));
        }
        catch (\Exception $e) {
            return new InfoResponse(null, [$e->getMessage()]);
        }
    }
}
