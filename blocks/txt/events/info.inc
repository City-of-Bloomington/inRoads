<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Event;

$t = $this->event->getEventType();
$type = $t ? "{$t->getName()}; {$t->getDescription()}" : '';

$url = BASE_URL.'/events/view?id='.$this->event->getId();

$segments = [];
foreach ($this->event->getSegments() as $segment) {
    $segments[] = "{$segment->getStreet()} from {$segment->getStreetFrom()} to {$segment->getStreetTo()}";
}
$location = $segments ? implode("\n", $segments) : $this->event->getGeography_description();

echo "
-------------------------------------------------
## {$this->event->getTitle()} ##
$location
{$this->event->getHumanReadableDuration('F j, Y')}
$type
{$this->event->getDescription()}
$url
";
