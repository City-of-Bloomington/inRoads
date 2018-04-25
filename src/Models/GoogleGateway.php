<?php
/**
 * Wrapper around Google API calls
 *
 * @see https://developers.google.com/google-apps/calendar/
 * @see https://github.com/google/google-api-php-client
 * @see https://developers.google.com/api-client-library/php/auth/service-accounts
 * @copyright 2015-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;
use Recurr\Rule;

class GoogleGateway
{
    const DATE_FORMAT     = 'Y-m-d';
    const DATETIME_FORMAT = \DateTime::RFC3339;
    const FIELDS = 'description,end,endTimeUnspecified,iCalUID,id,kind,location,locked,originalStartTime,privateCopy,recurrence,recurringEventId,sequence,source,start,status,summary,transparency,updated,visibility,extendedProperties';

    private static function getClient()
    {
        static $client = null;

        if (!$client) {
            $client = new \Google_Client();
            $client->setAuthConfig(GOOGLE_CREDENTIALS_FILE);
            $client->setScopes([\Google_Service_Calendar::CALENDAR]);
            $client->setSubject(GOOGLE_USER_EMAIL);
        }
        return $client;
    }

    public static function getService()
    {
        static $service;

        if (!$service) {
            $service = new \Google_Service_Calendar(self::getClient());
        }
        return $service;
    }

    /**
     * @see https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list
     * @return CalendarList
     */
    public static function getCalendars()
    {
        $service = self::getService();
        return $service->calendarList->listCalendarList();
    }

    /**
     * @see https://developers.google.com/google-apps/calendar/v3/reference/events/list
     * @param string $calendarId
     * @param DateTime $start
     * @param DateTime $end
     * @return array An array of Application\Model\Events
     */
    public static function getEvents($calendarId, \DateTime $start=null, \DateTime $end=null, array $filters=null, $singleEvents=null)
    {
        $events = [];

        $service = self::getService();

        $opts = [
            'fields'       => 'items('.self::FIELDS.')',
            'singleEvents' => $singleEvents
        ];

        if ($start) { $opts['timeMin'] = $start->format(self::DATETIME_FORMAT); }
        if ($end  ) { $opts['timeMax'] = $end  ->format(self::DATETIME_FORMAT); }

        $events = [];
        // Get all the events from Google Calendar for the date range
        $list = $service->events->listEvents($calendarId, $opts);

        // Loop through and filter out the events that don't match the filters provided
        foreach ($list as $e) {
            $event = new Event($e);

            if (!empty($filters['eventTypes'])) {
                $t = $event->getEventType();
                if (!$t || !in_array($t->getCode(), $filters['eventTypes'])) {
                    continue;
                }
            }
            $events[] = $event;
        }

        // Sort by datetime
        usort($events, function ($a, $b) {
            if ($a->getStartDate() == $b->getStartDate()) { return 0; }
            return ($a->getStartDate() < $b->getStartDate()) ? -1 : 1;
        });
        return $events;
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     * @return Event
     */
    public static function getEvent($calendarId, $eventId)
    {
        $service = self::getService();
        return $service->events->get($calendarId, $eventId);
    }

    /**
     * @param string $calendarId
     * @param Google_Service_Calendar_Event $event
     * @return Google_Service_Calendar_Event
     */
    public static function insertEvent($calendarId, \Google_Service_Calendar_Event $event)
    {
        $service = self::getService();
        return $service->events->insert($calendarId, $event);
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     * @param Google_Service_Calendar_Event $patch
     * @return Google_Service_Calendar_Event
     */
    public static function patchEvent($calendarId, $eventId, \Google_Service_Calendar_Event $patch)
    {
        $service = self::getService();
        return $service->events->patch($calendarId, $eventId, $patch);
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     */
    public static function deleteEvent($calendarId, $eventId)
    {
        $service = self::getService();
        $service->events->delete($calendarId, $eventId);
    }

    /**
     * Returns an internal data array for this event
     *
     * The return value is the data array to instantiate an
     * Application\Models\Event
     *
     * @return array
     */
    public static function createLocalEventData(\Google_Service_Calendar_Event $e)
    {
        $data = [];
        $data['google_event_id'] = !empty($e->recurringEventId) ? $e->recurringEventId : $e->id;
        $data['location'       ] = $e->location;
        $data['description'    ] = $e->description;

        self::parseSummary($data, $e);
        self::parseDates  ($data, $e);

        $properties = $e->getExtendedProperties();
        if ($properties) {
            $shared = $properties->getShared();
            if (!empty($shared['geography'])  && strlen($shared['geography'])<=1000) {
                $data['geography'] = $shared['geography'];
            }
        }

        return $data;
    }

   /**
    * Parses structured data out of the summary string
    */
    private static function parseSummary(array &$data, \Google_Service_Calendar_Event &$e)
    {
        $d = implode('|', Department::codes());
        if (preg_match("/^($d)(\s+)?-/i", $e->getSummary(), $matches)) {
            try {
                $department = new Department(strtoupper($matches[1]));
                $data['department_id'] = $department->getId();
            }
            catch (\Exception $e) {
            }
        }

        $d = implode('|', EventType::names());
        if (preg_match("/$d/i", $e->getSummary(), $matches)) {
            $name = ucwords(strtolower($matches[0]));
            $sql = 'select id from eventTypes where name=?';
            $zend_db = Database::getConnection();
            $result = $zend_db->query($sql, [$name]);
            if (count($result)) {
                $row = $result->current();
                $data['eventType_id'] = $row['id'];
            }
        }

        if (preg_match('/-([^-]+)$/', $e->getSummary(), $matches)) {
            $data['title'] = trim($matches[1]);
        }
        else {
            $data['title'] = $e->getSummary();
        }
    }

    /**
     * Populates date time information
     *
     * Writes startDate, startTime, endDate, endTime and rrule
     * into the provided data array from the Google Event.
     *
     * @param array $data
     * @param Google_Service_Calendar_Event $e
     */
    public static function parseDates(array &$data, \Google_Service_Calendar_Event &$e)
    {
        if ($e->start->dateTime) {
            $d = new \DateTime($e->start->dateTime);
            $data['startDate'] = $d->format(ActiveRecord::MYSQL_DATE_FORMAT);
            $data['startTime'] = $d->format(ActiveRecord::MYSQL_TIME_FORMAT);

            $d = new \DateTime($e->end->dateTime);
            $data['endDate']   = $d->format(ActiveRecord::MYSQL_DATE_FORMAT);
            $data['endTime']   = $d->format(ActiveRecord::MYSQL_TIME_FORMAT);
        }
        else {
            // All Day Event
            $d = new \DateTime($e->start->date);
            $data['startDate'] = $d->format(ActiveRecord::MYSQL_DATE_FORMAT);

            $d = new \DateTime($e->end->date.' 23:59:59');
            $data['endDate']   = $d->format(ActiveRecord::MYSQL_DATETIME_FORMAT);
        }
        if ($e->recurrence) {
            foreach ($e->recurrence as $r) {
                if (strpos($r, 'RRULE:') !== false) {
                    $rrule = substr($r, 6);
                    $rule = new Rule($rrule);
                    $data['rrule'] = $rule->getString(Rule::TZ_FIXED);
                }
            }
        }
    }
}
