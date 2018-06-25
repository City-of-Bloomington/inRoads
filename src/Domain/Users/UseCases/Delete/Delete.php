<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Users\UseCases\Delete;

use Domain\Users\DataStorage\UsersRepository;

class Delete
{
    private $repo;

    public function __construct(UsersRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(DeleteRequest $req): DeleteResponse
    {
        try {
            $this->repo->delete($req->id);
        }
        catch (\Exception $e) {
            return new DeleteResponse([$e->getMessage()]);
        }
        return new DeleteResponse();
    }
}
