<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->events An array of Events
 */
use Application\Block;

$block = new Block('events/info.inc');

foreach ($this->events as $e) {
    $block->event = $e;
    echo $block->render('txt');
}
