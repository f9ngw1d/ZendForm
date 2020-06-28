<?php

namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class RoleNameTable{
    protected $tableGateway;

    public function __construct(TableGateway  $tg)
    {
        $this->tableGateway = $tg;
    }
    public function fetchAll($paginated = false){//获取表中全部数据
        if($paginated){
            $select = new Select('usr_role');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new RoleName());
            $pageAdapter = new DbSelect($select,$this->tableGateway->getAdapter);
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public  function getRole($id){ //单行检索数据
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('rid'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getRoleArr($ridArr){
        $purlArr = array();
        if($ridArr){
            foreach ($ridArr as $key => $rid){
                $rowSet = $this->tableGateway->select(array('rid'=>$rid));
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

    public function saveRid(Role $Role){
        $data = array(
            'name' => $Role->name,
            'belong'=>$Role->belong
        );
        $id = (int) $Role->id;
        if($id == 0){
            $this->tableGateway->insert($data);
        }else{
            if($this->getRole($id)){
                $this->tableGateway->update($data,array('rid'=>$id));
            }else{
                throw new \Exception("could not find now {id}");
            }
        }
    }
}
