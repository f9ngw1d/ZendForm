<?php
namespace Stu\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class InfovalidatemailTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }

    public function fetchAll($paginated = false)
    {//获取全部
        if ($paginated) {
            $select = new Select('infovalidatemail');
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new Infovalidatemail());
            $pageAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $rs);
            $pagnator = new Paginator($pageAdapter);
            return $pagnator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * @author cry
     * @function:根据激活邮箱加密查询
     * @param $active
     * @return array|\ArrayObject|null
     */
    public function getValidatemail($active)
    {//查询
        $rowSet = $this->tableGateway->select(array('active' => $active));
        $row = $rowSet->current();
        return $row;
    }
    /**
     * @author cry
     * @function:查看存储激活邮箱的表单，是否已经存在了
     * @param $active
     * @return array|\ArrayObject|null
     */
    public function getValidatemailbyactive($active)
    {//查询
        $rowSet = $this->tableGateway->select(array('active' => $active));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    /**
     * @author cry
     * @function:查看存储激活邮箱的表单，是否已经存在了
     * @param $active
     * @return array|\ArrayObject|null
     */
    public function getIfValiateByactive($active)
    {//查询
        $rowSet = $this->tableGateway->select(array('active' => $active));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row->status; //1:激活
    }

    /**
     *
     *@author cry
     * @function:新增/修改
     * @param
     * @return int
     */
    public function saveValidatemail($email)
    {
        $data = array(
            'active' => $email['active'],
            'status' => $email['status']
        );
        $mail = $email['active'];
        if (!$this->getValidatemail($mail)) {
            return $this->tableGateway->insert($data);
        }
    }
    public function update($key){
        return $this->tableGateway->update($key['data'],$key['where']);
    }

    /**
     * @author cry
     * @param $email
     * @return int
     * @throws \Exception
     */
    public function deleteStuAllEinfo($email)
    {
        $active = md5($email);
        $res = $this->tableGateway->delete(array('active' => $active));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("del stu_validatemail uid:".$email." fail");
        }
    }
    public function deleteMaillist($mail)
    {//删
        return $this->tableGateway->delete(array('mail' => $mail));
    }

    public function deleteMaillistbyactive($active)
    {//删
        return $this->tableGateway->delete(array('active' => $active));
    }
}
