<?php
namespace Manage\Model;

class Staff{
    public $staff_id;
    public $staff_name;
    public $college_id;
    public $title;
    public $phone;
    public $cellphone;
    public $email;
    public $position;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data){
        $this->staff_id = (!empty($data['staff_id'])) ? $data['staff_id'] : null;
        $this->college_id = (!empty($data['college_id']))? $data['college_id'] : null;
        $this->staff_name = (!empty($data['staff_name'])) ? $data['staff_name'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->cellphone = (!empty($data['cellphone'])) ? $data['cellphone'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->position = (!empty($data['position'])) ? $data['position'] : null;
    }

}