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
    protected $columns = ['id', 'eventType'];

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
            'eventType'       => 'eventType',
            'created'         => 'created',
            'updated'         => 'updated',
            'startDate'       => 'startDate',
            'endDate'         => 'endDate',
            'description'     => 'description',
            'geography'             => new Expression('AsText(geography)'),
            'geography_description' => 'geography_description'
        ]);

        if (count($fields)) {
            foreach ($fields as $key=>$value) {
                if (in_array($key, $this->columns)) {
                    $select->where([$key=>$value]);
                }
            }
        }

        return parent::hydrateResults(parent::performSelect($select, $order, $paginated, $limit));
    }
}
