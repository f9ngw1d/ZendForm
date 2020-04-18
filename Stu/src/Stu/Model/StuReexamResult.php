<?php

namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class StuReexamResult implements InputFilterAwareInterface
{
    public $id;
    public $course1;
    public $course2;
    public $course3;
    public $course4;
    public $marker1;
    public $marker2;
    public $marker3;
    public $marker4;
    public $total;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新条件的时候用
    public function exchangeArray($data){
        $this->course1 = (!empty($data['course1'])) ? $data['course1'] : null;
        $this->course2 = (!empty($data['course2'])) ? $data['course2'] : null;
        $this->course3 = (!empty($data['course3'])) ? $data['course3'] : null;
        $this->course4 = (!empty($data['course4'])) ? $data['course4'] : null;
        $this->marker1 = (!empty($data['marker1'])) ? $data['marker1'] : null;
        $this->marker2 = (!empty($data['marker2'])) ? $data['marker2'] : null;
        $this->marker3 = (!empty($data['marker3'])) ? $data['marker3'] : null;
        $this->marker4 = (!empty($data['marker4'])) ? $data['marker4'] : null;
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->total = (!empty($data['total'])) ? $data['total'] : null;
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

    }
}