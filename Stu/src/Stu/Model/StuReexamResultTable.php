<?php

namespace Stu\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StuReexamResultTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg){
        $this->tableGateway = $tg;
    }

    public function fetchAll(){//获取全部
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getStuReexamResultbyId($id){//查询
        $id = (int)$id;
        $rowSet  = $this->tableGateway->select(array('id'=>$id));
        $row = $rowSet->current();

        if(!$row){
            //throw new \Exception("could not find row");
            return false;
        }
        return $row;
    }

    public function saveStuReexamResult(StuReexamResult $add){//增加 和 修改
        $data = array(
            'course1' => $add->course1,
            'course2' => $add->course2,
            'course3' => $add->course3,
            'course4' => $add->course4,
            'marker1' => $add->marker1,
            'marker2' => $add->marker2,
            'marker3' => $add->marker3,
            'marker4' => $add->marker4,
            'id' => $add->id,
            'total' => $add->total,
        );
        if(!$this->getStuReexamResultbyId($add->id)){
            $this->tableGateway->insert($data);
            //echo "insert success!<br/>";
            return true;
        }else{
            if($this->getStuReexamResultbyId($add->id)){
                $this->tableGateway->update($data,array('id'=>$add->id));
                //echo "update success!<br/>";
                return true;
            }else{
                return false;
            }
        }
    }

    public function deleteStuReexamResult($id){//删
        $this->tableGateway->delete(array('id'=>$id));
    }
}