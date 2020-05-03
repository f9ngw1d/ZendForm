<?php

namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class HonourTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getHonour($hid)
    {//查询
        $rowset = $this->tableGateway->select(array('honour_id' => $hid));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function getHonourByUid($uid)
    {//查询
        $uid = (int)$uid;
        $resultSet = $this->tableGateway->select(array('uid' => $uid));
        $honourArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $honourArr[] = $row;
            }
        }
        return $honourArr;
    }
    public function getHonourByUidHname($uid,$honour_name)
    {//查询
        $uid = (int)$uid;
        $rowset = $this->tableGateway->select(array('uid' => $uid,'honour_name'=>$honour_name));
        $row = $rowset->current();
        if (!$row) {
            return true; //同一人插入相同数据，则不插入
        }
        return false;
    }
    public function saveHonour(Honour $bxyjfx)
    {//增加 和 修改
        $data = array(
            'honour_id' => $bxyjfx->honour_id,
            'uid' => (int)$bxyjfx->uid,
            'honour_name' => $bxyjfx->honour_name,
            'specificdesc' => $bxyjfx->specificdesc,
            'certificate' => $bxyjfx->certificate,
            'certificate_level' => $bxyjfx->certificate_level,
            'honour_at' => $bxyjfx->honour_at,
            'create_at' => $bxyjfx->create_at,
        );
        if (!$this->getHonour($bxyjfx->honour_id)) {
            return $this->tableGateway->insert($data);
        } else {
            return $this->tableGateway->update($data, array('honour_id' => $bxyjfx->honour_id));
        }
    }

    public function deleteHonour($hid)
    {//删
        $hid = (int)$hid;
       if( $this->tableGateway->delete(array('honour_id' => $hid))){
           return true;
       }else{
           return false;
       }
    }

    /**
     * @author lrn
     * @param $uid
     * @return array|\ArrayObject|bool|null
     */
    public function getHonourBystuid($uid)
    {
        $rowset = $this->tableGateway->select(array('uid' => $uid));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    /**
     * @author lrn
     * @param $uid
     * @return int
     * @throws \Exception
     */
    public function deleteStuAllHonour($uid)
    {
        $res = $this->tableGateway->delete(array('uid' => $uid));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del stu_honour uid:".$uid." fail");
        }
    }
}