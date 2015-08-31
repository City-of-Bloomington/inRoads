<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\ActiveRecord;

$_SERVER['SITE_HOME'] = __DIR__;
require_once realpath(__DIR__.'/../../../configuration.inc');

class ActiveRecordTest extends PHPUnit_Framework_TestCase
{
	private $testModel;

	public function __construct()
	{
		$this->testModel = new TestModel();
	}

	public function testGetAndSet()
	{
		$this->testModel->set('testField', 'testValue');
		$this->assertEquals('testValue', $this->testModel->get('testField'));
	}

	public function testGetAndSetDate()
	{
		$dateString = '2012-01-01 01:23:43';
		$this->testModel->setDate('testField', $dateString);
		$this->assertEquals($dateString, $this->testModel->getDate('testField'));
	}

	public function testSetDateNow()
	{
        $dateString = 'now';
        $this->testModel->setDate('testField', $dateString);
        $this->assertEquals(date('Y-m-d'), $this->testModel->getDate('testField', 'Y-m-d'));
	}

	public function testSetDatePortionOnly()
	{
        $dateString = '2/14/2014';
        $mysqlDate  = '2014-02-14';

        $this->testModel->setDateOnly('testField', $dateString);
        $this->assertEquals($mysqlDate, $this->testModel->get('testField'));
	}

	public function testSetTimePortionOnly()
	{
        $timeString = '2:00pm';
        $mysqlTime  = '14:00:00';

        $this->testModel->setTimeOnly('testField', $timeString);
        $this->assertEquals($mysqlTime, $this->testModel->get('testField'));
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage invalidDate
	 */
	public function testSetDateInvalidFormat()
	{
        $dateString = '12390481290/asjdk';
        $this->testModel->setDate('testField', $dateString);
	}

	public function testDateFormat()
	{
		$dateString = '1/3/2013 01:23:43';
		$this->testModel->setDate('testField', $dateString);
		$this->assertEquals('Jan 3rd 2013', $this->testModel->getDate('testField', 'M jS Y'));
	}

	public function testRawDateDataIsMySQLFormat()
	{
		$dateString = '1/3/2013 01:23:43';
		$mysqlDate = '2013-01-03 01:23:43';

		$this->testModel->setDate('testField', $dateString);
		$this->assertEquals($mysqlDate, $this->testModel->getDate('testField'));
	}

	public function testForeignKeyObject()
	{
		$this->testModel->setTestModel(new TestModel(1));
		$o = $this->testModel->getTestModel();
		$this->assertEquals(1, $o->get('id'));
	}
}

/**
 * A test model implementing the ActiveRecord class
 *
 * This model represents a Model class that a developer
 * would write that would need to read and write to the
 * database.
 *
 * We can't instaniate the ActiveRecord directly.
 */
class TestModel extends Blossom\Classes\ActiveRecord
{
	protected $foreignkey;

	public function __construct($id=null)
	{
		if ($id) { parent::set('id', $id); }
	}

	public function validate() { }

	public function getId() { return parent::get('id'); }

	/**
	 * Retrive raw values from test fields
	 *
	 * This way we can check to see that data was set correctly.
	 * This is needed because the $data array is protected
	 *
	 * @param string $field
	 * @return string
	 */
	public function get($field)  { return parent::get($field); }
	public function set($field, $value) { parent::set($field, $value); }


	public function getDate($field, $format=null, \DateTimeZone $timezone=null)
	{
		return parent::getDateData($field, $format, $timezone);
	}

	public function setDate    ($field, $date) { parent::setDateData($field, $date); }
	public function setDateOnly($field, $date) { parent::setDateData($field, $date, DATE_FORMAT, ActiveRecord::MYSQL_DATE_FORMAT); }
	public function setTimeOnly($field, $time) { parent::setDateData($field, $time, TIME_FORMAT, ActiveRecord::MYSQL_TIME_FORMAT); }

	public function getTestModel()      { return parent::getForeignKeyObject('TestModel', 'foreignkey_id'); }
	public function setTestModel(TestModel $o) { parent::setForeignKeyObject('TestModel', 'foreignkey_id', $o); }
}
