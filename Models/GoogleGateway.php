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

require GOOGLE.'/autoload.php';

class GoogleGateway
{
    const DATETIME_FORMAT = 'c';
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
     * @return EventList
     */
    public static function getEvents($calendarId)
    {
        $service = self::getService();
        return $service->events->listEvents($calendarId);
    }
}