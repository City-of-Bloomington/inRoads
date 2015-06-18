#!/usr/local/bin/php
<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\GoogleGateway;
use Blossom\Classes\Block;

// You'll need to edit the path to the configuration file in your
// road_closings installation.
include '../configuration.inc';

$start = new \DateTime();
$end   = new \DateTIme('+1 week');

$list = GoogleGateway::getEvents(GOOGLE_CALENDAR_ID, $start, $end);
$block = new Block('events/list.inc', ['events'=>$list]);
$message = $block->render('txt');
$subject = 'Road closings for the week: '.$start->format(DATETIME_FORMAT).' to '.$end->format(DATETIME_FORMAT);
$from    = 'From: '.ADMINISTRATOR_EMAIL;
mail(GOOGLE_GROUP, $subject, $message, $from);
