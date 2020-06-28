<?php
namespace Manage\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class TDbUnderSubjectTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    //检索数据库中 db_under_subject表所有的记录，然后将结果返回 ResultSet
    /*
    public function fetchAll()
    {
        $resultSet= $this->tableGateway->select();
        return $resultSet;
    }
    */
    public function fetchAll($paginated = false, $column = NULL, $value = NULL)
    {
        if ($paginated) {
            // create a new Select object for the table album
            $select = new Select('db_under_subject');
            // create a new result set based on the Album entity
            if (isset($column)&&!empty($column)) {
                if($column=='id'){
                    $select->where(array('id'=>$value));
                }
                elseif ($column='name'){
                    $select->where->like($column, '%'.$value.'%');
                }
            }
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new TDbUnderSubject());
            // create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
            // our configured select object
                $select,
                // the adapter to run it against
                $this->tableGateway->getAdapter(),
                // the result set to hydrate
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public function getUnderSubject($id){//查询
        $id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
            //throw new \Exception("could not find row");
        }
        return $row;
    }
    public function getAllSubjectArr()//获取全部学科门类
    {
        $collegeArr=array();
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl ->from('db_under_subject')
            ->columns(array('id'=>'id','name'=>'name'))
            ->where('length(id) =2');
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);
        if($result_arr){
            foreach ($result_arr as $key => $row){
                $id = $row['id'];
                $collegeArr[$id] = $row['name'];
            }
        }
        return $collegeArr;
    }
    public function getRelation1($relation1){//查询
        $resultSet  = $this->tableGateway->select(array('relation1'=>$relation1));
        $classArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $classArr[$row->id] = $row->name;
            }
        }
        return $classArr;
    }

    public function getRelation2($relation2){//查询
        $resultSet  = $this->tableGateway->select(array('relation2'=>$relation2));
        $proArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $proArr[$row->id] = $row->name;
            }
        }
        return $proArr;
    }

    public function getSubject(){//查询
        $resultSet = $this->tableGateway->select(array('relation1'=>'','relation2'=>''));
        $subArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                //if(is_object($row))
                $subArr[$row->id] = $row->name;
            }
        }
        return $subArr;
    }
public function getUnderSubjectByid($id){
    $rowSet  = $this->tableGateway->select(array('id'=>$id));
    $row = $rowSet->current();
    if(!$row){
        return false;
        //throw new \Exception("could not find row");
    }
    return $row;
}
    public function saveUnderSubject($data){
        $row = $this->getUnderSubjectByid($data['id']);
        if(!$row){
            $this->tableGateway->insert($data);
            //echo "insert success!<br/>";
            return true;
        }else{
            if($row){
                $this->tableGateway->update($data,array('id'=>$data['id']));
                //echo "update success!<br/>";
                return true;
            }else{
                return false;
            }
        }

    }
    public function deleteUnderSubject($id){//删
        $this->tableGateway->delete(array('id'=>$id));
    }
    public function getProclassByCid($cid){//根据本科学科门类id查询本科专业类
        $resultSet  = $this->tableGateway->select(array('relation1'=>$cid));
        $classArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $classArr[$row->id] = $row->name;
            }
        }
        return $classArr;
    }
    public function getProByPcid($pid){
        $resultSet  = $this->tableGateway->select(array('relation2'=>$pid));
        $proArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $proArr[$row->id] = $row->name;
            }
        }
        return $proArr;
    }
}
?>