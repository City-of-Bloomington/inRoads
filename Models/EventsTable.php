<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class EventsTable extends TableGateway
{
    protected $columns = ['id', 'eventType', 'severity', 'status', 'jurisdiction_id'];

    public function __construct() { parent::__construct('events', __namespace__.'\Event'); }

    /**
     * @param array $fields
     * @param string|array $order Multi-column sort should be given as an array
     * @param bool $paginated Whether to return a paginator or a raw resultSet
     * @param int $limit
     */
    public function find($fields=null, $order='created desc', $paginated=false, $limit=null)
    {
        $select = new Select('events');
        $select->columns([
            'id'              => 'id',
            'jurisdiction_id' => 'jurisdiction_id',
            'eventType'       => 'eventType',
            'severity'        => 'severity',
            'status'          => 'status',
            'created'         => 'created',
            'updated'         => 'updated',
            'headline'        => 'headline',
            'description'     => 'description',
            'detour'          => 'detour',
            'geography'       => new Expression('AsText(geography)')
        ]);

        if (count($fields)) {
            foreach ($fields as $key=>$value) {
                if (in_array($key, $this->columns)) {
                    $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }
}
