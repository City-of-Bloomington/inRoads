<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Update;

use Domain\Notifications\DataStorage\NotificationsRepository;
use Domain\Notifications\Entities\Notification;

class Update
{
    private $repo;

    public function __construct(NotificationsRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(UpdateRequest $request): UpdateResponse
    {
        $n = new Notification((array)$request);
        if (!$n->type || !$n->email) {
            return new UpdateResponse(null, ['missingRequiredFields']);
        }

        try {
            $id = $this->repo->save($n);
            return new UpdateResponse($id);
        }
        catch (\Exception $e) {
            return new UpdateResponse(null, [$e->getMessage()]);
        }

    }
}
