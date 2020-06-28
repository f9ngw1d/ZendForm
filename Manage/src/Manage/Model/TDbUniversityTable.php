<?php
/**
 * @author cry
 * table: db_university_free
 * 推免资格学校
 */

namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TDbUniversityTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
        $this->table = 'db_university_free';
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * @author cry
     * 查询具有推免资格的所有大学所在省份
     */
    public function getProvinceArr(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('db_university_free')
            ->quantifier('DISTINCT')
            ->columns(array('SSDM'=>'SSDM','SSDMC'=>'SSDMC'))
            ->where('freetest_qualified="1" and SSDMC is not null');
//            ->order('SSDMC');
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);
        if($result_arr){
            foreach ($result_arr as $key => $row){
                $ssdm = $row['SSDM'];
                $provinceArr[$ssdm] = $row['SSDMC'];
            }
        }
        return $provinceArr;
    }
    /**
     * @author cry
     * 查询具有推免资格的选中省份的所有大学
     */
    public function getUniArrByPid($pid){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('db_university_free')
            ->quantifier('DISTINCT')
            ->columns(array('university_id'=>'university_id','university_name'=>'university_name'))
            ->where('freetest_qualified="1" and SSDM = '.$pid);
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);
        $unisArr = array();
        if($result_arr){
            foreach ($result_arr as $key => $row){
                $unisArr[$row['university_id']] = $row['university_name'];
            }
        }
        return $unisArr;
    }
    /**
     * @author cry
     * 根据大学name查询大学
     */
    public function getUniversity($name)
    {//查询
        $rowset = $this->tableGateway->select(array('university_name' => $name));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    /**
     * @author cry
     * 根据大学id查询大学 推免
     */
    public function getUniversitybyId($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('university_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;//->freetest_qualified
    }
    /**
     * @author cry
     * 增加 和 修改 大学
     */
    public function saveUniversity(TDbUniversity $bxyjfx)
    {
        $data = array(
            'university_name' => $bxyjfx->university_name,
            'university_id' => $bxyjfx->university_id,
            'is985' => $bxyjfx->is985,
            'is211' => $bxyjfx->is211,
            'freetest_qualified' => $bxyjfx->freetest_qualified,
            'SSDM' =>$bxyjfx->SSDM,
            'SSDMC' => $bxyjfx->SSDMC,
        );
        if (!$this->getUniversity($bxyjfx->university_name)) {
            return $this->tableGateway->insert($data);
        } else {
            return $this->tableGateway->update($data, array('university_name' => $bxyjfx->university_name));
        }
    }
    /**
     * @author cry
     * 根据大学名字删大学
     */
    public function deleteUniversity($name)
    {//删
        $this->tableGateway->delete(array('university_name' => $name));
    }

    public function getUnibyname($name)
    {
        //var_dump($offset);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('uni' => $this->table))
            ->columns(array('*'))
            ->where(array('university_name = ?' => $name));;
        $statement = $sql->prepareStatementForSqlObject($sl);
        // echo "statment";
        //echo ">>>>>>>".$statement->getSql();
        $resultset = $statement->execute();
        $resultArr = iterator_to_array($resultset);
        // var_dump($resultArr);
        if (empty($resultArr)) {
            return 0;
        }
        else
            return 1;
    }
    /*
     * lrn    查学校
     */
    public function getUniversityByUid($id)
    {//查询
        $rowset = $this->tableGateway->select(array('university_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function getUnibyCon($condArr,$limit = 0,$offset = 0)
    {
        //echo "get";
        // echo $offset;
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('uni' => $this->table))
            ->columns(array('*'));
        foreach($condArr as $cond){
            $sl->where($cond);
        }
        if($limit != 0){
            $sl->limit($limit);
        }
        if($offset != 0){
            $sl->offset($offset);
        }

        $statement = $sql->prepareStatementForSqlObject($sl);
        $resultset = $statement->execute();
        $resultArr = iterator_to_array($resultset);
        return $resultArr;
    }

    public function updateUni(TDbUniversity $uni)
    {
        //echo  ">>>>>>>>>>>>>>>>>>>>>>>>>>>>update";
        $data = array(
            'university_name' => $uni->university_name,
//            'university_id'  => $uni->university_id,
            'SSDM'  => $uni->SSDM,
            'SSDMC' => $uni->SSDMC,
            'is985' => $uni->is985,
            'is211'  => $uni->is211,
            'freetest_qualified' => $uni->freetest_qualified,
        );
        $id = $uni->university_id;
        if($this->getUniversityByUid($id)){
            $res=$this->tableGateway->update($data,array('university_id'=>$id));
            if($res)
                return true;
            else
                return false;
        }else{
            throw new \Exception("系统中找不到该学校信息，请先添加高校信息");
        }
//        unset($data['university_id']);
//        if($this->tableGateway->update($data, array('university_id' => $id)))
//            return true;
//        else
//            return false;
    }

    public function getConnum($condArr)
    {
        if(!$condArr)
            return 0;
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from(array('db_university' => $this->table))
            ->columns(array('count'=>new \Zend\Db\Sql\Expression('COUNT(*)')));
        foreach($condArr as $cond){
            $sl->where($cond);
        }

        $statement = $sql->prepareStatementForSqlObject($sl);
        $resultset = $statement->execute();
        $row       = $resultset->current();
        $rowCount = $row['count'];
        return $rowCount;
    }
}