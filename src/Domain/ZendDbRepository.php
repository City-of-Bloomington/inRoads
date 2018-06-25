<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Domain;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

abstract class ZendDbRepository
{
    const DATE_FORMAT = 'Y-m-d';

    protected $zend_db;
    protected $sql;

    public function __construct(Adapter $zend_db)
    {
        $this->zend_db = $zend_db;
        $this->sql     = new Sql($this->zend_db);
    }

	public function performSelect(Select $select, int $itemsPerPage=null, int $currentPage=null) : array
	{
        $rows  = [];
        $total = null;

        if ($itemsPerPage) {
            $currentPage = $currentPage ? $currentPage : 1;


            $string = $this->sql->buildSqlString($select);
            $c      = "select count(*) as count from ($string) o";
            $result = $this->zend_db->query($c)->execute();
            $row    = $result->current();
            $total  = (int)$row['count'];

            $select->limit ($itemsPerPage);
            $select->offset($itemsPerPage * ($currentPage-1));
        }

        $query   = $this->sql->prepareStatementForSqlObject($select);
        $results = $query->execute();
        foreach ($results as $row) { $rows[] = $row; }

        return [
            'rows'  => $rows,
            'total' => $total
        ];
	}

	protected function saveToTable(array $data, string $table, ?string $pk='id'): int
	{
		$sql = new Sql($this->zend_db, $table);
		if (!empty($data[$pk])) {
            // Update
            $id = $data[$pk];
            unset($data[$pk]);

			$update = $sql->update()
				->set($data)
				->where([$pk=>$id]);
			$sql->prepareStatementForSqlObject($update)->execute();
			return (int)$id;
		}
		else {
            // Insert
            unset($data[$pk]);

			$insert = $sql->insert()->values($data);
			$sql->prepareStatementForSqlObject($insert)->execute();
			return (int)$this->zend_db->getDriver()->getLastGeneratedValue();
		}
	}

	public function distinctFromTable(string $field, string $table): array
	{
        $rows   = [];
        $select = $this->sql->select()
                       ->quantifier('DISTINCT')
                       ->columns([$field])
                       ->from($table)
                       ->where("$field is not null")
                       ->order($field);
        $result = $this->sql->prepareStatementForSqlObject($select)->execute();
        foreach ($result as $row) { $rows[] = $row; }
        return $rows;
	}

    protected function doQuery(string $sql, ?array $params=null): array
    {
        $rows   = [];
        $query  = $this->zend_db->createStatement($sql, $params);
        $result = $query->execute();
        foreach ($result as $row) { $rows[] = $row; }
        return $rows;
    }
}
