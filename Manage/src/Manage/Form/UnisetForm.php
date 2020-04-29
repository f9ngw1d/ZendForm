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

class uniSetForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;
    public function __construct($UniCode,$Uni)
    {
        // we want to ignore the name passed
        parent::__construct('uniset');

        $levelop = array('本科'=>'本科','专科'=>'专科');
        $remarkop = array('null'=>'否','民办'=>'是');
        $is985op = array('0'=>'否','1'=>'是');
        $is211op = array('0'=>'否','1'=>'是');
        $qualop = array('0'=>'否','1'=>'是');
        $this->add(array(
            'name' => 'university_id',
            'type' => 'text',
            'options' => array(
                'label' => '学校代码(必填)',
            ),
            'attributes'=>array(
                'id'=>'uniids',
            ),
        ));
        $this->add(array(
            'name' => 'university_name',
            'type' => 'text',
            'options' => array(
                'label' => '学校名称(必填)',
            ),
            'attributes'=>array(
                'id'=>'uninames',
            ),
        ));
        $this->add(array(
            'name' => 'SSDM',
            'type' => 'select',
            'options' => array(
                'label' => '所在省代码(必填)',
                'empty_option' => '请选择',
                'value_options' => $UniCode,
            ),
            'attributes'=>array(
                'id'=>'SSDMs',
            ),
        ));
        $this->add(array(
            'name' => 'SSDMC',
            'type' => 'select',
            'options' => array(
                'label' => '所在省(必填)',
                'empty_option' => '请选择',
                'value_options' => $Uni,
            ),
            'attributes'=>array(
                'id'=>'SSDMCs',
            ),
        ));

        $this->add(array(
            'name' => 'is985',
            'type' => 'select',
            'options' => array(
                'label' => '是否985(必填)',
                'empty_option' => '请选择',
                'value_options' => $is985op,
            ),
            'attributes'=>array(
                'id'=>'is985s',
            ),
        ));
        $this->add(array(
            'name' => 'is211',
            'type' => 'select',
            'options' => array(
                'label' => '是否211(必填)',
                'empty_option' => '请选择',
                'value_options' => $is211op,
            ),
            'attributes'=>array(
                'id'=>'is211s',
            ),
        ));
        $this->add(array(
            'name' => 'freetest_qualified',
            'type' => 'select',
            'options' => array(
                'label' => '是否有推免资格(必填)',
                'empty_option' => '请选择',
                'value_options' => $qualop,
            ),
            'attributes'=>array(
                'id'=>'freetest_qualifieds',
            ),
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => '添加',
                'class'=>'btn btn-primary',
                'id' => 'submit',
            )
        ));

    }
    public function getInputFilterSpecification()
    {
        return array(
            'university_name' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 20,
                        ),
                    ),
                )),
            'university_id' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                )),
            'SSDM' => array(
                'required' => true,
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ),
            'SSDMC' => array(
                'required' => true,
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ),
            'is985' => array(
                'required' => false,
            ),
            'is211' => array(
                'required' => false,
            ),
            'freetest_qualified' => array(
                'required' => false,
            ),
        );
    }
}