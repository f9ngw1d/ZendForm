<?php

namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class ConfigKey implements InputFilterAwareInterface
{
    public $id;
    public $key_name;
    public $key_cn;
    public $key_value;
    public $create_at;
    public $update_at;

    public $inputFilter;

    //数组转换成对象里的属性，插入新条件的时候用
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->key_name = (!empty($data['key_name'])) ? $data['key_name'] : null;
        $this->key_cn = (!empty($data['key_cn'])) ? $data['key_cn'] : null;
        $this->key_value = (!empty($data['key_value'])) ? $data[' key_value'] : null;
        $this->create_at = (!empty($data['create_at'])) ? $data[' create_at'] : null;
        $this->update_at = (!empty($data['update_at'])) ? $data[' update_at'] : null;
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