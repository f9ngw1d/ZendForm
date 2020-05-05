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

class CollegeAddForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'text',
            'name' => 'college_id',
            'options' => array(
                'label' => '学院编号'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'college_name',
            'options' => array(
                'label' => '学院名称'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'phone',
            'options' => array(
                'label' => '学院电话'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Url',
            'name' => 'ip_address',
            'options' => array(
                'label' => '学院官网网址'
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'address',
            'options' => array(
                'label' => '办公楼地址',
            ),

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
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'college_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'phone',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 7,
                            'max'      => 11,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'college_id',
                'required'	=> true,		//必需的
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'ip_address',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $inputFilter->add(array(
                'name'		=> 'address',
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