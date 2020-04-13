<?php

namespace Manage\Form;

use Manage\Model\SystemManagement;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class CollegeAddForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'text',
            'name' => 'college_id',
            'options' => array(
                'label' => '学院编号'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'college_name',
            'options' => array(
                'label' => '学院名称'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'phone',
            'options' => array(
                'label' => '学院电话'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'ip_address',
            'options' => array(
                'label' => '网址'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'address',
            'options' => array(
                'label' => '办公楼地址',
            ),

        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => '确认添加学院',
                'class' => 'btn btn-primary',
            )
        ));
    }
}