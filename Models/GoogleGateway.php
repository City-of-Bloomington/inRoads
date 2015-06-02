<?php
/**
 * Wrapper around Google API calls
 *
 * @see https://developers.google.com/google-apps/calendar/
 * @see https://github.com/google/google-api-php-client
 * @see https://developers.google.com/api-client-library/php/auth/service-accounts
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

require_once GOOGLE.'/autoload.php';

class GoogleGateway
{
    const DATETIME_FORMAT = \DateTime::RFC3339;
    const FIELDS = 'description,end,endTimeUnspecified,iCalUID,id,kind,location,locked,originalStartTime,privateCopy,recurrence,recurringEventId,sequence,source,start,status,summary,transparency,updated,visibility,extendedProperties';

    private static $service;

    private static function getService()
    {
        if (!self::$service) {
            $json = json_decode(file_get_contents(GOOGLE_CREDENTIALS_FILE));
            $credentials = new \Google_Auth_AssertionCredentials(
                $json->client_email,
                ['https://www.googleapis.com/auth/calendar'],
                $json->private_key
            );
            $credentials->sub = GOOGLE_USER_EMAIL;

            $client = new \Google_Client();
            $client->setAssertionCredentials($credentials);
            if ($client->getAuth()->isAccessTokenExpired()) {
                $client->getAuth()->refreshTokenWithAssertion();
            }

            self::$service = new \Google_Service_Calendar($client);

            //$calendar = new \Google_Service_Calendar($client);
        }
        return self::$service;
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
     * @return EventList
     */
    public static function getEvents($calendarId, \DateTime $start=null, \DateTime $end=null)
    {
        $events = [];

        $service = self::getService();

        $opts = [
            'fields'  => 'items('.self::FIELDS.')',
            'orderBy' => 'startTime',
            'singleEvents' => true
        ];
        if ($start) { $opts['timeMin'] = $start->format(self::DATETIME_FORMAT); }
        if ($end  ) { $opts['timeMax'] = $end  ->format(self::DATETIME_FORMAT); }

        return $service->events->listEvents($calendarId, $opts);
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
     * @param string $eventId
     * @param Google_Service_Calendar_Event $patch
     */
    public static function patchEvent($calendarId, $eventId, $patch)
    {
        $service = self::getService();
        $service->events->patch($calendarId, $eventId, $patch);
    }
}