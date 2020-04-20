<?php

namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PermissionTable{
    protected $tableGateway;

    public function __construct(TableGateway  $tg)
    {
        $this->tableGateway = $tg;
    }
    public function fetchAll($paginated = false){//获取表中全部数据
        if($paginated){
            $select = new Select('usr_permission');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new Permission());
            $pageAdapter = new DbSelect($select,$this->tableGateway->getAdapter);
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public  function getPermission($id){ //单行检索数据
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('pid'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getPermissionArr($pidArr){
        $purlArr = array();
        if($pidArr){
            foreach ($pidArr as $key => $pid){
                $rowSet = $this->tableGateway->select(array('pid'=>$pid));
                $row = $rowSet->current();
                if(!$row){
                    continue;
                }
                $purl = $row->name;
                $purlArr[$purl]=1;

            }
        }
        return $purlArr;

    }
    public function getPermissionStringArrByPidArr($pidArr){
        $purlArr = array();
        $purlSegArr = array();
        if($pidArr){
            foreach ($pidArr as $key => $pid) {
                //echo "<br/><br/>pid = $pid<br/>";
                $rowSet = $this->tableGateway->select(array('pid'=>$pid));
                $row = $rowSet->current();
                if(!$row){
                    continue;
//                    throw new \Exception("找不到 pid=$pid 对应的权限");
                }
                $purl = $row->name;
                $purlArr[$pid]=$purl;
                //print_r($purlArr);

                $purlSeg = explode("/", $purl);
                switch (sizeof($purlSeg)) {
                    case 1://只有module名，整个module都有权限
                        $purlSegArr[$purlSeg[0]] = 1;
                        break;
                    case 2://modeule/action
                        if(isset($purlSegArr[$purlSeg[0]])){
                            if(!is_array($purlSegArr[$purlSeg[0]]))
                                break;
                        }
                        $purlSegArr[$purlSeg[0]][$purlSeg[1]] = 1;
                        break;
                    case 3://module/controller/action
                        if(isset($purlSegArr[$purlSeg[0]])){
                            if(!is_array($purlSegArr[$purlSeg[0]]))
                                break;
                            else if(isset($purlSegArr[$purlSeg[0]][$purlSeg[1]])){
                                if(!is_array($purlSegArr[$purlSeg[0]][$purlSeg[1]]))
                                    break;
                            }
                        }
                        $purlSegArr[$purlSeg[0]][$purlSeg[1]][$purlSeg[2]] = 1;
                    default: break;
                }
            }
        }
        //return $purlArr;
        // echo "<br/><br/>";
        // print_r($purlSegArr);
        // echo "<br/><br/>";
        return $purlSegArr;
    }
    public function savePermission(Permission $permission){
        $data = array(
            'name' => $permission->name
        );
        $id = (int) $permission->id;
        if($id == 0){
            $this->tableGateway->insert($data);
        }else{
            if($this->getPermission($id)){
                $this->tableGateway->update($data,array('pid'=>$id));
            }else{
                throw new \Exception("could not find now {id}");
            }
        }
    }
    public function deleteRolepermission($id){//删
        if((!empty($id['pid']))&&(!empty($id['pid']))) {
            $pid = (int)$id['pid'];
            $rid = (int)$id['rid'];
            $this->tableGateway->delete(array('pid'=>$pid,'rid'=>$rid));
        }else if((!empty($id['pid']))&&(empty($id['rid']))){
            $id = (int)$id['pid'];
            $this->tableGateway->delete(array('pid'=>$id));
        }else if((empty($id['pid']))&&(!empty($id['rid']))){
            $id = (int)$id['rid'];
            $this->tableGateway->delete(array('rid'=>$id));
        }else{
            throw new \Exception("could not delete with no pid and rid");
        }
    }
}
