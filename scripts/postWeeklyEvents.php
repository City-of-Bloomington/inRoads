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
include __DIR__'/../configuration.inc';

$start = new \DateTime('+1 monday');
$end   = new \DateTIme('+2 monday');

$filters = ['eventTypes'=>[]];
foreach (EventType::types() as $type) {
    if ($type->isDefaultForSearch()) {
        $filters['eventTypes'][] = $type->getCode();
    }
}
$list = GoogleGateway::getEvents(GOOGLE_CALENDAR_ID, $start, $end, $filters);
$block = new Block('events/list.inc', ['events'=>$list]);

$group = substr(GOOGLE_GROUP, 0, strpos(GOOGLE_GROUP, '@'));

$message = "Here are the upcoming road related events for next week, ";
$message.= $start->format(DATE_FORMAT).' to '.$end->format(DATE_FORMAT).":\n\n";
$message.= $block->render('txt');
$message.= "
To update your subscription status, please visit:
https://groups.google.com/forum/#!forum/$group
";

$subject = 'Road closings for the week: '.$start->format(DATE_FORMAT).' to '.$end->format(DATE_FORMAT);
$from    = 'From: '.ADMINISTRATOR_EMAIL;
mail(GOOGLE_GROUP, $subject, $message, $from);
