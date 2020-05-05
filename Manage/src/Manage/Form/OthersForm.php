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

class OthersForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;
    public function __construct($roles,$search_college_arr)
    {
        parent::__construct('new-account');
        $this->setAttribute('method','post');
        $this->setAttribute('class','form-horizontal');

        $this->add(array(
            'name'=>'Uid',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'Uid',
            ),
        ));

        $this->add(array(
            'name' => 'Uname',
            'readonly'=>true,
            'options' => array(
                'label' => '用户名'
            ),
            'attributes' => array(
                'type' => 'text',
                'id' => 'Uname',
                'class'=>'form-control',
            )
        ));

        $this->add(array(
            'name' => 'Realname',
            'options' => array(
                'label' => '姓名'
            ),
            'attributes' => array(
                'type' => 'text',
                'id' => 'Realname',
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'Password',
            'options' => array(
                'label' => '密码'
            ),
            'attributes' => array(
                'id' => 'Password',
                'class'=>'form-control',
            )
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'Password2',
            'options' => array(
                'label' => '确认密码'
            ),
            'attributes' => array(
                'id' => 'Password2',
                'class'=>'form-control',
            )
        ));

        $this->add(array(
            'name' => 'Mobile',
            'options' => array(
                'label' => '移动电话'
            ),
            'attributes' => array(
                'type' => 'text',
                'id' => 'Mobile',
                'class'=>'form-control',
            )
        ));

        $this->add(array(
            'name' => 'Email',
            'options' => array(
                'label' => '电子邮箱'
            ),
            'attributes' => array(
                'type' => 'text',
                'id' => 'Email',
                'class'=>'form-control',
            )
        ));

        $this->add(array(
            'type' => 'select',
            'name' => 'YXSM',
            'options' => array(
                'label' => '院系所',
                'empty_option' => '请选择',
                'value_options'=>$search_college_arr
            ),
            'attributes' => array(
                'id' => 'YXSM',
                'class'=>'form-control',
            )
        ));

        $this->add(array(
            'name'=>'Rid',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'Rid',
                'class'=>'form-control',
            ),
            'options'=>array(
                'label' => '用户角色',
                'empty_option' => '请选择',
                'value_options'=>$roles,
            ),
        ));
    }
    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'		=> 'user_name',
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
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function getInputFilterSpecification(){
    }
}