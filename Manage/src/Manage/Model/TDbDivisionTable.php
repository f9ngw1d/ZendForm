<?php

namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TDbDivisionTable
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

    public function getDivision($id)
    {//查询
        $rowset = $this->tableGateway->select(array('YQXDM' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveDivision(Division $bxyjfx)
    {//增加 和 修改
        $data = array(
            'YQXDM' => $bxyjfx->YQXDM,
            'YQXMC' => $bxyjfx->YQXMC,
            'SSDM' => $bxyjfx->SSDM,
            'SSMC' => $bxyjfx->SSMC,
            'DJSDM' => $bxyjfx->DJSDM,
            'DJSMC' => $bxyjfx->DJSMC,
            'XJSDM' => $bxyjfx->XJSDM,
            'XJSMC' => $bxyjfx->XJSMC,
            'QXDM' => $bxyjfx->QXDM,
            'QXMC' => $bxyjfx->QXMC,
            'BZ' => $bxyjfx->BZ,
        );
        if (!$this->getDivision($bxyjfx->YQXDM)) {
            return $this->tableGateway->insert($data);
        } else {
            return $this->tableGateway->update($data, array('YQXDM' => $bxyjfx->YQXDM));
        }
    }
    public function getUniCode()
    {
        $sql_query = "SELECT DISTINCT SSMC,SSDM FROM db_administrative_division";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function getUni()
    {
        $sql_query = "SELECT DISTINCT SSMC FROM db_administrative_division";
        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        $result_arr = iterator_to_array($rowSet);
        return $result_arr;
    }
    public function deleteDivision($id)
    {//删
        $this->tableGateway->delete(array('YQXDM' => $id));
    }
}
