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

class RoleTable{//usrRole表 只能增删查，不能修改
    protected $tableGateway;
	protected $roleTable;

	public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }

    public function fetchAll($paginate = false){
        if($paginate){
            $select = new Select('usr_role');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new Role());
            $pageAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
    }

    public function getLastInsert(){
        $dbadapter = $this->tableGateway->getAdapter();
        $sql = "select * from user where staff_id=last_insert_id()";
        $result = $dbadapter->query($sql)->execute();
        $resultArr = iterator_to_array($result);
        return $resultArr;
    }//sm


    //根据rid获取name
    public function getNameByRid($rid){
        $rowSet  = $this->tableGateway->select(array('rid'=>$rid));
        if(!$rowSet){
            return false;
        }
        return $rowSet;
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
			    array_push($rid_arr,$ur_row);
//				$rid_arr[] = $ur_row->rid;//lrn改之前189行
			}
		}
		return $rid_arr;
	}
}