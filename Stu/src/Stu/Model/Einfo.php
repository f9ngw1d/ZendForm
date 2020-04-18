<?php
/**
 * @author cry
 * @function 学生的电子凭证文件表 stu_einfo_map
 */
namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Einfo implements InputFilterAwareInterface{
    public $uid;
    public $eid;
    public $status;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新条件的时候用
    public function exchangeArray($data){
        $this->uid = (!empty($data['uid'])) ? $data['uid'] : null;
        $this->eid = (!empty($data['eid'])) ? $data['eid'] : null;
        $this->status = (!empty($data['status']))? $data['status'] : null;
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