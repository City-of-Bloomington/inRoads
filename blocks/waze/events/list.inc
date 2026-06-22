<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->events An array of Events
 */
use Application\Models\GoogleGateway;

$WAZE_DATE_FORMAT = \DateTime::RFC3339;

function extractPointsFromWKT($wkt) {
    $points = [];
    if (preg_match_all('/POINT\s*\(\s*([\d\.\-]+)\s+([\d\.\-]+)\s*\)/', $wkt, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $points[] = [
                'longitude' => (float)$matches[1][$i],
                'latitude'  => (float)$matches[2][$i]
            ];
        }
    }
    return $points;
}

echo '<events>';
foreach ($this->events as $event) {
    if (empty($event->recurrence)) {
        $startDateTime = $event->getStart()->format($WAZE_DATE_FORMAT);
        $endDateTime   = $event->getEnd()->format($WAZE_DATE_FORMAT);
    }
    else {
        $data = [];
        GoogleGateway::parseDates($data, $event->recurrence);
        $startDateTime = $data['startDate']->format($WAZE_DATE_FORMAT);
        $endDateTime   = $data['endDate']->format($WAZE_DATE_FORMAT);
    }

    $cifsType    = self::escape($event->getEventType()->getCifsType());
    $description = self::escape($event->getDescription());

    $pointIndex = 0;
    $points = extractPointsFromWKT($event->getGeography());
    foreach ($points as $point) {
        $id = $event->getId();
        if (isset($event->recurrence->id)) { $id .= '-'.$event->recurrence->id; }
        $id .= '-point-' . $pointIndex;
        ?>
        <event id="<?= $id ?>">
            <type><?= $cifsType ?></type>
            <start_date><?= $startDateTime ?></start_date>
            <update_date><?= $event->getUpdated($WAZE_DATE_FORMAT) ?></update_date>
            <end_date><?= $endDateTime ?></end_date>
            <description><?= $description ?></description>
            <location>
                <latitude><?= $point['latitude'] ?></latitude>
                <longitude><?= $point['longitude'] ?></longitude>
            </location>
        </event>
        <?php
        $pointIndex++;
    }

    foreach ($event->getSegments() as $segment) {
        $street = self::escape($segment->getStreet());
        $from   = self::escape($segment->getStreetFrom());
        $to     = self::escape($segment->getStreetTo());
        $dir    = self::escape($segment->getDirection());

        $id = $event->getId();
        if (isset($event->recurrence->id)) { $id .= '-'.$event->recurrence->id; }
        $id .= '-'.$segment->getId();
        ?>
        <event id="<?= $id ?>">
            <type><?= $cifsType ?></type>
            <start_date><?= $startDateTime ?></start_date>
            <update_date><?= $event->getUpdated($WAZE_DATE_FORMAT) ?></update_date>
            <end_date><?= $endDateTime ?></end_date>
            <description><?= $description ?></description>
            <location>
                <street><?= $street ?></street>
                <Direction><?= $dir ?></Direction>
                <latitude><?= $segment->getStartLatitude() ?></latitude>
                <longitude><?= $segment->getStartLongitude() ?></longitude>
                <specify_end>
                    <from_cross_street><?= $from ?></from_cross_street>
                    <end_cross_street><?= $to ?></end_cross_street>
                    <end_latitude><?= $segment->getEndLatitude() ?></end_latitude>
                    <end_longitude><?= $segment->getEndLongitude() ?></end_longitude>
                </specify_end>
            </location>
        </event>
        <?php
    }
}
echo '</events>';
