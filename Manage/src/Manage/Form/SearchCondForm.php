<?php

namespace Setting\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\Filter\File\Rename;
use Zend\Session\Container;

class SearchCondForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;

    public function __construct($collegeArr, $status_arr)
    {
        // we want to ignore the name passed
        parent::__construct('search-cond');

        $this->add(array(
            'name' => 'target_college',
            'type' => 'Select',
            'options' => array(
//                'disable_inarray_validator' => true,//加这一句，使之可以使用option以外的选项作为值，之后不会被过滤器拦截
//                'label' => '学院',
//                'empty_option' => '学院',
                'value_options' => $collegeArr,//下拉框的值
                'allow_empty' => true,
            ),
            'attributes' => array(
                'id' => 'target_college',
                'multiple' => true,
            ),
        ));//学院id
        $this->add(array(
            'name' => 'status',
            'type' => 'Select',
            'options' => array(
//                'disable_inarray_validator' => true,//加这一句，使之可以使用option以外的选项作为值，之后不会被过滤器拦截
//                'label' => '审核状态',
//                'empty_option' => '状态',
                'value_options' => $status_arr,
                'allow_empty' => true,
            ),
            'attributes' => array(
                'id' => 'status',
                'multiple' => true,
            ),
        ));//审核状态
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'options' => array(//                'label' => '姓名',
            ),
            'attributes' => array(
                'id' => 'user_name',
                'placeholder' => '姓名',
            ),
        ));

        $this->add(array(//submit
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '查询',
                'id' => 'submit',
                'class' => 'btn btn-primary',
            ),
        ));//提交键
    }

    public function getInputFilterSpecification()
    {
        return array(
            'target_college' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'allow_empty' => true,
            ),

            'user_name' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'allow_empty' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',    //验证长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 20,
                        ),
                    ),
                ),
            ),

            'status' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'allow_empty' => true,
            ),

            'submit' => array(
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',    //验证长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max' => 20,
                        ),
                    ),
                ),
            ),
        );
    }
}