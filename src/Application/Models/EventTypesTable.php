<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Application\TableGateway;
use Laminas\Db\Sql\Select;

class EventTypesTable extends TableGateway
{
	public function __construct() { parent::__construct('eventTypes', __namespace__.'\EventType'); }

	/**
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param bool $paginated Whether to return a paginator or a raw resultSet
	 * @param int $limit
	 */
	public function find($fields=null, $order='sortingNumber', $paginated=false, $limit=null)
	{
		$select = new Select('eventTypes');
		if ($fields) {
			foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'cifs':
                        $v = $value ? 'is not null' : 'is null';
                        $select->where("cifsType $v");
                    break;

                    default:
                        $select->where([$key=>$value]);
                }
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
