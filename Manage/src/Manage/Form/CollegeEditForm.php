<?php

namespace Manage\Form;

use Manage\Model\SystemManagement;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class CollegeEditForm extends Form
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
            'attributes' => array(
                'id' => 'college_ids'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'college_name',
            'options' => array(
                'label' => '学院名称'
            ),
            'attributes' => array(
                'id' => 'college_names'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'phone',
            'options' => array(
                'label' => '学院电话'
            ),
            'attributes' => array(
                'id' => 'phones'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'ip_address',
            'options' => array(
                'label' => '网址'
            ),
            'attributes' => array(
                'id' => 'ip_addresss'
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'address',
            'options' => array(
                'label' => '办公楼地址',
            ),
            'attributes' => array(
                'id' => 'addresss',
            )
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