<?php
namespace Manage\Form;

use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Radio;

class PersonalForm extends Form
{
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
            'name'=>'Uname',
            'options' => array(
                'label' => '用户名',
            ),
            'attributes' => array(
                'type'  => 'text',
                'id'=>'Uname',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'Realname',
            'options' => array(
                'label' => '真实姓名',
            ),
            'attributes' => array(
                'type' => 'text',
                'id' => 'Realname',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Password',
            'options' => array(
                'label' => '密码',
            ),
            'attributes' => array(
                'type'  => 'password',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Password2',
            'options' => array(
                'label' => '确认密码',
            ),
            'attributes' => array(
                'type'  => 'password',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Mobile',
            'options' => array(
                'label' => '移动电话',
            ),
            'attributes' => array(
                'type'  => 'text',
                'id'=>'Mobile',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Email',
            'options' => array(
                'label' => '电子邮箱',
            ),
            'attributes' => array(
                'type'  => 'text',
                'id'=>'Email',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'YXSM',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'YXSM',
                'class'=>'form-control',
            ),
            'options'=>array(
                'label' => '院系所',
                'empty_option' => '请选择',
                'value_options' => $search_college_arr,
            ),
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
                'value_options'=> $roles,
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => '提交',
                'id' => 'submitbutton',
                'class'=>'btn btn-primary',
            ),
        ));

    }
}