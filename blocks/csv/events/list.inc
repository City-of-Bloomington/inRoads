<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 */
use Application\Models\Person;
use Application\Models\Event;
use Blossom\Classes\Block;

$out = fopen('php://output', 'w');

foreach ($this->events as $e) {
    $format = $e->isAllDay() ? 'Y-m-d' : 'c';

    $event = [
        'id'                    => $e->getId(),
        'type'                  => $e->getEventType(),
        'department'            => $e->getDepartment(),
        'start'                 => $e->getStart($format),
        'end'                   => $e->getEnd  ($format),
        'title'                 => $e->getTitle(),
        'geography_description' => $e->getGeography_description(),
        'description'           => $e->getDescription()
    ];
    $rrule = $e->getRRule();
    if ($rrule) { $event['rrule'] = $rrule->getString(); }

    $geography = $e->getGeography();
    if ($geography) { $event['geography'] = $geography; }

    fputcsv($out, $event);
}
fclose($out);