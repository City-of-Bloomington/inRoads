<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models;

class Notifications
{
    // These are the flags in the database to look for.
    const TYPE_UPDATES   = 'notify_updates';
    const TYPE_EMERGENCY = 'notify_emergency';

    public static $TYPES = [
        self::TYPE_UPDATES,
        self::TYPE_EMERGENCY
    ];

    /**
     * Returns an array of email address strings
     *
     * @param  string $notificationType  The notification type
     * @return array
     */
    public static function emailAddresses(string $notificationType): array
    {
        global $NOTIFICATIONS_ADDITIONAL_ADDRESSES;

        if (!in_array($notificationType, self::$TYPES)) {
            throw new \Exception('notifications/unknownType');
        }

        $emailAddresses = isset($NOTIFICATIONS_ADDITIONAL_ADDRESSES)
                        ? $NOTIFICATIONS_ADDITIONAL_ADDRESSES
                        : [];

        $table = new PeopleTable();
        $list  = $table->find([$notificationType=>true]);
        foreach ($list as $p) {
            $email = $p->getEmail();
            if ($email) { $emailAddresses[] = $email; }
        }
        return $emailAddresses;
    }
}
