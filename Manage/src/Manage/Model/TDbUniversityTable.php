<?php
/**
 * @author cry
 * table: db_university_free
 * 推免资格学校
 */
namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TDbUniversityTable implements InputFilterAwareInterface{
    public $university_name;
    public $university_id;
    public $is985;
    public $is211;
    public $freetest_qualified;
    public $SSDM;
    public $SSDMC;


    protected $inputFilter;
    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data){
        $this->university_name = (!empty($data['university_name'])) ? $data['university_name'] : null;
        $this->university_id = (!empty($data['university_id']))? $data['university_id'] : null;
        $this->is985 = (!empty($data['is985'])) ? $data['is985'] : null;
        $this->is211 = (!empty($data['is211'])) ? $data['is211'] : null;
        $this->freetest_qualified = (!empty($data['freetest_qualified   '])) ? $data['freetest_qualified'] : null;
        $this->SSDM = (!empty($data['SSDM'])) ? $data['SSDM'] : null;
        $this->SSDMC = (!empty($data['SSDMC'])) ? $data['SSDMC'] : null;

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