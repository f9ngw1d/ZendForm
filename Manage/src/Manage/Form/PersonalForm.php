<?php
namespace Manage\Form;

use Zend\Form\Form;
use Zend\Form\Element\Select;
use Zend\Form\Element\Radio;

class PersonalForm extends Form
{
    public function __construct($name = null)
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
            'attributes' => array(
                'type'  => 'text',
                'id'=>'Uname',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'Realname',
            'attributes' => array(
                'type' => 'text',
                'id' => 'Realname',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Password',
            'attributes' => array(
                'type'  => 'password',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Password2',
            'attributes' => array(
                'type'  => 'password',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'new_pwd',
            'attributes' => array(
                'type'  => 'password',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'new_pwd2',
            'attributes' => array(
                'type'  => 'password',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Mobile',
            'attributes' => array(
                'type'  => 'text',
                'id'=>'Mobile',
                'class'=>'form-control',
            ),
        ));

        $this->add(array(
            'name'=>'Email',
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
                'value_options'=>array(
                ),
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
                'value_options'=>array(
                ),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class'=>'btn btn-primary',
            ),
        ));

    }
}