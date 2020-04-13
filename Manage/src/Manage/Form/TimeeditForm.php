<?php

namespace Manage\Form;

use Manage\Model\SystemManagement;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class timeEditForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $staop = array('-1' => '自动', '1' => '手动开启');

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
            'attributes' => array(
                'id' => 'names'
            )
        ));

        $this->add(array(
            'type' => 'datetime',
            'name' => 'start_time',
            'options' => array(
                'label' => '开始时间'
            ),
            'attributes' => array(
                'id' => 'start_times'
            )
        ));

        $this->add(array(
            'type' => 'datetime',
            'name' => 'end_time',
            'options' => array(
                'label' => '结束时间'
            ),
            'attributes' => array(
                'id' => 'end_times'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'description',
            'options' => array(
                'label' => '备注(选填)'
            ),
            'attributes' => array(
                'id' => 'descriptions'
            )
        ));

        $this->add(array(
            'type' => 'select',
            'name' => 'status',
            'options' => array(
                'label' => '开关状态',
                'value_options' => $staop,
            ),
            'attributes' => array(
                'id' => 'status'
            )
        ));

    }
}