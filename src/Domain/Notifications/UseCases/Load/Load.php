<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Load;

use Domain\Notifications\DataStorage\NotificationsRepository;
use Domain\Notifications\Entities\Notification;

class Load
{
    private $repo;

    public function __construct(NotificationsRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(int $id): LoadResponse
    {
        try {
            return new LoadResponse($this->repo->load($id));
        }
        catch (\Exception $e) {
            return new LoadResponse(null, [$e->getMessage()]);
        }
    }

}
