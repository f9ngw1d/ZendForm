<?php
/**
 * Created by PhpStorm.
 * User: sz-pc
 * Date: 2018/7/23
 * Time: 15:36
 */
namespace Manage\Model;

use Manage\Model\UsrTeacher;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UsrTeacherTable{
    protected $tableGateway;

    public function __construct(TableGateway $tg){
        $this->tableGateway = $tg;
        $this->table = 'usr_teacher';
    }

    public function fetchAll($paginated=false){//获取全部
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public function getUserById($id){//查询
        $id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('staff_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getUserByEmail($email){//查询
        $rowSet  = $this->tableGateway->select(array('email'=>$email));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    /**
     * @author  cry
     * @param \Manage\Model\UsrTeacher $user
     * @return int
     * @throws \Exception
     */
    public function saveUserRegister(UsrTeacher $user){//增加 和 修改
        $data = array(
            'staff_id' => $user->staff_id,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'salt' => $user->salt,
            'password' => $user->password,
            'create_time' => $user->create_time,
            'update_at' => $user->update_at,
        );
        $id = $user->staff_id;
        if (!$this->getUserById($user->staff_id)) {
            $res = $this->tableGateway->insert($data);
            if($res){
                return true;
            }else{
                return false;
                //throw new \Exception("insert usr_teacher fail");
            }
        } else {
            $res = $this->tableGateway->update($data, array('staff_id' => $id));
            if($res >= 0){//当更新数据相同时不认为错误
                return true;
            }else{
                //throw new \Exception("update usr_teacher fail");
                return false;
            }
        }
    }


    /**
     * @param \Manage\Model\UsrTeacher $user
     * @return int
     * @throws \Exception
     */
    public function saveUser2($user){//增加 和 修改
//        print_r($user);
        $data = array(
            'staff_id' => $user['staff_id'],
            'user_name' => $user['user_name'],
            'email' => $user['email'],
            'salt' => $user['salt'],
            'password' => $user['password'],
            'create_time' => $user['create_time'],
            'update_at' => $user['update_at'],
        );

        if (!$this->getUserById($user['staff_id'])) {
            //echo "insert";
            $res = $this->tableGateway->insert($data);
            if($res){
                return $res;
            }else{
                return false;
            }
        } else {
            //echo "update";
            $res = $this->tableGateway->update($data, array('staff_id' => $user['staff_id']));
            if($res){
                return $res;
            }else{
                return false;
            }
        }
    }

    /**
     * @author cry
     * @param $id
     * @return int
     * @throws \Exception
     */
    public function deleteUserStu($id)
    {
        $res = $this->tableGateway->delete(array('staff_id' => $id));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del usr_teacher staff_id:".$id." fail");
        }
    }

    public function deleteUser($id){//删
        $this->tableGateway->delete(array('staff_id'=>$id));
    }
    public function getLastInsert(){
        $dbadapter = $this->tableGateway->getAdapter();
        $sql = "select * from usr_teacher where staff_id=last_insert_id()";
        $result = $dbadapter->query($sql)->execute();
        $resultArr = iterator_to_array($result);
        return $resultArr;
    }//sm
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
        $id =(int) $user->staff_id;
        if($id == 0){
            $this->tableGateway->insert($data);
            return true;
        }else{
            if($this->getUserById($id)){
                $this->tableGateway->update($data,array('staff_id'=>$id));
                return true;
            }else{
                return false;
                //throw new \Exception("could not find row {$id}");
            }
        }
    }

    public function getLastUID(){
        $select = new Select();
        $select ->from('usr_teacher')
            ->order('staff_id desc')
            ->limit(1);
        $result = $this->tableGateway->selectWith($select);
        $uid = ($row = $result->toArray()) ? $row[0]['staff_id'] : 0;
        return $uid;
    }

	/**
	 * Method getUserName
	 * user:QDS
	 * time:2020/4/24 23:38
	 * @function:根据staff_id获取教师姓名
	 * TODO
	 */
    public function getUserNameById($staff_id)
	{
		$rowSet  = $this->tableGateway->select(array('staff_id'=>$staff_id));
		$row = $rowSet->current();
		if(!$row){
			return false;
		}
		return $row;
	}//sm

    public function findAll($limit = 0, $offset = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('usr_teacher' => $this->table));
        if ($limit != 0) {
            $sl->limit($limit);
        }
        if ($offset != 0) {
            $sl->offset($offset);
        }
        $stmt = $sql->prepareStatementForSqlObject($sl);
        $result = $stmt->execute();


        $resultArr = iterator_to_array($result);
        return $resultArr;

    }//sm
    public function getTotalnum()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('usr_teacher');
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));;

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $row = $result->current();
        $rowCount = $row['count'];
        return $rowCount;
    }//sm

//    public function findAllByCol($limit = 0, $offset = 0)
//    {
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $sl = new Select();
//        $sl->from(array('usr_teacher' => $this->table));
//        if ($limit != 0) {
//            $sl->limit($limit);
//        }
//        if ($offset != 0) {
//            $sl->offset($offset);
//        }
//        $stmt = $sql->prepareStatementForSqlObject($sl);
//        $result = $stmt->execute();
//        $resultArr = iterator_to_array($result);
//        return $resultArr;
//
//    }//sm
//    public function getColTotalnum($staff_id)
//    {
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $select = $sql->select('usr_teacher');
//        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));
//        $select->where(array('staff_id'=>$staff_id));
//
//        $stmt = $sql->prepareStatementForSqlObject($select);
//        $result = $stmt->execute();
//        print_r($result);
//        $row = $result->current();
//        $rowCount = $row['count'];
//        return $rowCount;
//    }//sm
}


