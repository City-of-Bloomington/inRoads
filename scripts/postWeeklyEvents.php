#!/usr/local/bin/php
<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\EventType;
use Application\Models\GoogleGateway;
use Blossom\Classes\Block;

// You'll need to edit the path to the configuration file in your
// road_closings installation.
include __DIR__.'/../bootstrap.inc';

$start = new \DateTime('+1 monday');
$end   = new \DateTIme('+2 monday');

$filters = ['eventTypes'=>[]];
foreach (EventType::types() as $type) {
    if ($type->isDefaultForSearch()) {
        $filters['eventTypes'][] = $type->getCode();
    }
}
$list = GoogleGateway::getEvents(GOOGLE_CALENDAR_ID, $start, $end, $filters);
$block = new Block('events/summary.inc', [
    'start'     => $start,
    'end'       => $end,
    'eventList' => $list
]);

$message = $block->render('txt');
$subject = 'Road closings for the week: '.$start->format(DATE_FORMAT).' to '.$end->format(DATE_FORMAT);
$from    = 'From: '.ADMINISTRATOR_EMAIL;
mail(GOOGLE_GROUP, $subject, $message, $from);
