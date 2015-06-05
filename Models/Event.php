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
    public $event;
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

    /**
     * Saves this event back to a Google Calendar
     *
     * If there is an event_id, it creates a new event
     * otherwise it checks to see if anything has been modified.
     * If fields have been modified, it sends a patch request.
     */
    public function save()
    {
        $errors = $this->validate();
        if (!count($errors)) {
            if (!$this->getId()) {
                $event = GoogleGateway::insertEvent(GOOGLE_CALENDAR_ID, $this->event);
                $this->handleSubmission($event);
            }
            elseif ($this->patch instanceof \Google_Service_Calendar_Event) {
                $event = GoogleGateway::patchEvent(GOOGLE_CALENDAR_ID, $this->event->id, $this->patch);
                $this->handleSubmission($event);
            }
        }
        else {
            return $errors;
        }
    }
    private function handleSubmission($event)
    {
        if ($event instanceof \Google_Service_Calendar_Event) {
            $this->event = $event;
            $this->patch = null;
        }
        else {
            throw new Exception('event/saveError');
        }
    }

    /**
     * @param array $post
     */
    public function handleUpdate($post)
    {
        $this->setDescription($post['description']);
        $this->setGeography($post['geography']);
    }


    //---------------------------------------------------------------
    //---------------------------------------------------------------
    public function getId()
    {
        return !empty($this->event->recurringEventId)
            ? $this->event->recurringEventId
            : $this->event->id;
    }
    public function getSummary() { return $this->event->summary; }
    public function getDescription() { return $this->event->description; }

    /**
     * @param string $format
     * @return string
     */
    public function getStart($format=null)
    {
        if (!empty($this->event->start)) {
            $date = $this->event->start->datetime
                ?   $this->event->start->datetime
                :   $this->event->start->date;

            if ($format) {
                $d = new \DateTime($date);
                return $d->format($format);
            }
            return $date;
        }
    }

    /**
     * @param string $format
     * @return string
     */
    public function getEnd($format=null)
    {
        if (!$this->event->endTimeUnspecified && !empty($this->event->end)) {
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
        return isset($this->event->start->datetime) ? false : true;
    }

    /**
     * @return Google_Service_Calendar_EventExtendedProperties
     */
    public function getExtendedProperties()
    {
        $properties = $this->event->getExtendedProperties();
        if (!$properties instanceof \Google_Service_Calendar_EventExtendedProperties) {
             $properties      = new \Google_Service_Calendar_EventExtendedProperties();
        }
        return $properties;
    }

    /**
     * @return string
     */
    public function getGeography()
    {
        $properties = $this->getExtendedProperties();

        $shared = $properties->getShared();
        if (!empty($shared['geography'])) {
            return $shared['geography'];
        }
    }
    //---------------------------------------------------------------
    // Setters
    //---------------------------------------------------------------
    private function set($field, $value)
    {
        if (is_string($value)) {
            $value = trim($value);
            if ($this->event->$field == $value) {
                // Value has not changed - nothing to update
                return;
            }
        }

        if (!$this->patch) {
            $this->patch = new \Google_Service_Calendar_Event();
        }

        $set = 'set'.ucfirst($field);
        $this->event->$set($value);
        $this->patch->$set($value);
    }

    public function setSummary    ($s) { $this->set('summary',     $s); }
    public function setDescription($s) { $this->set('description', $s); }

    /**
     * @param string $wkt Geometry in Well-Known Text format
     */
    public function setGeography($wkt)
    {
        $new = preg_replace('/[^A-Z0-9\s\(\)\,\-\.]/', '', $wkt);
        if ($this->getGeography() != $new) {
            $properties = $this->getExtendedProperties();
            $properties->setShared(['geography'=>$new]);

            $this->set('extendedProperties', $properties);
       }
    }
}