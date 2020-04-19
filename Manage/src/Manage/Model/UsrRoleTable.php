<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/25
 * Time: 16:00
 */
namespace Manage\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UsrRoleTable{//usrRole表 只能增删查，不能修改
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }

    public function fetchAll($paginate = false){
        if($paginate){
            $select = new Select('usr_user_role');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new Userrole());
            $pageAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
    }
    public  function getUserrole($conArr){ //通过uid 查询rid
        //传入一个数组 attay $id ,两个字段 uid 和rid
        $rowSet = $this->tableGateway->select($conArr);
        if(!$rowSet){
            return false;
        }
        return $rowSet;


    }
    public function saveUserrole(Userrole $Userrole){//增加
        $data = array(
            'rid' => $Userrole->rid,
            'uid' => $Userrole->uid
        );
        $resultSet = $this->getUserrole($data);
        $row = $resultSet->current();
        if($Userrole->rid && $Userrole->uid && !$row){
            //echo "<br/>插入新的userrole <br/>";
            $this->tableGateway->insert($data);
            return true;
        }
        elseif($row){
            //echo "数据库中有这对uid rid<br/>";
            return true;
        }
        else{
            //echo "数组中uid rid有错<br/>";
            return false;
            //throw new \Exception("could not insert {$Userrole->rid} & {$Userrole->uid}");
        }
    }

    public function getRid($uid){
        $rowSet  = $this->tableGateway->select(array('uid'=>$uid));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }

    public function modifyrid3($id){
        $data = array(
            'rid' => 3,
        );
        if($this->getUserrole($id)){
            $this->tableGateway->update($data,array('uid'=>$id));
            echo "<br/><br/>modify3 success！";
        }
    }

    public function modifyrid1($id){
        $data = array(
            'rid' => 1,
        );
        if($this->getUserrole($id)){
            $this->tableGateway->update($data,array('uid'=>$id));
            echo "<br/><br/>modify1 success！";
        }
    }

    public function deleteUserrole($id){//删
        if((!empty($id['uid']))&&(!empty($id['rid']))) {
            $uid = (int)$id['uid'];
            $rid = (int)$id['rid'];
            $this->tableGateway->delete(array('uid'=>$uid,'rid'=>$rid));
        }else if((!empty($id['uid']))&&(empty($id['rid']))){
            $id = (int)$id['uid'];
            $this->tableGateway->delete(array('uid'=>$id));
        }else if((empty($id['uid']))&&(!empty($id['rid']))){
            $id = (int)$id['rid'];
            $this->tableGateway->delete(array('rid'=>$id));
        }else{
            throw new \Exception("could not delete with no uid or rid");
        }

    }


}