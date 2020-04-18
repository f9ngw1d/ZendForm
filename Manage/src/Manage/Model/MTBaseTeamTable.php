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

class MTBaseTeamTable
{
    protected $tableGateway;
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

    public function getTeam($team_id)
    {//查询
        $rowset = $this->tableGateway->select(array('team_id' => $team_id));
        $row = $rowset->current();
        if (!$row) {
			throw new \Exception("Could not find row $team_id");
        }
        return $row;
    }

    public function saveTeam(MTBaseTeam $base_team)
    {//增加 和 修改
        $data = array(
            'team_name' => $base_team->team_name,
            'college_id' => $base_team->college_id,
            'leader_id' => $base_team->leader_id,
            'start_time' => $base_team->start_time,
            'end_time' => $base_team->end_time,
            'stu_num' => $base_team->stu_num,
            'introduction' => $base_team->introduction,
            'college_link' => $base_team->college_link,
        );
		$team_id = (int)$base_team->team_id;
		if ($team_id == 0) {
			$this->tableGateway->insert($data);
		} else {
			if ($this->getTeam($team_id)) {
				$this->tableGateway->update($data, array('team_id' => $team_id));
			} else {
				throw new \Exception('User ID does not exist');
			}
		}
    }

    public function deleteTeam($team_id)
    {
        $res = $this->tableGateway->delete(array('team_id' => $team_id));
        if($res){
            return $res;
        }else{//失败则抛出异常，for事务
        	echo "fail";
            throw new \Exception("del base_team uid:". $team_id. " fail");
        }
    }
    public function getTeamKey($key_name,$one_value_only = true,$with_key_name = true){
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
}

?>