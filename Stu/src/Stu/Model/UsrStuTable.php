<?php
/**
 * Created by PhpStorm.
 * User: sz-pc
 * Date: 2018/7/23
 * Time: 15:36
 */
namespace Stu\Model;

use Stu\Model\UsrStu;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UsrStuTable{
    protected $tableGateway;

    public function __construct(TableGateway $tg){
        $this->tableGateway = $tg;
    }

    public function fetchAll($paginated=false){//获取全部
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public function getUserByid($id){//查询
        $id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('uid'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getUserByemail($email){//查询
        $rowSet  = $this->tableGateway->select(array('email'=>$email));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    /**
     * @author  cry
     * @param \Stu\Model\UsrStu $user
     * @return int
     * @throws \Exception
     */
    public function saveUserRegister(UsrStu $user){//增加 和 修改
        $data = array(
            'user_name' => $user->user_name,
            'email' => $user->email,
            'salt' => $user->salt,
            'password' => $user->password,
            'create_at' => $user->create_at,
            'update_at' => $user->update_at,
        );

        if (!$this->getUserByid($user->uid)) {
//            return $this->tableGateway->insert($data);
            $res = $this->tableGateway->insert($data);
            if($res){
                return $res;
            }else{
                throw new \Exception("insert usr_stu fail");
            }
        } else {
//            return $this->tableGateway->update($data, array('uid' => $user->uid));
            $res = $this->tableGateway->update($data, array('uid' => $user->uid));
            if($res){
                return $res;
            }else{
                throw new \Exception("update usr_stu fail");
            }
        }
    }


    /**
     * @param \Stu\Model\UsrStu $user
     * @return int
     * @throws \Exception
     */
    public function saveUser2(UsrStu $user){//增加 和 修改
        $data = array(
            'uid' => $user->uid,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'salt' => $user->salt,
            'password' => $user->password,
            'create_at' => $user->create_at,
            'update_at' => $user->update_at,
        );

        if (!$this->getUserByid($user->uid)) {
            //echo "insert";
            $res = $this->tableGateway->insert($data);
            if($res){
                return $res;
            }else{
                return false;
            }
        } else {
            //echo "update";
            $res = $this->tableGateway->update($data, array('uid' => $user->uid));
            if($res){
                return $res;
            }else{
                return false;
            }
        }
    }

    /**
     * @author cry
     * @param $uid
     * @return int
     * @throws \Exception
     */
    public function deleteUserStu($uid)
    {
        $res = $this->tableGateway->delete(array('uid' => $uid));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del usr_stu uid:".$uid." fail");
        }
    }

    public function deleteUser($id){//删
        $this->tableGateway->delete(array('uid'=>$id));
    }
    public function getLastInsert(){
        $dbadapter = $this->tableGateway->getAdapter();
        $sql = "select * from user where uid=last_insert_id()";
        $result = $dbadapter->query($sql)->execute();
        $resultArr = iterator_to_array($result);
        return $resultArr;
    }
    public function registercheck($email){
        $rowset = $this->tableGateway->select(array('email' => $email));
        $row = $rowset->current();
        if(!$row)
            return $row;
        else
            return -1;
    }

    public function saveUser($user){//增加 和 修改
        $data = array(
            'user_name' => $user->user_name,
            'password' => $user->password,
            'email' => $user->email,
            //   'phone' => $user->phone
        );
        $id =(int) $user->uid;
        if($id == 0){
            $this->tableGateway->insert($data);
            return true;
        }else{
            if($this->getUserByid($id)){
                $this->tableGateway->update($data,array('uid'=>$id));
                return true;
            }else{
                return false;
                //throw new \Exception("could not find row {$id}");
            }
        }
    }

}
