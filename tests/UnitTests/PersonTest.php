<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Person;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class PersonTest extends PHPUnit_Framework_TestCase
{
	public function testGetFullName()
	{
		$person = new Person();
		$person->setFirstname('First');
		$person->setLastname('Last');
		$this->assertEquals('First Last', $person->getFullname());
	}

	public function testAuthenticationMethodDefaultsToLocal()
	{
		$person = new Person();
		$person->setFirstname('First');
		$person->setLastname('Last');
		$person->setEmail('test@localhost');
		$person->setUsername('test');
		$person->validate();

		$this->assertEquals('local', $person->getAuthenticationMethod());
	}
}
