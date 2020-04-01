<?php
namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\In;
use Zend\Tag\Item;

class ConfigKeyTable
{
    public $tableGateway;
    public $basetutorTable;
    protected $adapter;
    private $key_table;
    private $value_table;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $tableGateway->getAdapter();
        $this->key_table = 'config_key';
        $this->value_table = 'config_value';
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getValueColumns(){
        return array('*');
    }

    /**
     * @param $key_name string|array 传入需要查询的key不可空，空就返回空数组
     * @param array $value_name string|array 传入需要查询的value的name，可以空，可以为字符串，可以为array(string,string)
     * @param bool $with_value_name bool 是否需要把value_name作为返回值数组的key
     * @param $key_value_only boolean 返回的数组是否只需要key=>value
     * @return array|\Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getConfigValueByKey($key_name,$value_name = array(),$with_value_name = true,$key_value_only = false){
        $key_result = $this->getConfigKey($key_name,false,false);
        if(!$key_result){
            return array();
        }
        $key_id = $key_result[0]['id'];

        $sql = new Sql($this->adapter);
        $sl = new Select();
        $sl->from(array('val' => $this->value_table));
        if(!empty($key_id)){
            $sl->where(array('key_id'=>$key_id));
        }
        if(!empty($value_name)) {
            if(is_array($value_name)){
                $sl->where(new In('value_name',$value_name));
            }
            else{
                $sl->where(array('value_name'=>$value_name));
            }
        }

        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();

        $return_arr = iterator_to_array($result_set);
        if($key_value_only){
            $return_arr_with_key = array();
            foreach ($return_arr as $item){
                $return_arr_with_key[$item['value_name']] = $item['value_cn'];
            }
            return $return_arr_with_key;
        }
        if($with_value_name){
            $return_arr_with_key = array();
            foreach ($return_arr as $item){
                $return_arr_with_key[$item['value_name']] = $item;
            }
            return $return_arr_with_key;
        }
        return $return_arr;
    }


    /**====================================================================================
    ================================关于config_key表的处理==================================
    ====================================================================================**/

    /**
     * 根据配置项的key，获取配置项的值
     * @param $key_name string|array 传入需要查询的key
     * @param $one_value_only boolean 当只传入一个key时，是否只返回第一个匹配结果的key_value，默认为只返回一个值
     * @param $with_key_name boolean 返回的数组key是否为传入的key_name
     * @return array|\Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getConfigKey($key_name,$one_value_only = true,$with_key_name = true){
        $sql = new Sql($this->adapter);
        $sl = new Select();
        $sl->from($this->key_table);
        if(!empty($key_name)) {
            if(is_array($key_name)){
                $sl->where(new In('key_name', $key_name));
            }
            else{
                $sl->where(array('key_name'=>$key_name));
            }
        }
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();

        $return_arr = iterator_to_array($result_set);
        if($one_value_only && !is_array($key_name) && $return_arr){
            return $return_arr[0]['key_value'];
        }

        if($with_key_name){
            $result_with_key = array();
            foreach ($return_arr as $item){
                $result_with_key[$item['key_name']] = $item;
            }
            return $result_with_key;
        }
        return $return_arr;
    }

    /**
     * getConfigKeyByFilter根据条件查询configkey
     * @param $filter_arr array 查询的条件数组
     * @return array|\Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getConfigKeyByFilter($filter_arr){
        $sql = new Sql($this->adapter);
        $sl = new Select();
        $sl->from($this->key_table);
        foreach ($filter_arr as $key => $value) {
            if(is_array($value)){
                $sl->where(new In($key, $value));
            }
            else{
                $sl->where(array($key=>$value));
            }
        }
        // echo $sql->getSqlStringForSqlObject($sl)."<br/>";
        $statement = $sql->prepareStatementForSqlObject($sl);
        $result_set = $statement->execute();
        return $result_set;
    }

    /**
     * saveConfigKey 根据传入的data数组，更新数据库中config_key这一表，插入时需传入key_name字段
     * @param $data array 新的数据
     * @return bool 成功或失败
     */
    public function saveConfigKey($data = array()){
        if(!isset($data['id'])){
            return false;
        }
        //先查询有没有
        $id = $data['id'];
        if($this->getConfigKeyByFilter(array('id'=>$id))->current()){
            //有则更新
            // echo "有这个id,id = ".$data['id'].",应该update";
            unset($data['id']);
            if(isset($data['key_name'])){
                unset($data['key_name']);
            }
            $data['update_at'] = date('Y-m-d H:i:s');//设置update时间
            return $this->tableGateway->update($data,array('id'=>$id));
        }
        else{
            //没有插入
            if(!isset($data['key_name'])){
                // echo "新插入的值没有key_name";
                return false;
            }
            // echo "没有这个id，id= ".$data['id'].", 应该insert";
            return $this->tableGateway->insert($data);
        }
    }


//    public function getBasetutorTable() {
//        if ($this->basetutorTable) {
//            return $this->basetutorTable;
//        }
//        $resultSetPrototype = new ResultSet();
//        $resultSetPrototype->setArrayObjectPrototype(new BaseTutor());
//        $newTableGateway =  new TableGateway("base_tutor", $this->adapter,null,$resultSetPrototype);
//        $table = new BaseTutorTable($newTableGateway);
//        $this->basetutorTable = $table;
//        return $table;
//    }
}