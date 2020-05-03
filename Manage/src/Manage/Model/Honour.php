<?php

namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Honour implements InputFilterAwareInterface
{
    public $honour_id;
    public $uid;
    public $honour_name;
    public $specificdesc;
    public $certificate;
    public $certificate_level;
    public $honour_at;
    public $create_at;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data)
    {
        $this->honour_id = (!empty($data['honour_id'])) ? $data['honour_id'] : null;
        $this->uid = (!empty($data['uid'])) ? $data['uid'] : null;
        $this->honour_name = (!empty($data['honour_name'])) ? $data['honour_name'] : null;
        $this->specificdesc = (!empty($data['specificdesc'])) ? $data['specificdesc'] : null;
        $this->certificate = (!empty($data['certificate'])) ? $data['certificate'] : null;
        $this->certificate_level = (!empty($data['certificate_level'])) ? $data['certificate_level'] : null;
        $this->honour_at = (!empty($data['honour_at'])) ? $data['honour_at'] : null;
        $this->create_at = (!empty($data['create_at'])) ? $data['create_at'] : null;
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