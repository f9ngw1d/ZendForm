<?php

namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class MTBaseTeam implements  InputFilterAwareInterface
{
    public $team_id;
    public $team_name;
    public $college_id;
    public $leader_id;
    public $start_time;
    public $end_time;
    public $stu_num;
    public $introduction;
    public $college_link;
    protected $inputFilter;
    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data){
        $this->team_id = (!empty($data['team_id'])) ? $data['team_id'] : null;
        $this->team_name = (!empty($data['team_name']))? $data['team_name'] : null;
        $this->college_id = (!empty($data['college_id']))?$data['college_id'] : null;
        $this->leader_id = (!empty($data['leader_id'])) ? $data['leader_id'] : null;
        $this->start_time = (!empty($data['start_time'])) ? $data['start_time'] : null;
        $this->end_time = (!empty($data['end_time'])) ? $data['end_time'] : null;
        $this->stu_num = (!empty($data['stu_num'])) ? $data['stu_num'] : null;
        $this->introduction = (!empty($data['introduction'])) ? $data['introduction'] : null;
        $this->college_link = (!empty($data['college_link'])) ? $data['college_link'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    //InputFilterAwareInterface的方法，不需要是实现，但是这里不需要，所以直接抛出异常
    public function setInputFilter(InputFilterInterface $inputFilter){
        throw new \Exception("Not used");
    }

    //返回一个过滤器
    public function getInputFilter(){
    }
}

