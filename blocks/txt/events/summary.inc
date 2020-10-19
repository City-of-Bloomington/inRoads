<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param DateTime $this->start
 * @param DateTime $this->end
 * @param array    $this->eventsNextWeek
 * @param array    $this->eventsFuture
 */
use Application\Block;
$this->_include('events/partials/emailHeader.inc');
$group = substr(GOOGLE_GROUP, 0, strpos(GOOGLE_GROUP, '@'));
?>

########################################################
Road events for next week, <?= $this->start->format(DATE_FORMAT); ?> to <?= $this->end->format(DATE_FORMAT); ?>:
########################################################
<?php
    $block = new Block('events/list.inc', ['events'=>$this->eventsNextWeek]);
    echo $block->render('txt');

?>

########################################################
Future road events
########################################################
<?php
    $block->events = $this->eventsFuture;
    echo $block->render('txt');
?>

-----------------------------------------------------------
To update your subscription status, please visit:
https://groups.google.com/forum/#!forum/<?= $group; ?>
