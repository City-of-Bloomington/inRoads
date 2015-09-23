<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->events An array of Events
 */
use Blossom\Classes\View;

$WAZE_DATE_FORMAT = \DateTime::RFC3339;

echo '<events>';
foreach ($this->events as $e) {
    $geography = $e->getGeography();
    if (substr($geography, 0, 10) === 'LINESTRING') {
        $geo = explode(',', substr($geography, 11, -1));
        list($start_lat, $start_long) = explode(' ', $geo[0]);
        list($end_lat,   $end_long)   = explode(' ', end($geo));

        preg_match('/^(.+) from (.+) to (.+)$/', $e->getGeography_description(), $matches);
        $street      = !empty($matches[1]) ? View::escape($matches[1]) : '';
        $street_from = !empty($matches[2]) ? View::escape($matches[2]) : '';
        $street_to   = !empty($matches[3]) ? View::escape($matches[3]) : '';

        $description = View::escape($e->getDescription());
        echo "
        <event id=\"{$e->getId()}\">
            <type>ROAD_CLOSED</type>
            <start_date>{$e->getStart($WAZE_DATE_FORMAT)}</start_date>
            <update_date>{$e->getUpdated($WAZE_DATE_FORMAT)}</update_date>
            <end_date>{$e->getEnd($WAZE_DATE_FORMAT)}</end_date>
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
