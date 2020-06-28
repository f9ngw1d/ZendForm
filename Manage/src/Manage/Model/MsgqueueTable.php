<?php
/**
 * Created by PhpStorm.
 * User: sz-pc
 * Date: 2018/7/24
 * Time: 14:24
 */
namespace Manage\Model;

use Manage\Model\Msgqueue;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;



class MsgqueueTable{
    protected $tableGateway;

    public function __construct(TableGateway $tg){
        $this->tableGateway = $tg;
    }

    public function fetchAll(){//获取全部
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getMsgById($id){
        $rowSet  = $this->tableGateway->select(array('id'=>$id));
        $row = $rowSet->current();
        return $row;
    }

    public function getMsgByArr($Arr){
        $rowSet  = $this->tableGateway->select($Arr);
        return $rowSet;
    }

    public function saveMsgqueue(Msgqueue $add){//增加 和 修改
        $data = array(
            'id' => $add->id,
            'title' => $add->title,
            'receiver' => $add->receiver,
            'content' => $add->content,
            'status' => $add->status
        );
        if(!($this->getMsgById($add->id))){
            unset($data->id);
            return $this->tableGateway->insert($data);
        }else{
            unset($data->id);
            return $this->tableGateway->update($data,array('id'=>$add->id));
        }
    }

    public function deleteMsg($arr){//删
        return $this->tableGateway->delete($arr);
    }
}