<?php
namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UsrTeacher implements InputFilterAwareInterface{
    public $staff_id;
    public $user_name;
    public $email;
    public $salt;
    public $password;
    public $create_time;
    public $update_at;

    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data){
        $this->staff_id = (!empty($data['staff_id'])) ? $data['staff_id'] : null;
        $this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;
        $this->email = (!empty($data['email']))?$data['email']:null;
        $this->salt = (!empty($data['salt']))?$data['salt']:null;
        $this->password = (!empty($data['password']))? $data['password'] : null;
        $this->create_time = (!empty($data['create_time']))?$data['create_time'] : null;
        $this->update_at = (!empty($data['update_at']))?$data['update_at'] : null;
    }

    public function getArrayCopy(){
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