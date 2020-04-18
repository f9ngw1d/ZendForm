<?php

namespace Stu\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CheckTable
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

    public function getCheck($id)
    {//查询
        $rowset = $this->tableGateway->select(array('uid' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCheck(Check $bxyjfx)
    {//增加 和 修改
        $data = array(
            'uid' => $bxyjfx->uid,
            'status' => $bxyjfx->status,
            'subject' => $bxyjfx->subject,
            'professor' => $bxyjfx->professor,
            'college' => $bxyjfx->college,
            'school' => $bxyjfx->school,
            'remarks' => $bxyjfx->remarks,
            'subject_re' => $bxyjfx->subject_re,
            'college_re' => $bxyjfx->college_re,
            'school_re' => $bxyjfx->school_re,
            'college_re' => $bxyjfx->college_re,
            'create_at' => $bxyjfx->create_at,
            'update_at' => $bxyjfx->update_at,
            'live_check_staff' => $bxyjfx->live_check_staff,
            'live_check_status' => $bxyjfx->live_check_status,
        );
        if (!$this->getCheck($bxyjfx->uid)) {
            return $this->tableGateway->insert($data);
        } else {
            return $this->tableGateway->update($data, array('uid' => $bxyjfx->uid));
        }
    }

    /**
     * @author cry
     * @param $bxyjfx
     * @return int
     * @throws \Exception
     */
    public function saveCheckStatus($bxyjfx)
    {//增加 和 修改
        $data = array(
            'uid' => $bxyjfx['uid'],
            'status' => $bxyjfx['status'],
            'subject' => $bxyjfx['subject'],
            'professor' => $bxyjfx['professor'],
            'college' => $bxyjfx['college'],
            'school' => $bxyjfx['school'],
            'remarks' => $bxyjfx['remarks'],
            'subject_re' => $bxyjfx['subject_re'],
            'college_re' => $bxyjfx['college_re'],
            'school_re' => $bxyjfx['school_re'],
            'college_re' => $bxyjfx['college_re'],
            'create_at' => $bxyjfx['create_at'],
            'update_at' => $bxyjfx['update_at'],
            'live_check_staff' => $bxyjfx['live_check_staff'],
            'live_check_status' => $bxyjfx['live_check_status'],
        );
        if (!$this->getCheck($bxyjfx['uid'])) {//stu_check
//            return $this->tableGateway->insert($data);
            $res = $this->tableGateway->insert($data);
            if($res){
                return $res;
            }else{
                throw new \Exception("insert stu_check fail");
            }
        } else {
//            return $this->tableGateway->update($data, array('uid' => $bxyjfx['uid']));
            $res = $this->tableGateway->update($data, array('uid' => $bxyjfx['uid']));
            if($res){
                return $res;
            }else{
                throw new \Exception("update stu_check fail");
            }
        }
    }
    public function updateStatus(Check $bxyjfx)
    {//增加 和 修改
        $data = array(
            'uid' => $bxyjfx->uid,
            'status' => $bxyjfx->status,
        );
        if (!$this->getCheck($bxyjfx->uid)) {
            return false;
        } else {
            return $this->tableGateway->update($data, array('uid' => $bxyjfx->uid));
        }
    }

    /**
     * @author cry
     * @function //增加 和 修改
     * @param $uid
     * @param $status
     * @return bool|int
     */
    public function updateStuStatus($uid,$status)
    {
        if ($this->getCheck($uid)==false) {
            return false;
        } else {
            return $this->tableGateway->update(array('status'=>$status), array('uid' => $uid));
        }
    }
    public function updateLiveCheckStatus(Check $bxyjfx){
        $data = array(
            'uid' => $bxyjfx->uid,
            'live_check_status' => $bxyjfx->live_check_status,
            'live_check_staff' => $bxyjfx->live_check_staff,
        );
        if (!$this->getCheck($bxyjfx->uid)) {
            return false;
        } else {
            return $this->tableGateway->update($data, array('uid' => $bxyjfx->uid));
        }
    }

    /**
     * @author cry
     * @param $uid
     * @return int
     * @throws \Exception
     */
    public function deleteCheck($uid)
    {
        $res = $this->tableGateway->delete(array('uid' => $uid));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del stu_check uid:".$uid." fail");
        }
    }
}