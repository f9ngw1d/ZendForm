<?php
/**
 * @author cry
 * @function 民族对照表 stu_nationality
 */
namespace Stu\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class NationalityTable
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
     * @function 获取民族对照表 stu_nationality
     * @return array
     */
    public function fetchAllArr()
    {//查询
        $rowSet = $this->tableGateway->select();
        $data=array();
        foreach ($rowSet as $item){
//            $data[$item->id]["nationality_id"] = $item->nationality_id;
//            $data[$item->id]["nationality_name"] = $item->nationality_name;
            $data[$item->nationality_id] = $item->nationality_name;
        }
        return $data;
    }

    public function getNationality($nid)
    {//查询

        $rowset = $this->tableGateway->select(array('nationality_id' => $nid));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
}