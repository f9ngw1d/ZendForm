<?php
namespace Manage\Form;

use Zend\Form\Form;

class UnderSubjectForm extends Form
{
    public function __construct($name=null)
    {
        parent::__construct('new_under_subject');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type'  => 'text',
            'attributes' => array(
                'type'  => 'text',
                'id'=>'id',
                'placeholder' => '请输入专业编号',
            ),
            'options' => array(
                'label' => '专业编号',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type'  => 'text',
            'attributes' => array(
                'id'=>'name',
                'placeholder' => '请输入专业名称',
            ),
            'options' => array(
                'label' => '专业名称',
            ),
        ));

        $this->add(array(
            'name' => 'relation1',
            'type'  => 'text',
            'attributes' => array(
                'id'=>'relation1',
                'placeholder' => '请输入关联一级专业的编号',
            ),
            'options' => array(
                'label' => '关联的一级学科',
            ),

        ));


        $this->add(array(
            'name' => 'relation2',
            'type'  => 'text',
            'attributes' => array(
                'id'=>'relation2',
                'placeholder' => '请输入关联二级专业的编号',
            ),
            'options' => array(
                'label' => '关联的二级学科',
            ),

        ));


        $this->add(array(
            'name' => 'submit',
            'type'  => 'submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class'=>'btn',
            ),

        ));
    }

    public function getInputFilterSpecification(){


    }
}
