<?php
/**
 * id int(11) AI PK
name varchar(45)
surfix int(11)
syxl datetime
remark datetime
ksfs mediumtext
 * Created by PhpStorm.
 * User: sz-pc
 * Date: 2018/7/31
 * Time: 9:31
 */
namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Msgqueue implements InputFilterAwareInterface{
    public $id;
    public $title;
    public $receiver;
    public $content;
    public $status;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新条件的时候用
    public function exchangeArray($data){
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->receiver = (!empty($data['receiver']))? $data['receiver'] : null;
        $this->content = (!empty($data['content']))? $data['content'] : null;
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