<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\Database;

require_once GOOGLE.'/autoload.php';

class Event
{
    private $event;
    private $patch;

    public function __construct($event=null)
    {
        if ($event) {
            if ($event instanceof \Google_Service_Calendar_Event) {
                $this->event = $event;
            }
            elseif (is_string($event)) {
                $this->event = GoogleGateway::getEvent(GOOGLE_CALENDAR_ID, $event);
                if (!$this->event) {
                    throw new \Exception('event/unknown');
                }
            }
        }
        else {
            $this->event = new \Google_Service_Calendar_Event();
        }
    }

    public function validate()
    {
    }

    public function save()
    {
        if ($this->patch instanceof \Google_Service_Calendar_Event) {
            $errors = $this->validate();
            if (!count($errors)) {
                GoogleGateway::patchEvent(
                    GOOGLE_CALENDAR_ID,
                    $this->event->id,
                    $this->patch);
            }
            else {
                return $errors;
            }
        }
    }

    public function handleUpdate($post)
    {
        $this->setGeography($post['geography']);
    }

    //---------------------------------------------------------------
    //---------------------------------------------------------------
    public function getId() { return $this->event->id; }
    public function getSummary() { return $this->event->summary; }
    public function getDescription() { return $this->event->description; }

    /**
     * @param string $format
     * @return string
     */
    public function getStart($format=null)
    {
        $date = $this->event->start->datetime
            ?   $this->event->start->datetime
            :   $this->event->start->date;

        if ($format) {
            $d = new \DateTime($date);
            return $d->format($format);
        }
        return $date;
    }

    /**
     * @param string $format
     * @return string
     */
    public function getEnd($format=null)
    {
        if (!$this->event->endTimeUnspecified) {
            $date = $this->event->end->datetime
                ?   $this->event->end->datetime
                :   $this->event->end->date;

            if ($format) {
                $d = new \DateTime($date);
                return $d->format($format);
            }
            return $date;
        }
    }

    /**
     * @return bool
     */
    public function isAllDay()
    {
        return $this->event->start->datetime ? false : true;
    }

    /**
     * @return string
     */
    public function getGeography()
    {
        $properties = $this->event->getExtendedProperties();
        if ($properties) {
            $shared = $properties->getShared();
            if (!empty($shared['geography'])) {
                return $shared['geography'];
            }
        }
    }

    /**
     * @param string $wkt Geometry in Well-Known Text format
     */
    public function setGeography($wkt)
    {
        $new = preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $wkt);
        if ($this->getGeography() != $new) {
            $properties = new \Google_Service_Calendar_EventExtendedProperties();
            $properties->setShared(['geography'=>$new]);

            $this->event->setExtendedProperties($properties);
            if (!$this->patch) {
                 $this->patch = new \Google_Service_Calendar_Event();
            }
            $this->patch->setExtendedProperties($properties);
       }
    }
}