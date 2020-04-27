<?php

namespace Manage\Form;

use Manage\Model\SystemManagement;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Form\Element;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Validator\Identical\EmailAddress;
use Zend\I18n\Validator;

class TimesetForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;
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
        ));

        $this->add(array(
            'type' => 'DateSelect',
            'name' => 'start_time',
            'options' => array(
                'label' => '开始时间'
            ),
        ));

        $this->add(array(
            'type' => 'DateSelect',
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
                'value_options' => $staop,
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
    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'		=> 'name',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'start_time',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'end_time',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'description',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
    public function getInputFilterSpecification(){

    }
}