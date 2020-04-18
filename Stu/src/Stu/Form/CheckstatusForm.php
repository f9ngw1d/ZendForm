<?php
namespace Stu\Form;

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

class CheckstatusForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;
    public function __construct($status)
    {
        // we want to ignore the name passed
        parent::__construct('staffinfo');
        $this->add(array(
            'name' => 'live_check_status',
            'type' => 'Select',
            'options' =>array(
                'label' => '选择不通过原因：',
                'empty_option' =>'请选择状态',
                'value_options' => $status,
            ),
            'attributes'=>array(
                'id'=>'live_check_status',
            ),
        ));
        $this->add(array(
            'name' => 'submit2',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '现场审核不通过',
                'id' => 'submit2button',
                'class'=>'btn btn-primary',
            ),
        ));
        $this->add(array(
            'name' => 'submit3',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '现场审核通过',
                'id' => 'submit3button',
                'class'=>'btn btn-primary',
            ),
        ));
    }
    public function getInputFilterSpecification(){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'live_check_status',
                'required' => false,
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
                'allow_empty' => true,
            ));
            $this->inputFilter = $inputFilter;
        }
        //var_dump($this->inputFilter);
        return $this->inputFilter;
    }
}