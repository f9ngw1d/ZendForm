<?php

namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ProjectTable
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

    public function getProject($pid)
    {//查询
        
        $rowset = $this->tableGateway->select(array('project_id' => $pid));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getProjectbyUidbyLrn($id)
    {//查询
        echo "cha<br><Br>";
        echo $id."<br><br>";
        $rowset = $this->tableGateway->select(array('uid' => $id));
        var_dump($rowset);
        echo "rowset<br><br>";
        // $row = $rowset->current();
        if (!$rowset) {
            return false;
        }
        return $rowset;
    }
    
    public function getProjectByUidPname($uid,$project_name)
    {//查询
        $uid = (int)$uid;
        $rowset = $this->tableGateway->select(array('uid' => $uid,'project_name'=>$project_name));
        $row = $rowset->current();
        if (!$row) {
            return true; //同一人插入相同数据，则不插入
        }
        return false;
    }

    public function getProjectByUid($uid)
    {//查询
        $uid = (int)$uid;
        $resultSet = $this->tableGateway->select(array('uid' => $uid));
        $projectArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $projectArr[] = $row;
            }
        }
        return $projectArr;
    }
    
    public function saveProject(Project $bxyjfx)
    {//增加 和 修改
        $data = array(
            'project_id' => $bxyjfx->project_id,
            'uid' => (int)$bxyjfx->uid,
            'project_name' => $bxyjfx->project_name,
            'abstract' => $bxyjfx->abstract,
            'conclusion' => $bxyjfx->conclusion,
            'achievement' => $bxyjfx->achievement,
            'certificate' => $bxyjfx->certificate,
            'certificate_level' => $bxyjfx->certificate_level,
            'create_at' => $bxyjfx->create_at,
        );
        if (!$this->getProject($bxyjfx->project_id)) {
            return $this->tableGateway->insert($data);
        } else {
            return $this->tableGateway->update($data, array('project_id' => $bxyjfx->project_id));
        }
    }

    /**
     * @author lrn
     * @param $uid
     * @return array|\ArrayObject|bool|null
     */
    public function getProjectBystuid($uid)
    {
        $rowset = $this->tableGateway->select(array('uid' => $uid));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    /**
     * @author cry
     * @param $pid
     * @return int
     * @throws \Exception
     */
    public function deleteProject($pid)
    {
        $pid = (int)$pid;
        $res = $this->tableGateway->delete(array('project_id' => $pid));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del stu_project project_id:".$pid." fail");
        }
    }
    /**
     * @author cry
     * @param $uid
     * @return int
     * @throws \Exception
     */

    // public function deleteProject($uid)
    // {
    //     $uid = (int)$uid;
    //     $res = $this->tableGateway->delete(array('uid' => $uid));
    //     if($res){
    //         return $res;
    //     }else{//失败则抛出异常，for事务
    //         throw new \Exception("del stu_project uid:".$uid." fail");
    //     }
    // }
    /**
     * @author lrn
     * @param $pid
     * @return int
     * @throws \Exception
     */
    public function deleteProjectByUid($uid)
    {
        $uid = (int)$uid;
        $res = $this->tableGateway->delete(array('uid' => $uid));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del stu_project - uid:".$uid." fail");
        }
    }
}