<?php
/**
 * @author cry
 * @function 学生的电子凭证文件表 stu_einfo_map
 */
namespace Manage\Model;
use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class EinfoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }
    public function fetchAll()
    {
        $rowSet = $this->tableGateway->select();
        //$row = $rowSet->current();
        $result = iterator_to_array($rowSet);
        return $result;
    }

    /**
     * @author cry
     * @function 学生电子凭证必填项是否上传完毕
     * @param $uid
     * @return bool|string
     */
    public function getStuEinfoStatus($uid){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $sl = new Select();
        $sl->from( array( 're_einfo'=>'stu_electronic_info' ) )
            ->columns(array('name'=>'name'))
            ->join(array('stu' => 'stu_einfo_map'),'stu.eid = re_einfo.id')
            ->where(' stu.uid = '.$uid.' and re_einfo.remark = 1  and stu.status = 0');

        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        $result_arr = iterator_to_array($result_set);

        if($result_arr){
            $noupload = "";
            foreach ($result_arr as $key => $row){
                $noupload = $noupload ." \'".$row['name']." \'";
            }
            return $noupload;//没有进行上传的必填项的字符串
//            return  true;
        }else{
            return false;//必填项全部上传了
        }

    }
    public function getStuEinfo($uid,$eid){
        $rowSet = $this->tableGateway->select(array('uid' => $uid,'eid' => $eid));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }
    public function getStuEinfoByUid($uid){
        $resultSet = $this->tableGateway->select(array('uid' => $uid));
        $stu_einfoArr = array();
        if($resultSet){
            foreach ($resultSet as $key => $row) {
                $stu_einfoArr[$row->eid] = $row->status;
            }
        }
        return $stu_einfoArr;
    }

//    public function fetchAllArr()
//    {//查询
//        $rowSet = $this->tableGateway->select();
//        $data=array();
//        foreach ($rowSet as $item){
//            $data[$item->id]["name"] = $item->name;
//            $data[$item->id]["suffix"] = $item->surfix;
//            $data[$item->id]["remark"] = $item->remark;
//        }
//        return $data;
//    }
//
//    public function getElectronicinfoByname($name)
//    {//查询
//        $rowSet = $this->tableGateway->select(array('name' => $name));
//        $row = $rowSet->current();
//        return $row;
//    }
    /**
     * @param $uid
     * @param $eid
     * @param $status
     * @return int
     * @throws \Exception
     */
    public function saveStuEinfo($uid,$eid,$status)
    {//增加 和 修改
        $data = array(
            'uid' => $uid,
            'eid' => $eid,
            'status' => (int)$status,

        );
        if (!$this->getStuEinfo($uid,$eid)) {
//            return $this->tableGateway->insert($data);
            $res = $this->tableGateway->insert($data);
            if($res){
                return $res;
            }else{
                throw new \Exception("insert stu_einfofail");
            }
        } else if($this->getStuEinfo($uid,$eid)){
            unset($data['uid']);
//            return $this->tableGateway->update($data, array('uid' => $uid,'eid' => $eid));
            $res = $this->tableGateway->update($data, array('uid' => $uid,'eid' => $eid));
            if($res){
                return $res;
            }else{
                throw new \Exception("update stu_einfo fail");
            }
        }
    }

    public function deleteStuEinfo($uid,$eid)
    {//删
        return $this->tableGateway->delete(array('uid' => $uid,'eid' => $eid));

    }

    /**
     * @author lrn
     * @param $uid
     * @return int
     * @throws \Exception
     */
    public function deleteStuAllEinfo($uid)
    {
        $res = $this->tableGateway->delete(array('uid' => $uid));
//        var_dump($res);
//        die();
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
            throw new \Exception("delete stu_einfo_map uid=".$uid." fail");
        }
    }
}
