<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

class EventType
{
    private $data;

    /**
     * @return array  An array of EventType objects
     */
    public static function types()
    {
        $types = [];

        global $EVENT_TYPES;
        foreach ($EVENT_TYPES as $t) {
            $types[] = new EventType($t);
        }
        return $types;
    }

    /**
     * @return array An array of name strings
     */
    public static function names()
    {
        $names = [];

        global $EVENT_TYPES;
        foreach ($EVENT_TYPES as $t) {
            $names[] = $t['name'];
        }
        return $names;
    }

    /**
     * @return array An array of code strings
     */
    public static function codes()
    {
        $codes = [];

        global $EVENT_TYPES;
        foreach ($EVENT_TYPES as $t) {
            $codes[] = $t['code'];
        }
        return $codes;
    }

    /**
     * Loads type information
     *
     * If you pass in an array, it uses the data you provide.
     * If you pass in a string, it looks for a type with that code or name
     * @param array|string $data
     */
    public function __construct($data=null)
    {
        if ($data) {
            if (is_array($data)) {
                $this->data = $data;
            }
            else {
                global $EVENT_TYPES;
                foreach ($EVENT_TYPES as $t) {
                    if ($t['code'] === $data || $t['name'] === $data) {
                        $this->data = $t;
                        break;
                    }
                }
            }
            if (!$this->data) {
                throw new \Exception('eventType/unknown');
            }
        }
    }

    public function getCode           () { return $this->data['code'];        }
    public function getName           () { return $this->data['name'];        }
    public function getColor          () { return $this->data['color'];       }
    public function getDescription    () { return $this->data['description']; }
    public function isDefaultForSearch() { return $this->data['default'];     }

    public function __toString() { return $this->getName(); }
}