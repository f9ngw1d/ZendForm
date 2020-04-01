<?php

namespace StuData\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TStuBase implements InputFilterAwareInterface
{
    public $uid;
    public $gender;
    public $political_status;
    public $nationality;
    public $idcard;
    public $graduate_university;
    public $graduate_college;
    public $graduate_subject;
    public $graduate_professional_class;
    public $graduate_profession;

    public $target_university;
    public $target_college;
    public $target_subject;
    public $target_profession;
    public $target_professor;
    public $target_professor2;
    public $target_professor3;

    public $value_cet4;
    public $value_cet6;

    public $pro_stu_num;
    public $grade_point;
    public $ranking;
    public $universitylevel;
    public $relation;
    public $remarks;
    public $apply_type;
    public $examid;                //注册暂无
    public $phone;
    public $foreign_language;
    public $gre_score;
    public $toefl_score;
    public $email;
    public $user_name;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data)
    {
        $this->uid = (!empty($data['uid'])) ? $data['uid'] : null;
        $this->gender = (!empty($data['gender'])) ? $data['gender'] : null;
        $this->political_status = (!empty($data['political_status'])) ? $data['political_status'] : null;
        $this->nationality = (!empty($data['nationality'])) ? $data['nationality'] : null;
        $this->idcard = (!empty($data['idcard'])) ? $data['idcard'] : null;
        $this->graduate_university = (!empty($data['graduate_university'])) ? $data['graduate_university'] : null;
        $this->graduate_college = (!empty($data['graduate_college'])) ? $data['graduate_college'] : null;
        $this->graduate_subject = (!empty($data['graduate_subject'])) ? $data['graduate_subject'] : null;
        $this->graduate_professional_class = (!empty($data['graduate_professional_class'])) ? $data['graduate_professional_class'] : null;
        $this->graduate_profession = (!empty($data['graduate_profession'])) ? $data['graduate_profession'] : null;

        $this->target_university = (!empty($data['target_university'])) ? $data['target_university'] : null;
        $this->target_college = (!empty($data['target_college'])) ? $data['target_college'] : null;
        $this->target_subject = (!empty($data['target_subject'])) ? $data['target_subject'] : null;
        $this->target_profession = (!empty($data['target_profession'])) ? $data['target_profession'] : null;

        $this->target_professor = (!empty($data['target_professor'])) ? $data['target_professor'] : null;
        $this->target_professor2 = (!empty($data['target_professor2'])) ? $data['target_professor2'] : null;
        $this->target_professor3 = (!empty($data['target_professor3'])) ? $data['target_professor3'] : null;

        $this->value_cet4 = (isset($data['value_cet4'])) ? $data['value_cet4'] : -1;
        $this->value_cet6 = (!empty($data['value_cet6'])) ? $data['value_cet6'] : null;

        $this->pro_stu_num = (!empty($data['pro_stu_num'])) ? $data['pro_stu_num'] : null;
        $this->grade_point = (!empty($data['grade_point'])) ? $data['grade_point'] : null;
        $this->ranking = (!empty($data['ranking'])) ? $data['ranking'] : null;
        $this->relation = (!empty($data['relation'])) ? $data['relation'] : null;
        $this->universitylevel = (!empty($data['universitylevel'])) ? $data['universitylevel'] : null;
        $this->remarks = (!empty($data['remarks'])) ? $data['remarks'] : null;

        $this->apply_type = (!empty($data['apply_type'])) ? $data['apply_type'] : null;
        $this->examid = (!empty($data['examid'])) ? $data['examid'] : null;

        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->foreign_language  = (!empty($data['foreign_language'])) ? $data['foreign_language'] : null;
        $this->gre_score = (!empty($data['gre_score'])) ? $data['gre_score'] : null;
        $this->toefl_score = (!empty($data['toefl_score'])) ? $data['toefl_score'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->user_name = (!empty($data['user_name'])) ? $data['user_name'] : null;

    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    //返回一个过滤器
    public function getInputFilter()
    {
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(    //1.1 申请类型 apply_type Select
                'name'		=> 'apply_type',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(    //1.2 姓名 user_nameText
                'name'		=> 'user_name',
                'required'	=> true,					//必需的
                'filters'  => array(
                    array('name' => 'StringTrim'),		//去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',	//限制长度
                        'options' => array(
                            'min'      => 1,
                            'max'      => 16,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(    //1.3 性别 gender Text
                'name'		=> 'gender',
                'required'	=> true,					//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(    //1.4 身份证号 idcard Text
                'name'		=> 'idcard',
                'required'	=> true,					//必需的
                'filters'  => array(
                    array('name' => 'StringTrim'),		//去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',	//限制长度18位身份证号
                        'options' => array(
                            'min'      => 18,
                            'max'      => 18,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(    //1.5 民族 nationality Text
                'name'		=> 'nationality',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',	//限制长度
                        'options' => array(
                            'max'      => 16,
                        ),
                    ),
                ),

            ));
            $inputFilter->add(array(    //1.6 政治面貌 political_status Select
                'name'		=> 'political_status',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(    //1.7 联系方式 phone Text
                'name'		=> 'phone',
                'required'	=> true,		//必需的
                'filters'  => array(
                    array('name' => 'StringTrim'),		//去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',	//限制长度
                        'options' => array(
                            'min'      => 11,
                            'max'      => 11,
                        ),
                    ),
                    array('name'    => 'Digits'),	//必须为数字字符
                ),
            ));
            $inputFilter->add(array(    //1.8 电子邮箱 email Text
                'name'		=> 'email',
                'required'	=> true,		//必需的
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),	//过滤空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',//限制长度
                        'options' => array(
                            'min'      => 3,
                            'max'      => 64,
                        ),
                    ),
                    array(//检验邮箱格式，Zend\Validator\Identical\EmailAddress专门用于检验邮箱格式
                        'name'    => 'EmailAddress'
                    ),
                ),
            ));
            $inputFilter->add(array(    //1.9 本科高校 graduate_university Text
                'name'		=> 'graduate_university',
                'required'	=> true,		//必需的
                'filters'  => array(
                    array('name' => 'StringTrim'),		//去除前后空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',	//限制长度
                        'options' => array(
                            'min'      => 3,
                            'max'      => 16,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(    //1.10 CET4 value_cet4
                'name'		=> 'value_cet4',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}