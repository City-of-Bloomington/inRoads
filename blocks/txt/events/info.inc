<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
$segments = $segments ? implode("\n", $segments) : '';

echo "
-------------------------------------------------
## {$this->event->getTitle()} ##
{$this->event->getGeography_description()}

$segments

{$this->event->getHumanReadableDuration('F j, Y')}
$type
{$this->event->getDescription()}
$url
";
