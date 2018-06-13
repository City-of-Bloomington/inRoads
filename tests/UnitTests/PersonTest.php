<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
	public function testGetFullName()
	{
		$person = new Person();
		$person->setFirstname('First');
		$person->setLastname('Last');
		$this->assertEquals('First Last', $person->getFullname());
	}
}
