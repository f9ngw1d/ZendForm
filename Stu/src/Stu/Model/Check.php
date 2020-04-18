<?php
namespace Stu\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Check implements InputFilterAwareInterface{
    /*    status
    subject
    professor
    college
    school
    remarks
    subject_re
    college_re
    school_re
    remark_re
    create_at
    update_at
    */
    public $uid;
    public $status;
    public $subject;
    public $professor;
    public $college;
    public $school;
    public $remarks;
    public $subject_re;
    public $college_re;
    public $school_re;
    public $remark_re;
    public $create_at;
    public $update_at;
    public $live_check_status;
    public $live_check_staff;

    protected $inputFilter;
    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data){
        $this->uid = (!empty($data['uid'])) ? $data['uid'] : null;
        $this->status = (!empty($data['status']))? $data['status'] : null;
        $this->subject = (!empty($data['subject']))?$data['subject']:null;
        $this->professor = (!empty($data['professor']))?$data['professor']:null;
        $this->college = (!empty($data['college']))?$data['college']:null;
        $this->school = (!empty($data['school'])) ? $data['school'] : null;
        $this->remarks = (!empty($data['remarks'])) ? $data['remarks'] : null;
        $this->subject_re = (!empty($data['subject_re'])) ? $data['subject_re'] : null;
        $this->college_re = (!empty($data['college_re'])) ? $data['college_re'] : null;
        $this->school_re = (!empty($data['school_re'])) ? $data['school_re'] : null;
        $this->remark_re = (!empty($data['remark_re'])) ? $data['remark_re'] : null;
        $this->create_at = (!empty($data['create_at'])) ? $data['create_at'] : null;
        $this->update_at = (!empty($data['update_at'])) ? $data['update_at'] : null;
        $this->live_check_status = (!empty($data['live_check_status'])) ? $data['live_check_status'] : 13;
        $this->live_check_staff = (!empty($data['live_check_staff'])) ? $data['live_check_staff'] : null;
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