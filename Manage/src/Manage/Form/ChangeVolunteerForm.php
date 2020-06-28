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

class ChangeVolunteerForm extends Form implements InputFilterProviderInterface{

    protected $inputFilter;

    public function __construct($targetcollege){
        parent::__construct('changevolunteer');

        $this->add(array(//target_college Select
            'name' => 'target_college',
            'type' => 'Select',
            'options' => array(
//                'label' => '调整报考院系到：',
                'empty_option' => '请选择',
                'value_options' => $targetcollege,
            ),
            'attributes'=>array(
                'id'=>'target_college',
            ),
        ));
        $this->add(array(//target_direction Text
            'name' => 'target_team',
            'type' => 'Select',
            'options' => array(
//                'label' => '调整报考组到：',
                'empty_option' => '请选择',
            ),
            'attributes'=>array(
                'id'=>'target_team',
            ),
        ));

        $this->add(array(//submit Submit
            'name'=>'submit',
            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'确认',
                'id'=>'submitbutton',
                'class'=>'btn btn-primary',
            ),
        ));
    }

    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'		=> 'target_college',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'target_team',
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