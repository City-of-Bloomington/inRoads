<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Department;
use Application\Models\Event;
use Application\Models\Person;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class AccessControlTest extends PHPUnit_Framework_TestCase
{
    public function testEventEditPrevention()
    {
        $event = new Event();
        $this->assertFalse(Person::isAllowed('events', 'update'));

        $_SESSION['USER'] = new Person();
        $this->assertFalse(Person::isAllowed('events', 'update'));

        $department = new Department();
        $department->setCode('TEST');
        $_SESSION['USER']->setDepartment($department);
        $this->assertFalse(Person::isAllowed('events', 'update'));

        $_SESSION['USER']->setRole('Anonymous');
        $this->assertFalse(Person::isAllowed('events', 'update'));
    }

    public function testEventEditAllowance()
    {
        $department = new Department('PROJ');

        $event = new Event();
        $event->setDepartment($department->getCode());

        $_SESSION['USER'] = new Person();
        $_SESSION['USER']->setRole('Administrator');
        $this->assertTrue(Person::isAllowed('events', 'update'));
        $this->assertTrue($event->permitsEditingBy($_SESSION['USER']));

        $_SESSION['USER']->setRole('Staff');
        $this->assertTrue(Person::isAllowed('events', 'update'));
        $this->assertTrue($event->permitsEditingBy($_SESSION['USER']));

        $_SESSION['USER']->setRole('Public');
        $this->assertTrue(Person::isAllowed('events', 'update'), 'Public user cannot access event/update');
        $this->assertFalse($event->permitsEditingBy($_SESSION['USER']));

        $_SESSION['USER']->setDepartment($department);
        $this->assertTrue($event->permitsEditingBy($_SESSION['USER']));
    }
}
