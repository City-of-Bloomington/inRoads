<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class EventsTable extends TableGateway
{
    protected $columns = ['id', 'eventType', 'severity', 'status', 'jurisdiction_id'];

    public function __construct() { parent::__construct('events', __namespace__.'\Event'); }
}
