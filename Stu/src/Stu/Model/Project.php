<?php

namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Project implements InputFilterAwareInterface
{
    public $project_id;
    public $uid;
    public $project_name;
    public $abstract;
    public $conclusion;
    public $achievement;
    public $certificate;
    public $certificate_level;
    public $create_at;

    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data)
    {
        /*
 project_id
uid
project_name
abstract
conclusion
achievement
certificate
certificate_level
create_at
 */
        $this->project_id = (!empty($data['project_id'])) ? $data['project_id'] : null;
        $this->uid = (!empty($data['uid'])) ? $data['uid'] : null;
        $this->project_name = (!empty($data['project_name'])) ? $data['project_name'] : null;
        $this->abstract = (!empty($data['abstract'])) ? $data['abstract'] : null;
        $this->conclusion = (!empty($data['conclusion'])) ? $data['conclusion'] : null;
        $this->achievement = (!empty($data['achievement'])) ? $data['achievement'] : null;
        $this->certificate = (!empty($data['certificate'])) ? $data['certificate'] : null;
        $this->certificate_level = (!empty($data['certificate_level'])) ? $data['certificate_level'] : null;
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