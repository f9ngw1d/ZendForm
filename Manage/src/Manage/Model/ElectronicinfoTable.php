<?php
/**
 * @author cry
 * @function 学生的电子凭证要求的文件表 stu_electronic_info
 */
namespace Manage\Model;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ElectronicinfoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }
    /**
     * @author cry
     * @function  查询学生所需要的凭证
     */
    public function fetchAll()
    {
        $rowSet = $this->tableGateway->select();
        //$row = $rowSet->current();
        $result = iterator_to_array($rowSet);
        return $result;
    }
    public function fetchAllArr()
    {//查询
        $rowSet = $this->tableGateway->select();
        $data=array();
        foreach ($rowSet as $item){
            $data[$item->id]["name"] = $item->name;
            $data[$item->id]["suffix"] = $item->surfix;
            $data[$item->id]["remark"] = $item->remark;
            $data[$item->id]["maxnum"] = $item->maxnum;
        }
        return $data;
    }
    public function getElectronicinfoByRemark($remark)
    {
        $rowSet = $this->tableGateway->select(array('remark' => $remark));
        $result = iterator_to_array($rowSet);
        return $result;
    }
    public function getElectronicinfo($id)
    {//查询
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        return $row;
    }


    public function getElectronicinfoByname($name)
    {//查询
        $rowSet = $this->tableGateway->select(array('name' => $name));
        $row = $rowSet->current();
        return $row;
    }

    public function saveElectronicinfo(Electronicinfo $e)
    {//增加 和 修改
        $data = array(
            'id' => $e->id,
            'name' => $e->name,
            'surfix' => $e->surfix,
            'remark' => $e->remark,
            'maxnum' => $e->maxnum,
        );
        $id = $e->id;
        if (!$this->getElectronicinfo($id)) {
            return $this->tableGateway->insert($data);
        } else {
            if ($this->getElectronicinfo($id)) {
                unset($data['id']);
                $this->tableGateway->update($data, array('id' => $id));
            }
        }
    }

    public function deleteElectronicinfo($id)
    {//删
        return $this->tableGateway->delete(array('id' => $id));
    }
}
