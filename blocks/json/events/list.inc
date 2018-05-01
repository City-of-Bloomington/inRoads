<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->events An array of Events
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\Block;

$events = [];
foreach ($this->events as $e) {
    $format = $e->isAllDay() ? 'Y-m-d' : 'c';

    $event = [
        'id'                    => $e->getId(),
        'type'                  => $e->getEventType()->__toString(),
        'department'            => $e->getDepartment(),
        'start'                 => $e->getStart()->format($format),
        'end'                   => $e->getEnd  ()->format($format),
        'title'                 => $e->getTitle(),
        'geography_description' => $e->getGeography_description(),
        'description'           => $e->getDescription()
    ];
    if (Person::isAllowed('events', 'viewPrimaryContact')) {
        $event['primaryContact'] = $e->getPrimaryContact();
    }
    $rrule = $e->getRRule();
    if ($rrule) { $event['rrule'] = $rrule->getString(); }

    $geography = $e->getGeography();
    if ($geography) { $event['geography'] = $geography; }

    $events[] = $event;
}
echo json_encode($events, JSON_PRETTY_PRINT);
