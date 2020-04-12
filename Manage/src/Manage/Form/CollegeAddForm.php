<?php
namespace Manage\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Session\Container;

class CollegeAddForm extends Form{

    public function __construct(){
        //给表添加元素
        //学院编号
        $this->add(array(
            'name' => 'college_id',
            'type' => 'number'
        ));
        //名称
        $this->add(array(
            'name' => 'college_name',
            'type' => 'text'
        ));
        //联系电话
        $this->add(array(
            'name' => 'phone',
            'type' => 'text'
        ));
        //网址
        $this->add(array(
            'name' => 'ip_address',
            'type' => 'url'
        ));
        $this->add(array(
            'name' => 'address',
            'type' => 'text'
        ));
        $this->add(array(//提交按钮
            'name'=>'add_college',
            'type'=>'submit',
            'attributes'=>array(
                'value'=>'确认添加学院',
                'id'=>'add_college',
                'class'=>'btn btn-primary',
            ),
        ));
    }
}
