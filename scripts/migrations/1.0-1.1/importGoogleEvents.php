<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

include '../../../configuration.inc';

$FIELDS = [
    'description',
    'end',
    #'endTimeUnspecified',
    'iCalUID',
    'id',
    #'kind',
    #'location',
    #'locked',
    #'originalStartTime',
    #'privateCopy',
    'recurrence',
    'recurringEventId',
    #'sequence',
    #'source',
    'start',
    #'status',
    'summary',
    #'transparency',
    #'updated',
    #'visibility',
    'extendedProperties/shared'
];
#items(description,end,extendedProperties/shared,id,recurrence,recurringEventId,start,summary),nextPageToken

$service = GoogleGateway::getService();
$opts = [
    'fields' => 'items('.implode(',',$FIELDS).'),nextPageToken',
    #'maxResults' => 5
];

$eventCount = 0;
$errorCount = 0;
$pageToken = 1;

$ERROR_LOG = fopen('./errors.log', 'w');

while ($pageToken) {
    if ($pageToken !== 1) { $opts['pageToken'] = $pageToken; }

    $list = $service->events->listEvents(GOOGLE_CALENDAR_ID, $opts);

    foreach ($list as $e) {
        echo "{$e->id} {$e->recurringEventId}\n";
        try {
            $data = GoogleGateway::createLocalEventData($e);
            $event = new Event($data);
            $event->save();
        }
        catch (\Exception $ex) {
            $errorCount++;

            $error = print_r($event, true);
            fwrite($ERROR_LOG, $error);

            echo $error;
        }
        $eventCount++;
    }
    echo "Found $eventCount events\n";
    echo "$errorCount errors\n";
    $pageToken = $list->getNextPageToken();
    echo "Next Page $pageToken\n";
}

fclose($ERROR_LOG);
