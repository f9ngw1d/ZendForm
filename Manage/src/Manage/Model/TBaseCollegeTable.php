<?php
namespace Manage\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Stdlib\Hydrator;

class TBaseCollegeTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->table = 'base_college';
    }
    //检索数据库中 base_college 表所有的记录，然后将结果返回 ResultSet
    public function fetchAll()
    {
        $resultSet= $this->tableGateway->select();
        return $resultSet;
    }
    public function getCollegeNameArr(){//查询所有学院名称
        $resultSet  = $this->tableGateway->select();
        $collegeArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row){
                $collegeArr[] = $row->college_name;
            }
        }
        //$colArr = array_flip($collegeArr);//键/值对反转
        return $collegeArr;
    }
    public function getCollegesIDNameArr(){
        $resultSet = $this->tableGateway->select();
        $collegeArr = array();
        foreach ($resultSet as $key => $row) {
            if(!empty($row)) {
                $collegeArr[$row->college_id] = $row->college_name;
            }
        }
        return $collegeArr;
    }
    public function getCollege($id){//根据cid查询
        //$id = $id;
        $rowSet  = $this->tableGateway->select(array('college_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getCollegebyStaffid($id){//根据cid查询
        //$id = $id;
        $rowSet  = $this->tableGateway->select(array('manager_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            $rowSet  = $this->tableGateway->select(array('dean_id'=>$id));
            $row = $rowSet->current();
        }
        return $row;
    }
    public function saveCollege(TBaseCollege $college)
    {
        $data = array(
            'college_id' => $college->college_id,
            'college_name' => $college->college_name,
            'phone' => $college->phone,
            'ip_address' => $college->ip_address,
            'address' => $college->address,
        );
        //var_dump($data);
        $id = $college->college_id;

        if ($this->getCollegebyStaffid($id)) {
            echo "update<br><br>";
            $this->tableGateway->update($data, array('college_id' => $id));
        } else
        {
            echo "insert<br><br>";
            $this->tableGateway->insert($data);
        }
    }
    public function findAll($limit = 0, $offset = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('base_college' => $this->table));

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
        $select = $sql->select('base_college');
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));;

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $row = $result->current();
        $rowCount = $row['count'];
        return $rowCount;
    }
}