<?php

namespace Manage\Form;

use Manage\Model\SystemManagement;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class collegeAddForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id'
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'name',
            'options' => array(
                'label' => '项目名称'
            ),
        ));

        $this->add(array(
            'type' => 'datetime',
            'name' => 'start_time',
            'options' => array(
                'label' => '开始时间'
            ),
        ));

        $this->add(array(
            'type' => 'datetime',
            'name' => 'end_time',
            'options' => array(
                'label' => '结束时间'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'description',
            'options' => array(
                'label' => '备注(选填)'
            ),
        ));

        $this->add(array(
            'type' => 'select',
            'name' => 'status',
            'options' => array(
                'label' => '开关状态',
            ),

        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => '设置',
                'class' => 'btn btn-primary',
            )
        ));
    }
}