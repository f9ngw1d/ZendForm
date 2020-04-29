<?php
namespace Manage\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Filter\File\Rename;
use Zend\Session\Container;

class UnisearchForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;
    public function __construct($UniCode,$Uni)
    {
        // we want to ignore the name passed
        parent::__construct('unisearch');

        $is985op = array('0'=>'否','1'=>'是');
        $is211op = array('0'=>'否','1'=>'是');
        $qualop = array('0'=>'否','1'=>'是');
        $this->add(array(
            'name' => 'university_id',
            'type' => 'text',
            'options' => array(
                'label' => '学校代码(选填)',
            ),
            'attributes'=>array(
                'id'=>'uniid',
            ),
        ));
        $this->add(array(
            'name' => 'university_name',
            'type' => 'text',
            'options' => array(
                'label' => '学校名称(选填)',
            ),
            'attributes'=>array(
                'id'=>'uniname',
            ),
        ));
        $this->add(array(
            'name' => 'SSDM',
            'type' => 'select',
            'options' => array(
                'label' => '所在省代码(选填)',
                'empty_option' => '请选择',
                'value_options' => $UniCode,
            ),
            'attributes'=>array(
                'id'=>'SSDM',
            ),
        ));
        $this->add(array(
            'name' => 'SSDMC',
            'type' => 'select',
            'options' => array(
                'label' => '所在省(选填)',
                'empty_option' => '请选择',
                'value_options' => $Uni,
            ),
            'attributes'=>array(
                'id'=>'SSDMC',
            ),
        ));
        $this->add(array(
            'name' => 'is985',
            'type' => 'select',
            'options' => array(
                'label' => '是否985(选填)',
                'empty_option' => '请选择',
                'value_options' => $is985op,
            ),
            'attributes'=>array(
                'id'=>'is985',
            ),
        ));
        $this->add(array(
            'name' => 'is211',
            'type' => 'select',
            'options' => array(
                'label' => '是否211(选填)',
                'empty_option' => '请选择',
                'value_options' => $is211op,
            ),
            'attributes'=>array(
                'id'=>'is211',
            ),
        ));
        $this->add(array(
            'name' => 'freetest_qualified',
            'type' => 'select',
            'options' => array(
                'label' => '是否具有推免资格(选填)',
                'empty_option' => '请选择',
                'value_options' => $qualop,
            ),
            'attributes'=>array(
                'id'=>'freetest_qualified',
            ),
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => '查询',
                'class'=>'btn btn-primary',
            )
        ));
    }
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'university_name',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name'     => 'university_id',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name'		=> 'SSDM',
                'required'	=> false,		//必需的
            ));

            $inputFilter->add(array(
                'name'		=> 'SSDMC',
                'required'	=> false,		//必需的
            ));

            $inputFilter->add(array(
                'name'		=> 'is985',
                'required'	=> false,		//必需的
            ));

            $inputFilter->add(array(
                'name'		=> 'is211',
                'required'	=> false,		//必需的
            ));
            $inputFilter->add(array(
                'name'		=> 'freetest_qualified',
                'required'	=> false,		//必需的
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
    public function getInputFilterSpecification(){
    }
}