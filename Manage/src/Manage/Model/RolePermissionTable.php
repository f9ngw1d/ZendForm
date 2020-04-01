<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/25
 * Time: 16:01
 */
namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class RolepermissionTable{
    protected $tableGateway;

    public  function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }
    public function fetchAll($paginted =false){//获取全部
        if($paginted){
            $select = new Select('usr_role_permission');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new Rolepermission());
            $pageAdapter = new DbSelect($select,$this->tableGateway->getAdapter(),$rs);
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
    }

    public function getRolepermission($id){
        if((!empty($id['pid']))&&(!empty($id['rid']))){
            $pid = (int)$id['pid'];
            $rid = (int)$id['rid'];
            $rowSet = $this->tableGateway->select(array('pid'=>$pid,'rid'=>$rid));
        }else if((!empty($id['pid'])) && (empty($id['rid']))){
            $id = (int)$id['pid'];
            $rowSet = $this->tableGateway->select(array('pid'=>$id));
        }else if((empty($id['pid']))&&(!empty($id['rid']))){
            $id = (int)$id['rid'];
            $rowSet = $this->tableGateway->select(array('rid'=>$id));
        }else{
            echo"both ALL";
            return NULL;
        }
        return $rowSet;
    }
    public function saveRolepermission(Rolepermission $Rolepermission){//增加 和 修改
        $data = array(
            'rid' => $Rolepermission->rid,
            'pid' => $Rolepermission->pid
        );
        if($Rolepermission->rid && $Rolepermission->pid){
            $this->tableGateway->insert($data);
        }else{
            throw new \Exception("could not insert {$Rolepermission->rid} & {$Rolepermission->pid}");
        }
    }


}