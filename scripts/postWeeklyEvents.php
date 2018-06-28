#!/usr/local/bin/php
<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\EventType;
use Application\Models\GoogleGateway;
use Blossom\Classes\Block;
use Blossom\Classes\Template;

// You'll need to edit the path to the configuration file in your
// road_closings installation.
include __DIR__.'/../bootstrap.inc';

$start  = new \DateTime('+1 monday');
$end    = new \DateTime('+2 monday');
$future = new \DateTime('+120 days');

$filters = ['eventTypes'=>[]];
foreach (EventType::types() as $type) {
    if ($type->isDefaultForSearch()) {
        $filters['eventTypes'][] = $type->getCode();
    }
}

// Add next week's events to the email message
$eventsNextWeek = GoogleGateway::getEvents(GOOGLE_CALENDAR_ID, $start, $end, $filters);

// Add future events to the email message
$eventsFuture = [];
$ids          = [];
foreach ($eventsNextWeek as $e) { $ids[] = $e->getId(); }
$list = GoogleGateway::getEvents(GOOGLE_CALENDAR_ID, $end, $future, $filters);
foreach ($list as $e) {
    if (!in_array($e->getId(), $ids)) { $eventsFuture[] = $e; }
}


$template = new Template('default', 'txt');
$block    = new Block('events/summary.inc', [
    'start'          => $start,
    'end'            => $end,
    'eventsNextWeek' => $eventsNextWeek,
    'eventsFuture'   => $eventsFuture
]);
$message  = $block->render('txt', $template);
$subject  = 'Road closings for the week: '.$start->format(DATE_FORMAT).' to '.$end->format(DATE_FORMAT);
$from     = 'From: '.ADMINISTRATOR_EMAIL;
mail(GOOGLE_GROUP, $subject, $message, $from);
