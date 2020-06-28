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

class IpConfigForm extends Form
{

    protected $inputFilter;

    public function __construct()
    {
        parent::__construct('ipconfig');

        $this->add(array(
            'name' => 'ip_address1',
            'type' => 'Text',
            'options' => array(),
            'attributes' => array(
                'id' => 'ip_address1',
                'class' => 'span1',
                'onkeyup' => "this.value=this.value.replace(/\D/g,'')"
            ),
        ));
        $this->add(array(
            'name' => 'ip_address2',
            'type' => 'Text',
            'options' => array(),
            'attributes' => array(
                'id' => 'ip_address2',
                'class' => 'span1',
                'onkeyup' => "this.value=this.value.replace(/\D/g,'')"
            ),
        ));
        $this->add(array(
            'name' => 'ip_address3',
            'type' => 'Text',
            'options' => array(),
            'attributes' => array(
                'id' => 'ip_address3',
                'class' => 'span1',
                'onkeyup' => "this.value=this.value.replace(/\D/g,'')"
            ),
        ));
        $this->add(array(
            'name' => 'ip_address4',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ip_address4',
                'class' => 'span1',
                'onkeyup' => "this.value=this.value.replace(/\D/g,'')",
            ),
        ));
        $this->add(array(
            'name' => 'net_mask',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'net_mask',
                'class' => 'span3',
                'onkeyup' => "value=value.replace(/[^\d.]/g,'')",
                'placeholder' => '例：255.255.255.255'
            ),
        ));
        $this->add(array(//target_college Select
            'name' => 'default_gateway',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'default_gateway',
                'class' => 'span3',
                'onkeyup' => "value=value.replace(/[^\d.]/g,'')",
                'placeholder' => '例：255.255.255.255'
            ),
        ));

        $this->add(array(//submit Submit
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '确认',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(    //1.7 联系方式 phone Text
                'name' => 'ip_address1',
                'required' => true,        //必需的
                'filters' => array(
                    array('name' => 'StringTrim'),        //去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),        //不允许为空
                    array(
                        'name' => 'StringLength',    //限制长度
                        'options' => array(
                            'min' => 1,
                            'max' => 3,
                        ),
                    ),
                    array('name' => 'Digits'),    //必须为数字字符
                ),
            ));
            $inputFilter->add(array(    //1.7 联系方式 phone Text
                'name' => 'ip_address2',
                'required' => true,        //必需的
                'filters' => array(
                    array('name' => 'StringTrim'),        //去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),        //不允许为空
                    array(
                        'name' => 'StringLength',    //限制长度
                        'options' => array(
                            'min' => 1,
                            'max' => 3,
                        ),
                    ),
                    array('name' => 'Digits'),    //必须为数字字符
                ),
            ));
            $inputFilter->add(array(    //1.7 联系方式 phone Text
                'name' => 'ip_address3',
                'required' => true,        //必需的
                'filters' => array(
                    array('name' => 'StringTrim'),        //去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),        //不允许为空
                    array(
                        'name' => 'StringLength',    //限制长度
                        'options' => array(
                            'min' => 1,
                            'max' => 3,
                        ),
                    ),
                    array('name' => 'Digits'),    //必须为数字字符
                ),
            ));
            $inputFilter->add(array(    //1.7 联系方式 phone Text
                'name' => 'ip_address4',
                'required' => true,        //必需的
                'filters' => array(
                    array('name' => 'StringTrim'),        //去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),        //不允许为空
                    array(
                        'name' => 'StringLength',    //限制长度
                        'options' => array(
                            'min' => 1,
                            'max' => 3,
                        ),
                    ),
                    array('name' => 'Digits'),    //必须为数字字符
                ),
            ));
            $inputFilter->add(array(
                'name' => 'net_mask',
                'required' => true,        //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),        //不允许为空
                    array(
                        'name' => 'StringLength',    //限制长度
                        'options' => array(
                            'min' => 8,
                            'max' => 15,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'default_gateway',
                'required' => true,        //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),        //不允许为空
                    array(
                        'name' => 'StringLength',    //限制长度
                        'options' => array(
                            'min' => 8,
                            'max' => 15,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }


}