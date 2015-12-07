<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DateTime $start
 * @param DateTime $end
 * @param array $eventList
 */
use Blossom\Classes\Block;
?>
This is an automated email message from inRoads.

inRoads is a digital service of the City of Bloomington to publish planned
and future road closings, lane reductions, parking reservations and more.
inRoads can be accessed online at http:bloomigton.in.gov/inroads.

Here are the upcoming road related events for next week, <?= $this->start->format(DATE_FORMAT); ?> to <?= $this->end->format(DATE_FORMAT); ?>:

<?php
    $block = new Block('events/list.inc', ['events'=>$this->eventList]);
    echo $block->render('txt');

    $group = substr(GOOGLE_GROUP, 0, strpos(GOOGLE_GROUP, '@'));
?>

To update your subscription status, please visit:
https://groups.google.com/forum/#!forum/<?= $group; ?>
