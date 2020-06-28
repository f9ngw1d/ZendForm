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

class DnsConfigForm extends Form
{

    protected $inputFilter;

    public function __construct()
    {
        parent::__construct('dnsconfig');

        $this->add(array(
            'name' => 'dns_name',
            'type' => 'Text',
            'options' => array(),
            'attributes' => array(
                'id' => 'dns',
                'class' => 'span3',
//                'onkeyup' => "this.value=this.value.replace(/\D/g,'')"
            ),
        ));
        $this->add(array(
            'name' => 'dns1',
            'type' => 'Text',
            'options' => array(),
            'attributes' => array(
                'id' => 'dns1',
                'class' => 'span3',
                'onkeyup' => "value=value.replace(/[^\d.]/g,'')",
                'placeholder' => '例：255.255.255.255'
            ),
        ));
        $this->add(array(
            'name' => 'dns2',
            'type' => 'Text',
            'options' => array(),
            'attributes' => array(
                'id' => 'dns2',
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
                'name' => 'dns_name',
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
                            'max' => 30,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'dns1',
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
                'name' => 'dns2',
                'required' => false,        //必需的
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
                'allow_empty' => true,
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }


}