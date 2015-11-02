<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Event;
use Application\Models\Department;
use Application\Models\Person;

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

        $targetStart = new \DateTime('2014-02-04');
        $targetEnd   = new \DateTime('2014-02-04 23:59:59');

        $this->assertEquals($event->getStart('c'), $targetStart->format('c'));
        $this->assertEquals($event->getEnd  ('c'), $targetEnd  ->format('c'));

        $event->setStartTime($timeString, TIME_FORMAT);
        $event->setEndTime  ($timeString, TIME_FORMAT);
        $this->assertEquals($event->getStart('c'), '2014-02-04T14:00:00-05:00');
        $this->assertEquals($event->getEnd  ('c'), '2014-02-04T14:00:00-05:00');
    }

    public function testEditPermissions()
    {
        $department = new Department(['id'=>2, 'name'=>'Stub Department']);
        $otherDept  = new Department(['id'=>3, 'name'=>'Other Stub Department']);
        $person     = new Person();
        $event      = new Event();

        $this->assertFalse($event->permitsEditingBy($person));

        $person->setRole('Administrator');
        $this->assertTrue($event->permitsEditingBy($person), 'Administrators cannot edit events');

        $person->setRole('Staff');
        $this->assertTrue($event->permitsEditingBy($person), 'Staff cannot edit events');

        $person->setRole('Public');
        $person->setDepartment($department);
        $this->assertTrue($event->permitsEditingBy($person), 'Public users cannot create events');

        $event->setDepartment($otherDept);
        $this->assertFalse($event->permitsEditingBy($person), 'Public users can edit other department\'s events');

        $event->setDepartment($department);
        $this->assertTrue($event->permitsEditingBy($person), 'Public users cannot edit events for their own department');
    }

    public function testRequiredFields()
    {
        $start = '10/20/2015';
        $end   = '10/30/2015';
        $title = 'Title';
        $desc  = 'Description';
        $geo   = 'Geography Description';


        $event = new Event();
        $event->setStartDate($start);
        $event->setEndDate($end);
        $event->setDescription($desc);
        $error = $event->validate();
        $this->assertNotNull($error);

        $event->setTitle($title);
        $error = $event->validate();
        $this->assertNull($error);
    }

    public function testDescriptionLengthValidation()
    {
        $start = '10/20/2015';
        $end   = '10/30/2015';
        $title = 'Title';
        $geo   = 'Geography Description';
        $desc  = 'Description';

        $desc = '';
        $length = Event::MAX_DESCRIPTION_LENGTH + 1;
        for ($i=0; $i<=$length; $i++) { $desc.='x'; }

        $event = new Event();
        $event->setStartDate($start);
        $event->setEndDate($end);
        $event->setTitle($title);
        $event->setDescription($desc);
        $error = $event->validate();

        $this->assertNotNull($error);
        $this->assertEquals('description_length', $error->getMessage());
    }
}