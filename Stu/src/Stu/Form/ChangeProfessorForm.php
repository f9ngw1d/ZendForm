<?php
namespace Stu\Form;

use Zend\Form\Element;
use Zend\Form\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Validator\Identical\EmailAddress;
use Zend\I18n\Validator;

class ChangeProfessorForm extends Form implements InputFilterProviderInterface{

    protected $inputFilter;

    public function __construct($target_professor1,$target_professor2,$target_professor3){
        parent::__construct('changeprofessor');

        $this->add(array(//target_college Select
            'name' => 'target_professor',
            'type' => 'Select',
            'options' => array(
                'label' => '最终录取导师',
                'empty_option' => '请选择',
                'value_options' => $target_professor1,
            ),
            'attributes'=>array(
                'id'=>'target_professor',
            ),
        ));
        $this->add(array(//target_college Select
            'name' => 'target_professor2',
            'type' => 'Select',
            'options' => array(
                'label' => '意向导师2（可空）',
                'empty_option' => '请选择',
                'value_options' => $target_professor2,
            ),
            'attributes'=>array(
                'id'=>'target_professor2',
            ),
        ));
        $this->add(array(//target_college Select
            'name' => 'target_professor3',
            'type' => 'Select',
            'options' => array(
                'label' => '意向导师3（可空）',
                'empty_option' => '请选择',
                'value_options' => $target_professor3,
            ),
            'attributes'=>array(
                'id'=>'target_professor3',
            ),
        ));
        $this->add(array(//submit Submit
            'name'=>'submit2',
            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'确认修改',
                'id'=>'submit2button',
                'class'=>'btn btn-primary',
            ),
        ));
    }

    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'		=> 'target_professor',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'target_professor2',
                'required'	=> false,
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
                'allow_empty' => true,
            ));

            $inputFilter->add(array(
                'name'		=> 'target_professor3',
                'required'	=> false,
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
                'allow_empty' => true,
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }


    public function getInputFilterSpecification(){
    }
}