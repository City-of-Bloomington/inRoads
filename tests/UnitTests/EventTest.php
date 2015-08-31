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
}