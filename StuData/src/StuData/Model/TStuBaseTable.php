<?php

namespace StuData\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class TStuBaseTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
        $this->table = 'stu_base';
    }

    public function fetchAllStuByCond()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('stu_base'=> $this->table))
            ->columns(array('uid',
                'gender',
                'graduate_university',
                'target_college',
                'target_subject',
                'target_profession',
            ));
//        $sl->from(array('stu_base'))
//            ->join(array('usr_stu'), 'sb.uid=u.uid')
//            ->join(array('base_college'), 'stu_base.target_college=base_college.college_id', array('college_name'))
//            ->join(array('base_subject'), 'stu_base.target_subject=base_subject.subject_id', array('subject_name'))
//            ->join(array('base_profession'), 'stu_base.target_subject=base_profession.subject_id and stu_base.target_college=base_profession.college_id and stu_base.target_profession=base_profession.profession_id', array('profession_name'))
//            //->where("sb.target_subject='" .$subject_id ."' AND sb.target_profession='" .$profession_id ."'")
//            ->order('sb.target_profession ASC')
//            ->order('sb.uid ASC');
        $statement = $sql->prepareStatementForSqlObject($sl);
        $stu_res = $statement->execute();
        $stu = iterator_to_array($stu_res);//学生信息数组
        return $stu;
    }

    public function fetchAll($paginated = false)
    {//获取全部
        if ($paginated) {
            $select = new Select('StuBase');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new StuBase());
            $pageAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $rs);
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
//lrn
    public function getStu($id)
    {//查询
        $rowset = $this->tableGateway->select(array('uid' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function getStuByEmailandIdcard($email,$idcard){
        $rowset = $this->tableGateway->select(array('email' => $email,'idcard' => $idcard));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getStuByCondArr($condArr = array(),$orderArr = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('stu_base')
            ->columns(array('*'))
            ->join('stu_check', 'stu_base.uid=stu_check.uid', array('status'));
        foreach ($condArr as $cond) {
            $sl->where($cond);
        }
        foreach ($orderArr as $order){
            $sl->order($order);
        }

        $statement = $sql->prepareStatementForSqlObject($sl);
       // var_dump($statement);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);
        return $result_arr;
    }


    public function getStubyId($idcard)
    {//查询
        $rowset = $this->tableGateway->select(array('idcard' => $idcard));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getStubyCollege($id)
    {//查询
        $rowset = $this->tableGateway->select(array('target_college' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    /**
     * @author sm
     * @param $target_college
     * @function 根据target_college获取学生信息
     */
    public function getStuByCol($target_college){
        $rowSet = $this->tableGateway->select(array('target_college' => $target_college));
        if(!$rowSet){
            return false;
        }
        return $rowSet;
    }//苏淼
    /**
     * @author sm
     * @param $target_team
     * @function 根据target_team获取学生信息
     */
    public function getStuByTeam($target_team){
        $rowSet = $this->tableGateway->select(array('target_team' => $target_team));
        if(!$rowSet){
            return false;
        }
        return $rowSet;
    }//苏淼

    public function getStuisAllowAdd($id)
    {//查询
        $rowset = $this->tableGateway->select(array('uid' => $id));
        $row = $rowset->current();
//        if(!$row->foreign_language){
//            return true;
//        }else{
//            return false;
//        }
        if(!$row){
            return false;
        }
        return $row->foreign_language;
    }
    /*
     * lrn   change volunteer
     */
    public function updateStuVolunteer(TStuBase $Tstu)
    {//增加 和 修改
        $data = array(
            'uid' => $Tstu->uid,
            'target_college' => $Tstu->target_college,
            'target_profession' => $Tstu->target_profession,
            'target_subject' => $Tstu->target_subject,
        );
        if (!$this->getStu($Tstu->uid)) {
            $this->tableGateway->insert($data);
        } else {
            $this->tableGateway->update($data, array('uid' => $Tstu->uid));
        }
    }
    /*
     * lrn   change author
     */
    public function updateTargetProfessor(StuBase $Tstu)
    {//增加 和 修改
        $data = array(
            'uid' => $Tstu->uid,
            'target_professor' => $Tstu->target_professor,
            'target_professor2' => $Tstu->target_professor2,
            'target_professor3' => $Tstu->target_professor3,
        );
        if (!$this->getStu($Tstu->uid)) {
            $this->tableGateway->insert($data);
        } else {
            $this->tableGateway->update($data, array('uid' => $Tstu->uid));
        }
    }

    /**
     * @author cry
     * @param TStuBase $Tstu
     * @return int
     * @throws \Exception
     */
    public function saveStu(TStuBase $Tstu)
    {//增加 和 修改
        $data = array(
            'uid' => $Tstu->uid,
            'gender' => $Tstu->gender,
            'idcard' => $Tstu->idcard,
            'nationality' => $Tstu->nationality,
            'political_status' => $Tstu->political_status,
            'graduate_university' => $Tstu->graduate_university,
            'graduate_college' => $Tstu->graduate_college,
            'graduate_subject' => $Tstu->graduate_subject,
            'graduate_professional_class' => $Tstu->graduate_professional_class,
            'graduate_profession' => $Tstu->graduate_profession,
            'target_university' => $Tstu->target_university,
            'target_college' => $Tstu->target_college,
            'apply_type' => $Tstu->apply_type,
            'examid' => $Tstu->examid,
            'value_cet4' => $Tstu->value_cet4,
            'value_cet6' => $Tstu->value_cet6,
            'pro_stu_num' => $Tstu->pro_stu_num,
            'grade_point' => $Tstu->grade_point,
            'ranking' => $Tstu->ranking,
            'relation' => $Tstu->relation,
            'universitylevel' => $Tstu->universitylevel,
            'remarks' => $Tstu->remarks,
            'email' => $Tstu->email,
            'user_name'=>$Tstu->user_name,
            'phone' => $Tstu->phone,
        );
        if (!$this->getStu($Tstu->uid)) {
//            return $this->tableGateway->insert($data);
            $res = $this->tableGateway->insert($data);
            if($res){
                return $res;
            }else{
                throw new \Exception("insert stu_base fail");
            }
        } else {
//            return $this->tableGateway->update($data, array('uid' => $Tstu->uid));
            $res = $this->tableGateway->update($data, array('uid' => $Tstu->uid));
            if($res){
                return $res;
            }else{
                throw new \Exception("update stu_base fail");
            }

        }
    }
    public function saveStuAddinfo($uid,$addinfo){
        $data = array(
            'graduate_subject' => $addinfo['graduate_subject'],
            'graduate_college' =>$addinfo['graduate_college'],
            'graduate_professional_class' => $addinfo['graduate_professional_class'],
            'graduate_profession' => $addinfo['graduate_profession'],
            'pro_stu_num' => (int)$addinfo['pro_stu_num'],
            'ranking' => (int)$addinfo['ranking'],
            'grade_point' =>  floatval($addinfo['grade_point']),
            'value_cet6' =>  (!empty($addinfo['value_cet6'])) ?floatval($addinfo['value_cet6']): null,
            'target_university' => $addinfo['target_university'],
            'target_college' =>  $addinfo['target_college'],
            'target_subject' =>  $addinfo['target_subject'],
            'target_profession' =>  $addinfo['target_profession'],
            'target_professor' =>  $addinfo['target_professor'],
            'target_professor2' =>  $addinfo['target_professor2'],
            'target_professor3' =>  $addinfo['target_professor3'],
            'foreign_language' =>  $addinfo['foreign_language'],
            'gre_score' =>  (!empty($addinfo['gre_score'])) ?floatval($addinfo['gre_score']): null,
            'toefl_score' =>  (!empty($addinfo['toefl_score'])) ?floatval($addinfo['toefl_score']): null,
        );
        return $this->tableGateway->update($data, array('uid' => $uid));
    }

    /**
     * @author lrn
     * @param $uid
     * @return int
     * @throws \Exception
     */
    public function deleteStu($uid)
    {
        $res = $this->tableGateway->delete(array('uid' => $uid));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del stu_base uid:".$uid." fail");
        }
    }

    public function deleteStuBase()
    {
        $res = $this->tableGateway->delete();
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del fail");
        }
    }

    /**
     * @author cry
     * @function 查询身份证号码或者邮件是否被注册 返回-1是被注册了
     * @param $method
     * @param $data
     * @return int
     */
    public function checkTstuRegister($method,$data){
        if($method == 'idcard'){
            $rowSet  = $this->tableGateway->select(array('idcard'=>$data));
            $row = $rowSet->current();
            if(!$row)
                return $data;//返回传入的idcard
            else
                return -1; //被注册了
        }else if ($method == 'email'){
            $rowSet  = $this->tableGateway->select(array('email'=>$data));
            $row = $rowSet->current();
            if(!$row)
                return $data;//返回传入的email
            else
                return -1;//被注册了
        }

    }

    public function forExport($status) {
        $sql_query = "
        SELECT DISTINCT * FROM stu_base
        JOIN stu_check ON stu_base.uid=stu_check.uid";

        if ($status != "all")
            $sql_query .= " WHERE stu_check.status=".$status;

        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        return $rowSet;
    }

    public function getGender()
    {
        $sql_query = "SELECT gender FROM stu_base";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getCollegeGender($id)
    {
        $sql_query = "SELECT gender FROM stu_base WHERE target_college = ".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getTeamGender($id)
    {
        $sql_query = "SELECT gender FROM stu_base WHERE target_team = ".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getAll()
    {
        $sql_query = "SELECT uid FROM stu_base";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getCollegeAll($id)
    {
        $sql_query = "SELECT uid FROM stu_base WHERE target_college = ".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getTeamAll($id)
    {
        $sql_query = "SELECT uid FROM stu_base WHERE target_team = ".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getUni()
    {
        $sql_query = "SELECT stu_base.uid FROM stu_base,db_university_free
                      WHERE (db_university_free.is985 = '1' OR db_university_free.is211 = '1') AND 
                      db_university_free.university_id = stu_base.graduate_university";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getCollegeUni($id)
    {
        $sql_query = "SELECT stu_base.uid FROM stu_base,db_university_free
                      WHERE (db_university_free.is985 = '1' OR db_university_free.is211 = '1') AND 
                      db_university_free.university_id = stu_base.graduate_university
                      AND stu_base.target_college = ".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getTeamUni($id)
    {
        $sql_query = "SELECT stu_base.uid FROM stu_base,db_university_free
                      WHERE (db_university_free.is985 = '1' OR db_university_free.is211 = '1') AND 
                      db_university_free.university_id = stu_base.graduate_university
                      AND stu_base.target_team = ".$id;
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getUniRank()
    {
        $sql_query = "SELECT db_university_free.university_name,COUNT(stu_base.graduate_university) as sum
                      FROM stu_base,db_university_free
                      WHERE db_university_free.university_id = stu_base.graduate_university
                      GROUP BY stu_base.graduate_university
					  ORDER BY sum DESC";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getCollegeUniRank($id)
    {
        $sql_query = "SELECT db_university_free.university_name,COUNT(stu_base.graduate_university) as sum
                      FROM stu_base,db_university_free
                      WHERE db_university_free.university_id = stu_base.graduate_university AND "."stu_base.target_college = ".$id."
                      GROUP BY stu_base.graduate_university
					  ORDER BY sum DESC";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getTeamUniRank($id)
    {
        $sql_query = "SELECT db_university_free.university_name,COUNT(stu_base.graduate_university) as sum
                      FROM stu_base,db_university_free
                      WHERE db_university_free.university_id = stu_base.graduate_university AND "."stu_base.target_team = ".$id."
                      GROUP BY stu_base.graduate_university
					  ORDER BY sum DESC";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }

    /**
     * @author ly
     * @param $team_id  array()
     * @function 获取小组内学生信息
     */
    public function getStuinfoByTeamID($team_id){
        $rowset = $this->tableGateway->select(array('target_team' => $team_id[0]));
        if($rowset){
            $stuArray = $rowset->toArray();
            $stuinfo = array();
            foreach ($stuArray as $key => $value) {
                $stuinfo[$key]['uid'] = $value['uid'];
                $stuinfo[$key]['gender'] = $value['gender'];
                $stuinfo[$key]['user_name'] = $value['user_name'];
            }
            return $stuinfo;
        }else{//失败则抛出异常，for事务
            throw new \Exception("getStuinfoByTeamID fail");
        }
    }
}
