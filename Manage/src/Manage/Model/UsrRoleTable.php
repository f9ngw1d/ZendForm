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
use Zend\Session\Container;

class UsrRoleTable{//usrRole表 只能增删查，不能修改
    protected $tableGateway;
	protected $userroleTable;

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

    public function getLastInsert(){
        $dbadapter = $this->tableGateway->getAdapter();
        $sql = "select * from user where staff_id=last_insert_id()";
        $result = $dbadapter->query($sql)->execute();
        $resultArr = iterator_to_array($result);
        return $resultArr;
    }//sm

    public function saveUserrole(UsrRole $Userrole){//增加
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
            return false;
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

    public function deleteLastInsert(){
        $select = new Select();
        $select ->from('usr_user_role')
            ->order('uid desc')
            ->limit(1);
        $result = $this->tableGateway->selectWith($select);
        $uid = ($row = $result->toArray()) ? $row[0]['uid'] : 0;
        $this->tableGateway->delete(array('uid'=>$uid));
    }//sm

	/**qds
	 *getRole 获取一个教师的角色数组
	 * @param string $staff_id
	 * @return array|bool|mixed
	 */
	public function getRole($staff_id = ""){
		if(empty($staff_id)){
			if(isset($this->rid_arr)) {
				echo "已经存在rid_arr";
				return $this->rid_arr;
			}
		}
		//如果角色数组已经存在了，则直接返回

		//如果session里有rid则从session里取出放入成员变量中
		$containerRidArr = new Container('rid');
		$this->rid_arr = $containerRidArr->item;

		//如果session中没有，则从数据库中取
		if(empty($this->rid_arr)){
			//先从session里取uid，如果有则直接用
			$containerRidArr = new Container('uid');
			$this->uid = $containerRidArr->item;

			//session中有uid，则直接用这个uid来查询role_id
			if(!empty($this->uid)) {
				$user_roles = $this->getUserroleTable()->getUserrole(array('uid'=>$this->uid));
				$rid_arr = $this->changeRoleToArr($user_roles);
				$this->rid_arr = $rid_arr;
			}
			//session中没有则用传入的staff_id来查询roleid
			elseif(!empty($staff_id)){
				$user_roles = $this->getUserroleTable()->getUserrole(array('uid'=>$staff_id));
				$rid_arr = $this->changeRoleToArr($user_roles);
				$this->rid_arr = $rid_arr;//iterator_to_array($role_set);
			}
			//session中也没有，也没有传入staff_id则设置为空
			else{
				$this->rid_arr = array();
			}
		}
		return $this->rid_arr;
	}

	/**
	 * changeRoleToArr 把教师的角色result_set转换成数组
	 * @param $user_roles
	 * @return array
	 */
	public function changeRoleToArr($user_roles){
		$rid_arr = array();
		foreach ($user_roles as $key => $ur_row) {
			if(!$ur_row){
				continue;
			}
			else{
				$rid_arr[] = $ur_row->rid;
			}
		}
		return $rid_arr;
	}
	//qds
	public function  getUserroleTable() {
		if($this->userroleTable){
			return $this->userroleTable;
		}
		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new UsrRole());
		$newTableGateway =  new TableGateway("usr_user_role", $this->adapter,null,$resultSetPrototype);
		$table = new UsrRoleTable($newTableGateway);
		$this->userroleTable = $table;
		return $table;
	}

}