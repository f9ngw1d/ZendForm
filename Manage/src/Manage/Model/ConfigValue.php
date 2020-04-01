<?php

namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class ConfigValue implements InputFilterAwareInterface
{
    public $id;
    public $key_id;
    public $value_name;
    public $value_cn;
    public $value_desc;
    public $option;
    public $num;
    public $create_at;
    public $update_at;

    public $inputFilter;

    //数组转换成对象里的属性，插入新条件的时候用
    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->key_id = (!empty($data['key_id'])) ? $data['key_id'] : null;
        $this->value_name = (!empty($data['value_name'])) ? $data['value_name'] : null;
        $this->value_cn = (!empty($data['value_cn'])) ? $data[' value_cn'] : null;
        $this->value_desc = (!empty($data['value_desc'])) ? $data[' value_desc'] : null;
        $this->option = (!empty($data['option'])) ? $data[' option'] : null;
        $this->num = (!empty($data['num'])) ? $data[' num'] : null;
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