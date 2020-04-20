<?php

namespace  Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class Permission implements InputFilterAwareInterface{
    public $pid;
    public $name;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data){
        $this->pid = (!empty($data['pid'])) ? $data['pid'] : null;
        $this->name = (!empty($data['name']))? $data['name'] : null;
    }

    public function getArrayCopy(){
        return get_object_vars($this);
    }

    //InputFilterAwareInterface的方法，不需要是实现，但是这里不需要，所以直接抛出异常
    public function setInputFilter(InputFilterInterface $inputFilter){
        throw new \Exception("Not used");
    }
    //返回一个过滤器
    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'		=> 'pid',
                'required'	=> true,	//必需的
                'filters'	=> array(
                    array('name'=>'Int'),	//只能是整数
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'name',
                'required'	=> true,	//必需的
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),	//过滤空格
                ),
                'validators'=>array(	//验证器
                    array(
                        'name'		=>'name',	//验证长度
                        'options'	=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 32,
                        ),
                    ),//其它验证
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}