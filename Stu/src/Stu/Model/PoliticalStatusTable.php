<?php
/**
 * @author cry
 * @function 政治面貌对照表 stu_political_status
 */
namespace Stu\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PoliticalStatusTable
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
     * @author cry
     * @function 获取政治面貌对照表 stu_political_status
     * @return array
     */
    public function fetchAllArr()
    {//查询
        $rowSet = $this->tableGateway->select();
        $data=array();
        foreach ($rowSet as $item){
            $data[$item->political_status_id] = $item->political_status_name;
        }
        return $data;
    }

    public function getPoliticalStatus($pid)
    {//查询

        $rowset = $this->tableGateway->select(array('political_status_id' => $pid));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}