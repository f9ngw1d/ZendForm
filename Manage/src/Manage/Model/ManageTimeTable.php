<?php


namespace Manage\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Stdlib\Hydrator;
use Manage\Model\Managetime;

class ManageTimeTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->table = 'manage_time';
    }

    public function find($name)
    {
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function findid($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
//            throw new \Exception("Could not find row $id");
            return false;
        }
        return $row;

    }

    public function findAll($limit = 0, $offset = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('manage_time' => $this->table));

        if ($limit != 0) {
            $sl->limit($limit);
        }
        if ($offset != 0) {
            $sl->offset($offset);
        }

        $stmt = $sql->prepareStatementForSqlObject($sl);
        $result = $stmt->execute();


        $resultArr = iterator_to_array($result);
        return $resultArr;

    }

    public function getTotalnum()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('manage_time');
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));;

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $row = $result->current();
        $rowCount = $row['count'];
        return $rowCount;
    }

    public function saveTime(Managetime $time)
    {
        //echo "save=>";
        $data = array(
            'name' => $time->name,
            'start_time' => $time->start_time,
            'end_time' => $time->end_time,
            'description' => $time->description,
            'status' => $time->status,
        );
        //var_dump($data);
        $name = $time->name;

        if ($this->find($name)) {
//            echo "update<br><br>";
            $this->tableGateway->update($data, array('name' => $name));
        } else
        {
//            echo "insert<br><br>";
            $this->tableGateway->insert($data);
        }
    }

    public function getTimeSta($id)
    {
        //var_dump($name);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('manage_time' => $this->table))
            ->where(array('id = ?' => $id));

        $statement = $sql->prepareStatementForSqlObject($sl);
        $resultset = $statement->execute();
        $resultArr = iterator_to_array($resultset);

        foreach ($resultArr as $key => $value)
        {
            if ($value['status'] == 1)
                return 1;
            else
            {
                $date = date("Y-m-d H:i:s");
                if (strtotime($date)<strtotime($value['end_time']))
                    return 1;
                else
                    return 0;
            }
        }


    }
}