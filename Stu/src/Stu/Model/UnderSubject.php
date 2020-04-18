<?php

namespace Stu\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UnderSubject implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $relation1;
    public $relation2;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->relation1 = (!empty($data['relation1'])) ? $data['relation1'] : null;
        $this->relation2 = (!empty($data['relation2'])) ? $data['relation2'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            //设置专业名称过滤
            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'), //过滤掉字符串中的xml和html标签
                    array('name' => 'StringTrim'),  //除去前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),//不允许为空
                    array(
                        'name' => 'StringLength',  //限制长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            )));

            //设置id过滤
            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'), //过滤掉字符串中的xml和html标签
                    array('name' => 'StringTrim'),  //除去前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),//不允许为空
                    array('name' => 'Zend\I18n\Validator\IsFloat')//必须为字母字符
                ),
            )));

            return $this->inputFilter;
        }
    }
}