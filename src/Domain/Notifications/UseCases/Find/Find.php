<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\UseCases\Find;

use Domain\Notifications\DataStorage\NotificationsRepository;

class Find
{
    private $repo;

    public function __construct(NotificationsRepository $repository)
    {
        $this->repo = $repository;
    }

    public function __invoke(string $type): FindResponse
    {
        try {
            $result = $this->repo->find($type);
            return new FindResponse($result['rows'], $result['total']);
        }
        catch (\Exception $e) {
            return new FindResponse(null, [$e->getMessage()]);
        }
    }
}
