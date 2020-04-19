<?php
namespace Manage\Form;

use Zend\Form\Element;
use Zend\Form\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Validator\Identical\EmailAddress;
use Zend\I18n\Validator;

class ChangeStatusForm extends Form implements InputFilterProviderInterface{

    protected $inputFilter;

    public function __construct($targetcollege){
        parent::__construct('changevolunteer');

        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'options' => array(
                'label' => '姓名',
            ),
            'attributes'=>array(
                'id'=>'user_name',
            ),
        ));
        $this->add(array(
            'name' => 'now_status',
            'type' => 'Text',
            'options' => array(
                'label' => '当前状态',
            ),
            'attributes'=>array(
                'id'=>'now_status',
            ),
        ));
        $this->add(array(//target_college Select
            'name' => 'target_status',
            'type' => 'Select',
            'options' => array(
                'label' => '变更后状态',
                'empty_option' => '请选择',
                'value_options' => $targetstatus,
            ),
            'attributes'=>array(
                'id'=>'target_status',
            ),
        ));

        $this->add(array(//submit Submit
            'name'=>'submit',
            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'确认修改',
                'id'=>'submitbutton',
                'class'=>'btn btn-primary',
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
                'name'		=> 'now_status',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'target_status',
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