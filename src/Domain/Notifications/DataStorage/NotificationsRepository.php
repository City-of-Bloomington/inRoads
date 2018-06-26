<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\DataStorage;

use Domain\Notifications\Entities\Notification;

interface NotificationsRepository
{
    // Read functions
    public function find(string $type): array;
    public function load(int $id): Notification;

    // Write functions
    public function save(Notification $n): int;
    public function delete(int $id);
}
