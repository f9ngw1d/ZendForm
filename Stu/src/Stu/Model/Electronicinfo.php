<?php
/**
 * @author cry
 * @function 学生的电子凭证要求的文件表 stu_electronic_info
 */
namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Electronicinfo implements InputFilterAwareInterface{
    public $id;
    public $name;
    public $surfix;
    public $remark;
    public $maxnum ;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新条件的时候用
    public function exchangeArray($data){
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->surfix = (!empty($data['surfix']))? $data['surfix'] : null;
        $this->remark = (!empty($data['remark']))? $data['remark'] : null;
        $this->maxnum = (!empty($data['maxnum']))? $data['maxnum'] : 1;
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

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}