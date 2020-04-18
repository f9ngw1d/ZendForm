<?php

namespace Stu\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class UniversityTable
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

    /**
     * @return array
     */
    public function getProvinceArr(){//查询具有推免资格的所有大学所在省份
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('db_university')
            ->quantifier('DISTINCT')
            ->columns(array('SSDM'=>'SSDM','SSDMC'=>'SSDMC'))
            ->where('freetest_qualified=1 and SSDMC <>""')
            ->order('SSDMC');
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
    public function getUniArrByPid($pid){//没写完！！！！！！！！！！！！！！！
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('db_university')
            ->quantifier('DISTINCT')
            ->columns(array('university_id'=>'university_id','university_name'=>'university_name'))
            ->where('freetest_qualified=1 and SSDM = '.$pid);
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
    public function getCityArrByPid($pid){//查询指定省份下具有推免资格的所有大学所在城市 location
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('db_university')
            ->quantifier('DISTINCT')
            ->columns(array('location'=>'location'))
            ->where('freetest_qualified=1 and location <>"" and SSDM = '.$pid);
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);
        if($result_arr){
            foreach ($result_arr as $key => $row){
                $cityArr[$row['location']] = $row['location'];
            }
        }
        return $cityArr;
    }
    public function getUniArrByCity($city){//查询城市下具有推免资格的所有大学
//        $city ="唐山市";
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from('db_university')
//            ->quantifier('DISTINCT')
            ->columns(array('university_id'=>'university_id','university_name'=>'university_name'))
            ->where("freetest_qualified=1 and  location = '$city'");
//            ->where("SSDM = $city");
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);
        if($result_arr){
            foreach ($result_arr as $key => $row){
                $uniArr[$row['university_id']] = $row['university_name'];
            }
        }
        return $uniArr;
    }

    public function getUniversity($id)
    {//查询
        $rowset = $this->tableGateway->select(array('university_name' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function getUniversitybyId($id)
    {//查询
        $rowset = $this->tableGateway->select(array('university_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveUniversity(University $bxyjfx)
    {//增加 和 修改
        $data = array(
            'university_name' => $bxyjfx->university_name,
            'university_id' => $bxyjfx->university_id,
            'belong_department' => $bxyjfx->belong_department,
            'locaiton' => $bxyjfx->locaiton,
            'level' => $bxyjfx->level,
            'remark' => $bxyjfx->remark,
            'is985' => $bxyjfx->is985,
            'is211' => $bxyjfx->is211,
            'freetest_qualified' => $bxyjfx->freetest_qualified,
        );
        if (!$this->getUniversity($bxyjfx->university_name)) {
            return $this->tableGateway->insert($data);
        } else {
            return $this->tableGateway->update($data, array('university_name' => $bxyjfx->university_name));
        }
    }

    public function deleteUniversity($id)
    {//删
        $this->tableGateway->delete(array('university_name' => $id));
    }
}