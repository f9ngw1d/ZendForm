<?php

namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Division implements InputFilterAwareInterface
{
/*YQXDM
YQXMC
SSDM
SSMC
DJSDM
DJSMC
XJSDM
XJSMC
QXDM
QXMC
BZ*/
    public $YQXDM;
    public $YQXMC;
    public $SSDM;
    public $SSMC;
    public $DJSDM;
    public $DJSMC;
    public $XJSDM;
    public $XJSMC;
    public $QXDM;
    public $QXMC;
    public $BZ;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data)
    {
        $this->YQXDM = (!empty($data['YQXDM'])) ? $data['YQXDM'] : null;
        $this->YQXMC = (!empty($data['YQXMC'])) ? $data['YQXMC'] : null;
        $this->SSDM = (!empty($data['SSDM'])) ? $data['SSDM'] : null;
        $this->SSMC = (!empty($data['SSMC'])) ? $data['SSMC'] : null;
        $this->DJSDM = (!empty($data['DJSDM'])) ? $data['DJSDM'] : null;
        $this->DJSMC = (!empty($data['DJSMC'])) ? $data['DJSMC'] : null;
        $this->XJSDM = (!empty($data['XJSDM'])) ? $data['XJSDM'] : null;
        $this->XJSMC = (!empty($data['XJSMC'])) ? $data['XJSMC'] : null;
        $this->QXDM = (!empty($data['QXDM'])) ? $data['QXDM'] : null;
        $this->QXMC = (!empty($data['QXMC'])) ? $data['QXMC'] : null;
        $this->BZ = (!empty($data['BZ'])) ? $data['BZ'] : null;
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