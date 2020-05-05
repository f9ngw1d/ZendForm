<?php
namespace Manage\Model;
use Manage\Model\TBaseCollege;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Stdlib\Hydrator;

class TBaseCollegeTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
        $this->table = 'base_college';
    }
    //检索数据库中 base_college 表所有的记录，然后将结果返回 ResultSet
    public function fetchAll()
    {
        $resultSet= $this->tableGateway->select();
        return $resultSet;
    }
    public function getCollegeNameArr(){//查询所有学院名称
        $resultSet  = $this->tableGateway->select();
        $collegeArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row){
                $collegeArr[] = $row->college_name;
            }
        }
        //$colArr = array_flip($collegeArr);//键/值对反转
        return $collegeArr;
    }
    public function getCollegesIDNameArr(){
        $resultSet = $this->tableGateway->select();
        $collegeArr = array();
        foreach ($resultSet as $key => $row) {
            if(!empty($row)) {
                $collegeArr[$row->college_id] = $row->college_name;
            }
        }
        return $collegeArr;
    }
    public function getCollege($id){//根据cid查询
        //$id = $id;
        $rowSet  = $this->tableGateway->select(array('college_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            return false;
        }
        return $row;
    }
    public function getCollegebyStaffid($id){//根据cid查询
        //$id = $id;
        $rowSet  = $this->tableGateway->select(array('manager_id'=>$id));
        $row = $rowSet->current();
        if(!$row){
            $rowSet  = $this->tableGateway->select(array('dean_id'=>$id));
            $row = $rowSet->current();
        }
        return $row;
    }
    public function find($college_id)
    {
        $rowset = $this->tableGateway->select(array('college_id' => $college_id));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveCollege(TBaseCollege $college)
    {
        $data = array(
            'college_id' => $college->college_id,
            'college_name' => $college->college_name,
            'phone' => $college->phone,
            'ip_address' => $college->ip_address,
            'address' => $college->address,
        );
        //var_dump($data);
        $id = $college->college_id;

        if ($this->find($id)) {
//            echo "update<br><br>";
            $this->tableGateway->update($data, array('college_id' => $id));
        }
        else
        {
//            echo "insert<br><br>";
            $this->tableGateway->insert($data);
        }
    }
    public function findAll($limit = 0, $offset = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('base_college' => $this->table));

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
    }
    public function getTotalnum()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('base_college');
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(*)')));;

        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $row = $result->current();
        $rowCount = $row['count'];
        return $rowCount;
    }
    public function deleteCollege($college_id)
    {
        $res = $this->tableGateway->delete(array('college_id' => $college_id));
        if($res){
            echo "delete successful.";
            return $res;
        }else{//失败则抛出异常，for事务
            echo "fail";
            throw new \Exception("del base_college uid:". $college_id. " fail");
        }
    }
    public function getCollegeName($id)
    {
        $sql_query = "SELECT college_name FROM base_college WHERE college_id =".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function deleteAll()
    {
        $sql_query = "Delete From base_team";
        $sql_query1 = "Delete From base_college";
        $sql_query2 = "Delete From base_staff";
        $sql_query3 = "Delete From info_article";
        $sql_query4 = "Delete From info_mail";
        $sql_query5 = "Delete From info_text";
        $sql_query6 = "Delete From info_validatemail";
        $sql_query7 = "Delete From manage_filter_condition";
        $sql_query8 = "Delete From manage_subject_map";
        $sql_query9 = "Delete From manage_time";
        $sql_query10 = "Delete From stu_base";
        $sql_query11 = "Delete From stu_check";
        $sql_query12 = "Delete From stu_einfo_map";
        $sql_query13 = "Delete From stu_electronic_info";
        $sql_query14 = "Delete From stu_honour";
        $sql_query15 = "Delete From stu_project";
        $sql_query16 = "Delete From stu_reexam_result";
        $sql_query17 = "Delete usr_user_role From usr_user_role,usr_stu WHERE usr_user_role.uid = usr_stu.uid";
        $sql_query18 = "Delete usr_user_role From usr_user_role,usr_teacher WHERE usr_teacher.staff_id = usr_user_role.uid AND rid <> 10 AND rid <> 99";
        $sql_query19 = "Delete From usr_stu";
        $sql_query20 = "Delete From usr_teacher";

        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $rowSet1 = $this->tableGateway->getAdapter()->query($sql_query1)->execute();
        $rowSet2 = $this->tableGateway->getAdapter()->query($sql_query2)->execute();
        $rowSet3 = $this->tableGateway->getAdapter()->query($sql_query3)->execute();
        $rowSet4 = $this->tableGateway->getAdapter()->query($sql_query4)->execute();
        $rowSet5 = $this->tableGateway->getAdapter()->query($sql_query5)->execute();
        $rowSet6 = $this->tableGateway->getAdapter()->query($sql_query6)->execute();
        $rowSet7 = $this->tableGateway->getAdapter()->query($sql_query7)->execute();
        $rowSet8 = $this->tableGateway->getAdapter()->query($sql_query8)->execute();
        $rowSet9 = $this->tableGateway->getAdapter()->query($sql_query9)->execute();
        $rowSet10 = $this->tableGateway->getAdapter()->query($sql_query10)->execute();
        $rowSet11 = $this->tableGateway->getAdapter()->query($sql_query11)->execute();
        $rowSet12 = $this->tableGateway->getAdapter()->query($sql_query12)->execute();
        $rowSet13 = $this->tableGateway->getAdapter()->query($sql_query13)->execute();
        $rowSet14 = $this->tableGateway->getAdapter()->query($sql_query14)->execute();
        $rowSet15 = $this->tableGateway->getAdapter()->query($sql_query15)->execute();
        $rowSet16 = $this->tableGateway->getAdapter()->query($sql_query16)->execute();
        $rowSet17 = $this->tableGateway->getAdapter()->query($sql_query17)->execute();
        $rowSet18 = $this->tableGateway->getAdapter()->query($sql_query18)->execute();
        $rowSet19 = $this->tableGateway->getAdapter()->query($sql_query19)->execute();
        $rowSet20 = $this->tableGateway->getAdapter()->query($sql_query20)->execute();
    }
}