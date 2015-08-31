<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\EventType;
include '../../../configuration.inc';

foreach ($EVENT_TYPES as $t) {
    $t['isDefault'] = $t['default'];
    $t['color'] = str_pad(dechex($t['color'][0]), 2, '0', STR_PAD_LEFT)
                 .str_pad(dechex($t['color'][1]), 2, '0', STR_PAD_LEFT)
                 .str_pad(dechex($t['color'][2]), 2, '0', STR_PAD_LEFT);

    $type = new EventType();
    $type->handleUpdate($t);
    $type->save();
}