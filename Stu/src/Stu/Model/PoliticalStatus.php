<?php

namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class PoliticalStatus implements InputFilterAwareInterface
{
    public $political_status_id;
    public $political_status_name;

    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data)
    {
        $this->political_status_id = (!empty($data['political_status_id'])) ? $data['political_status_id'] : null;
        $this->political_status_name = (!empty($data['political_status_name'])) ? $data['political_status_name'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    //InputFilterAwareInterface的方法，不需要是实现，但是这里不需要，所以直接抛出异常
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    //返回一个过滤器
    public function getInputFilter()
    {
    }
}