<?php
namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StaffTable{
    protected $tableGateway;

    public function __construct(TableGateway $tg){
        $this->tableGateway = $tg;
    }

    public function fetchAll($paginated=false){//获取全部
        if($paginated){
            $select = new Select('base_staff');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new Staff());
            $pageAdapter = new DbSelect($select,$this->tableGateway->getAdapter(),$rs);
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public function fetchAllArr(){
        $resultSet = $this->tableGateway->select();
        return $this->resultSetToArr($resultSet);
    }

    public function getStaff($id){//查询
        $id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('staff_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
            //throw new \Exception("could not find row");
        }
        return $row;
    }

    public function getStaffByCondArr($condArr){//查询
        $rowSet  = $this->tableGateway->select($condArr);
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getStaffLike($partName){
        $sql = "select * from master.base_staff where staff_name like '%".$partName."%'";
        $rowSet = $this->tableGateway->getAdapter()->query($sql)->execute();
        $staffArr = array();
        foreach ($rowSet as $key => $staff) {
            if(!$staff) break;
            $staffArr[] = $staff["staff_id"]."_".$staff["staff_name"];
        }
        return $staffArr;
    }
    public function getStaffByTidArr($tidArr){
        $teachArr = array();
        foreach ($tidArr as $key => $tid) {
            $teacher = $this->getStaff($tid);
            if($teacher){
                $teachArr[$tid] = $teacher->staff_name;
            }
        }
        return $teachArr;
    }
//    public function getStaffByUid($uid){//查询
//        $uid = (int)$uid;
//        $rowSet  = $this->tableGateway->select(array('uid'=>$uid));
//        $row = $rowSet->current();
//        if(!$row){
//            return false;
//            //throw new \Exception("could not find row");
//        }
//        return $row;
//    }
    public function getStaffByCid($cid){//查询
        //$id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('college_id'=>$cid));
        //$row = $rowSet->current();
        //if(!$row){
        //throw new \Exception("could not find row");
        //}
        return $rowSet;
    }
    public function getStaffArrByCid($cid){
        $resultSet = $this->tableGateway->select(array('college_id'=>$cid));
        return $this->resultSetToArr($resultSet);
    }

    public function saveStaff(Staff $staff){//增加 和 修改
        $data = array(
            'staff_id' => $staff->staff_id,
            'staff_name' => $staff->staff_name,
            'college_id'=>$staff->college_id,
            'title' =>$staff->title,
            'phone'=>$staff->phone,
            'cellphone'=>$staff->cellphone,
            'email'=>$staff->email,
            'position'=>$staff->position
        );
        $id =$staff->staff_id;
        if(!$this->getstaff($id)){
            $res =  $this->tableGateway->insert($data);
            if($res)
                return true;
            else
                return false;
        }else{
            $res = $this->tableGateway->update($data,array('staff_id'=>$id));
            if($res >= 0)//当更新数据相同时不认为错误
                return true;
            else
                return false;
        }
    }

    public function getNumOfOneCollege($college_id){
        $cnt = 0;
        $sql = "select count(*) as cnt from master.base_staff where college_id=:college_id";
        $data = array(':college_id'=>$college_id);
        $resultSet = $this->tableGateway->getAdapter()->query($sql,$data);
        if($resultSet){
            foreach ($resultSet as $row) {
                echo "<br/>";
                var_dump($row);
                echo "<br/>";
                $cnt = $row->cnt;//$subArr[] = $row->subject_id;
            }
        }
        return $cnt;
    }

    public function deletestaff($id){//删
        $this->tableGateway->delete(array('staff_id'=>$id));
    }

    public function resultSetToArr($resultSet){
        $staffArr = array();
        foreach ($resultSet as $key => $row) {
            if(!empty($row)){
                $id = $row->staff_id;
                $name = $row->staff_name;
                $staffArr[$id] = $name;
            }
            else echo "empty select result<br/>";
        }
        return $staffArr;
    }
    //lrn
    public function getLastUID(){
        $select = new Select();
        $select ->from('base_staff')
            ->order('staff_id desc')
            ->limit(1);
        $result = $this->tableGateway->selectWith($select);
        $uid = ($row = $result->toArray()) ? $row[0]['staff_id'] : 0;
        return $uid;
    }

    public function getColBySid($id){//根据staff_id返回college_id
        $id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('staff_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
            //throw new \Exception("could not find row");
        }
        return $row->college_id;
    }
}