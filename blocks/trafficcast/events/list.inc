<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->events An array of Events
 */
use Application\Models\GoogleGateway;

$WAZE_DATE_FORMAT = \DateTime::RFC3339;

echo '<events>';
foreach ($this->events as $event) {
    if (empty($event->recurrence)) {
        $startDateTime = $event->getStart()->format($WAZE_DATE_FORMAT);
        $endDateTime   = $event->getEnd  ()->format($WAZE_DATE_FORMAT);
    }
    else {
        $data = [];
        GoogleGateway::parseDates($data, $event->recurrence);
        $startDateTime = $data['startDate']->format($WAZE_DATE_FORMAT);
        $endDateTime   = $data[  'endDate']->format($WAZE_DATE_FORMAT);
    }

    $cifsType    = self::escape($event->getEventType()->getCifsType());
    $constructionFlag = $event->isConstruction() ? 'CONSTRUCTION' : 'INCIDENT';
    $description = self::escape($event->getDescription());
    $locations   = '';
    foreach ($event->getSegments() as $segment) {
        $street = self::escape($segment->getStreet());
        $from   = self::escape($segment->getStreetFrom());
        $to     = self::escape($segment->getStreetTo());
        $dir    = self::escape($segment->getDirection());

        echo "
        <event id=\"{$event->getId()}\">
            <type>$cifsType</type>
            <flag>$constructionFlag</flag>
            <start_date>$startDateTime</start_date>
            <update_date>{$event->getUpdated($WAZE_DATE_FORMAT)}</update_date>
            <end_date>$endDateTime</end_date>
            <description>$description</description>
            <location>
                <street>$street</street>
                <Direction>$dir</Direction>
                <latitude>{$segment->getStartLatitude()}</latitude>
                <longitude>{$segment->getStartLongitude()}</longitude>
                <specify_end>
                    <from_cross_street>$from</from_cross_street>
                    <end_cross_street>$to</end_cross_street>
                    <end_latitude>{$segment->getEndLatitude()}</end_latitude>
                    <end_longitude>{$segment->getEndLongitude()}</end_longitude>
                </specify_end>
            </location>
        </event>
        ";
    }
}
echo '</events>';
