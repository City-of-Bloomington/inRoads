<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Event;
use Application\Models\Department;
use Domain\Users\Entities\User;

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /**
     * Make sure dates are set and returned as \DateTime objects
     */
    public function testSetDates()
    {
        $event    = new Event();
        $testDate = new \DateTime('2014-02-02');
        $testTime = \DateTime::createFromFormat('H:i', '13:42');

        $event->setStartDate(null);
        $this->assertEquals (null, $event->getStartDate());

        $event->setStartDate($testDate);
        $this->assertEquals ($testDate, $event->getStartDate());

        $event->setEndDate (null);
        $this->assertEquals(null, $event->getEndDate());

        $event->setEndDate ($testDate);
        $this->assertEquals($testDate, $event->getEndDate());

        $event->setStartTime(null);
        $this->assertEquals (null, $event->getStartTime());

        $event->setStartTime($testTime);
        $this->assertEquals ($testTime, $event->getStartTime());

        $event->setEndTime (null);
        $this->assertEquals(null, $event->getEndTime());

        $event->setEndTime ($testTime);
        $this->assertEquals($testTime, $event->getEndTime());
    }

    public function testIsAllDay()
    {
        $testDate = new \DateTime('2014-02-04');
        $testTime = \DateTime::createFromFormat('H:i', '14:00');


        $event = new Event();
        $event->setStartDate($testDate);
        $this->assertTrue($event->isAllDay(), 'Event without a start time should be All Day');

        $event->setStartTime($testTime);
        $this->assertFalse($event->isAllDay(), 'Event with a start time should not be All Day');
    }

    public function testGetFullDatetime()
    {
        $testDate = new \DateTime('2014-02-04');
        $testTime = \DateTime::createFromFormat('H:i', '14:00');

        $event = new Event();
        $event->setStartDate($testDate);
        $event->setEndDate  ($testDate);

        $targetStart = new \DateTime('2014-02-04');
        $targetEnd   = new \DateTime('2014-02-04 23:59:59');

        $this->assertEquals($targetStart, $event->getStart());
        $this->assertEquals($targetEnd,   $event->getEnd());

        $event->setStartTime($testTime);
        $event->setEndTime  ($testTime);

        $target = new \DateTime('2014-02-04 14:00:00');
        $this->assertEquals($target, $event->getStart());
        $this->assertEquals($target, $event->getEnd());
    }

    public function testEditPermissions()
    {
        $department = new Department(['id'=>2, 'name'=>'Stub Department']);
        $otherDept  = new Department(['id'=>3, 'name'=>'Other Stub Department']);
        $user       = new User();
        $event      = new Event();

        $this->assertFalse($event->permitsEditingBy($user));

        $user->role = 'Administrator';
        $this->assertTrue($event->permitsEditingBy($user), 'Administrators cannot edit events');

        $user->role = 'Staff';
        $this->assertTrue($event->permitsEditingBy($user), 'Staff cannot edit events');

        $user->role = 'Public';
        $user->department_id = $department->getId();
        $this->assertTrue($event->permitsEditingBy($user), 'Public users cannot create events');

        $event->setDepartment($otherDept);
        $this->assertFalse($event->permitsEditingBy($user), 'Public users can edit other department\'s events');

        $event->setDepartment($department);
        $this->assertTrue($event->permitsEditingBy($user), 'Public users cannot edit events for their own department');
    }
}
