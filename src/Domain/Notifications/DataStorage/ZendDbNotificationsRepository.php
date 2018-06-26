<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications\DataStorage;

use Domain\Notifications\Entities\Notification;
use Domain\ZendDbRepository;

use Zend\Db\Sql\Select;

class ZendDbNotificationsRepository extends ZendDbRepository implements NotificationsRepository
{
    const TABLE = 'notificationEmails';

    public function find(string $type): array
    {
        $select = $this->sql->select()
                            ->from(self::TABLE)
                            ->where(['type'=>$type]);
        $result = $this->performSelect($select);

        $notifications = [];
        foreach ($result['rows'] as $row) { $notifications[] = new Notification($row); }
        $result['rows'] = $notifications;
        return $result;
    }

    public function load(int $id): Notification
    {
        $select = $this->sql->select()
                            ->from(self::TABLE)
                            ->where(['id'=>$id]);
        $result = $this->performSelect($select);
        if (count($result['rows'])) {
            return new Notification($result['rows'][0]);
        }
        throw new \Exception('notification/unknown');
    }

    public function save(Notification $n): int
    {
        return parent::saveToTable((array)$n, self::TABLE);
    }

    public function delete(int $id)
    {
        $this->zend_db->query('delete from notificationEmails where id=?', [$id]);
    }
}
