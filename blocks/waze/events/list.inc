<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 */
use Application\Models\GoogleGateway;
use Blossom\Classes\View;

$WAZE_DATE_FORMAT = \DateTime::RFC3339;

echo '<events>';
foreach ($this->events as $event) {
    if (empty($event->recurrence)) {
        $startDateTime = $event->getStart($WAZE_DATE_FORMAT);
        $endDateTime   = $event->getEnd  ($WAZE_DATE_FORMAT);
    }
    else {
        $data = [];
        GoogleGateway::parseDates($data, $event->recurrence);
        $tempS = empty($data['startTime']) ? new \DateTime($data['startDate']) : new \DateTime("$data[startDate] $data[startTime]");
        $tempE = empty($data['endTime'  ]) ? new \DateTime($data['endDate'  ]) : new \DateTime("$data[endDate] $data[endTime]");
        $startDateTime = $tempS->format($WAZE_DATE_FORMAT);
        $endDateTime   = $tempE->format($WAZE_DATE_FORMAT);
    }
    $geography = $event->getGeography();
    if (substr($geography, 0, 10) === 'LINESTRING') {
        $geo = explode(',', substr($geography, 11, -1));
        list($start_long, $start_lat) = explode(' ', $geo[0]);
        list(  $end_long,   $end_lat) = explode(' ', end($geo));

        preg_match('/^(.+) from (.+) to (.+)$/', $event->getGeography_description(), $matches);
        $street      = !empty($matches[1]) ? View::escape($matches[1]) : '';
        $street_from = !empty($matches[2]) ? View::escape($matches[2]) : '';
        $street_to   = !empty($matches[3]) ? View::escape($matches[3]) : '';

        $description = View::escape($event->getDescription());
        echo "
        <event id=\"{$event->getId()}\">
            <type>ROAD_CLOSED</type>
            <start_date>$startDateTime</start_date>
            <update_date>{$event->getUpdated($WAZE_DATE_FORMAT)}</update_date>
            <end_date>$endDateTime</end_date>
            <description>$description</description>
            <location>
                <street>$street</street>
                <latitude>$start_lat</latitude>
                <longitude>$start_long</longitude>
                <specify_end>
                    <from_cross_street>$street_from</from_cross_street>
                    <end_cross_street>$street_to</end_cross_street>
                    <end_latitude>$end_lat</end_latitude>
                    <end_longitude>$end_long</end_longitude>
                </specify_end>
            </location>
        </event>
        ";
    }
}
echo '</events>';
