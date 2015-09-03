<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Event;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class EventTest extends PHPUnit_Framework_TestCase
{
    public function testSetDateOnly()
    {
        $dateString = '2/4/2014';
        $mysqlDate  = '2014-02-04';

        $event = new Event();
        $event->setStartDate($dateString, DATE_FORMAT);
        $event->setEndDate  ($dateString, DATE_FORMAT);

        $this->assertEquals($mysqlDate, $event->getStartDate());
        $this->assertEquals($mysqlDate, $event->getEndDate());
    }

    public function testSetTimeOnly()
    {
        $timeString = '2:00pm';
        $mysqlTime  = '14:00:00';

        $event = new Event();
        $event->setStartTime($timeString, TIME_FORMAT);
        $event->setEndTime  ($timeString, TIME_FORMAT);

        $this->assertEquals($mysqlTime, $event->getStartTime());
        $this->assertEquals($mysqlTime, $event->getEndTime());
    }

    public function testIsAllDay()
    {
        $dateString = '2/4/2014';
        $timeString = '2:00pm';

        $event = new Event();
        $event->setStartDate($dateString, DATE_FORMAT);
        $this->assertTrue($event->isAllDay(), 'Event without a start time should be All Day');

        $event->setStartTime($timeString, TIME_FORMAT);
        $this->assertFalse($event->isAllDay(), 'Event with a start time should not be All Day');
    }

    public function testGetFullDatetime()
    {
        $dateString = '2/4/2014';
        $timeString = '2:00pm';

        $event = new Event();
        $event->setStartDate($dateString, DATE_FORMAT);
        $event->setEndDate  ($dateString, DATE_FORMAT);

        $this->assertEquals($event->getStart('c'), '2014-02-04T00:00:00-05:00');
        $this->assertEquals($event->getEnd  ('c'), '2014-02-04T00:00:00-05:00');

        $event->setStartTime($timeString, TIME_FORMAT);
        $event->setEndTime  ($timeString, TIME_FORMAT);
        $this->assertEquals($event->getStart('c'), '2014-02-04T14:00:00-05:00');
        $this->assertEquals($event->getEnd  ('c'), '2014-02-04T14:00:00-05:00');
    }
}