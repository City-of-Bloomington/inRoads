<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Delete;

use Domain\Notifications\DataStorage\NotificationsRepository;

class Delete
{
    private $repo;

    public function __construct(NotificationsRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(int $id): DeleteResponse
    {
        try {
            $this->repo->delete($id);
            return new DeleteResponse();
        }
        catch (\Exception $e) {
            return new DeleteResponse(null, [$e->getMessage()]);
        }
    }
}
