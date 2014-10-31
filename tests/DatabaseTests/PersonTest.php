<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Person;

require_once './DatabaseTestCase.php';

class PersonTest extends DatabaseTestCase
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/people.xml');
	}

	public function testSave()
	{
		$person = new Person();
		$person->setFirstname('First');
		$person->setLastname('Last');
		$person->setEmail('test@localhost');
		$person->save();

		$person = new Person('test@localhost');
		$this->assertEquals('First', $person->getFirstname());
		$this->assertEquals('Last',  $person->getLastname());
		$this->assertEquals('test@localhost',  $person->getEmail());
	}
}
