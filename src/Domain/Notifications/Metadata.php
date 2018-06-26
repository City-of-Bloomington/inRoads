<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain\Notifications;

class Metadata
{
    const TYPE_UPDATES   = 'updates';
    const TYPE_EMERGENCY = 'emergency';

    public static $TYPES = [
        self::TYPE_UPDATES,
        self::TYPE_EMERGENCY
    ];
}
