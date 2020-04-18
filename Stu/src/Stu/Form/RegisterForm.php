<?php
/**
 * @author cry
 * @function 学生注册表单
 */
namespace Stu\Form;

use Zend\Form\Form;
use Zend\Form\Element\Number;
use Zend\Validator\Identical\EmailAddress;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;

class RegisterForm extends Form  implements InputFilterProviderInterface{

    protected $inputFilter;

    public function __construct($graduate_university_province,$nationalityArr,$political_statusArr,$graduate_university=null){
        parent::__construct('Register');
        $this->setAttribute('method','post');

        $this->add(array(//1.1 申请类型 apply_type Select
            'name'=>'apply_type',
            'type'=>'Select',
            'options'=>array(
                'label'=>'申请类型(必填)',
                'empty_option' => '请选择',
                'value_options' => array(
                    'putong' => '普通推免',
                    'zhibo' => '直博生',
                    'zhijiaotuan' => '支教团',
                    'techangsheng' => '特长生',
                    '2jia' => '2+2/3',
                    'gugan' => '少数民族骨干计划',
                    'shibing' => '退役大学生士兵计划',
                ),
            ),
            'attributes'=>array(
                'id'=>'apply_type',
            ),
        ));
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'options' => array(
                'label' => '姓名(必填)',
            ),
            'attributes'=>array(
                'id'=>'user_name',
            ),
        ));
        $this->add(array(//1.3 性别 gender Text
            'name' => 'gender',
            'type' => 'Select',
            'options' => array(
                'label' => '性别(必填)',
                'empty_option' => '请选择',
                'value_options' => array(
                    '1' => '女',
                    '2' => '男',
                ),
            ),
            'attributes'=>array(
                'id'=>'gender',
            ),
        ));
        $this->add(array(//1.4 身份证号 idcard Text
            'name' => 'idcard',
            'type' => 'Text',
            'options' => array(
                'label' => '身份证号(必填)',
            ),
            'attributes'=>array(
                'id'=>'idcard',
                'placeholder' => '18位身份证号',

            ),
        ));
//        $this->add(array(//1.5 民族 nationality Text
//            'name' => 'nationality',
//            'type' => 'Text',
//            'options' => array(
//                'label' => '民族(必填)',
//            ),
//            'attributes'=>array(
//                'id'=>'nationality',
//            ),
//        ));
        $this->add(array(//1.5 民族 nationality Text
            'name' => 'nationality',
            'type' => 'Select',
            'options' => array(
                'label' => '民族(必填)',
                'empty_option' => '请选择',
                'value_options' =>(!empty($nationalityArr)) ? $nationalityArr:null,
            ),
            'attributes'=>array(
                'id'=>'nationality',
            ),
        ));
        $this->add(array(//1.6 政治面貌 political_status Select
            'name'=>'political_status',
            'type'=>'Select',
            'options'=>array(
                'label'=>'政治面貌(必填)',
                'empty_option' => '请选择',
//                'value_options' => array(
//                    'masses' => '群众',
//                    'cyl' => '共青团员',
//                    'cpc_candidate' => '预备党员',
//                    'cpc' => '中共党员',
//                    ),
                'value_options' =>(!empty($political_statusArr)) ? $political_statusArr:null,

            ),
            'attributes'=>array(
                'id'=>'political_status',
            ),
        ));
        $this->add(array(//1.7 联系方式 phone Text
            'name' => 'phone',
            'type' => 'number',
            'options' => array(
                'label' => '手机号码(必填)',
            ),
            'attributes'=>array(
                'id'=>'phone',
                'placeholder' => '11位数字。例:138XXXX0011',
            ),
        ));
        $this->add(array(//1.8 电子邮箱 email Text
            'name' => 'email',
            'type' => 'text',
            'options' => array(
                'label' => '电子邮箱(必填)',
            ),
            'attributes' => array(
                'id' => 'email',
                'placeholder' => '格式：XXX@XXX',
            )
        ));
        /*
        $this->add(array(//1.9 本科高校 graduate_university Text
            'name' => 'graduate_university',
            'type' => 'Text',
            'options' => array(
                'label' => '本科高校(必填)',
            ),
            'attributes'=>array(
                'id'=>'graduate_university',
            ),
        ));*/
        $this->add(array(//1.9.1 本科高校所在省份  Select
            'name' => 'graduate_university_province',
            'type' => 'Select',
            'options' => array(
                'label' => '本科高校所在省份(必填)',
                'class' => "control-label",
                'empty_option' => '请选择所在省份',
                'value_options' => $graduate_university_province,
            ),
            'attributes'=>array(
                'id'=>'graduate_university_province',
                'class'=>'span4'
            ),
        ));
        $this->add(array(//1.9.3 本科高校  Select
            'name' => 'graduate_university',
            'type' => 'Select',
            'options' => array(
                'label' => '本科高校(必填)',
                'empty_option' => '请选择本科高校',
                'value_options' => (!empty($graduate_university)) ? $graduate_university:null,
            ),
            'attributes'=>array(
                'id'=>'graduate_university',
                'class'=>'span5'
            ),
        ));
        $this->add(array(//1.10 英语四级成绩 value_cet4 Text
            'name'=>'value_cet4',
            'type'=>'number',
            'options'=>array(
                'label'=>'英语四级成绩(必填)',
            ),
            'attributes'=>array(
                'id'=>'value_cet4',
                'placeholder' => '425-710之间整数。例：425',
            ),
        ));
        $this->add(array(//submit Submit
            'name'=>'registersubmit',
            'type'=>'button',
//            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'提交信息',
                'id'=>'registersubmit',
                'class'=>'btn btn-primary',
            ),
        ));
    }
/*
    public function getInputFilterSpecification(){

    }
*/
    public function getInputFilter(){
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
                            'max'      => 50,
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
                            'max'      => 64,
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
            $inputFilter->add(array(    //1.7 手机号码 phone Text
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
            $inputFilter->add(array(    ///1.9.1 本科高校所在省份
                'name'		=> 'graduate_university_province',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
//            $inputFilter->add(array(    ///1.9.2 本科高校所在城市
//                'name'		=> 'graduate_university_city',
//                'required'	=> true,		//必需的
//                'validators' => array(
//                    array('name' => 'NotEmpty'),		//不允许为空
//                ),
//            ));
            $inputFilter->add(array(    ///1.9.3 本科高校 graduate_university Select
                'name'		=> 'graduate_university',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(    ///1.9.3 本科高校 graduate_university Select
                'name'		=> 'graduate_university',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(    //1.10 CET4 value_cet4 Text
                'name'		=> 'value_cet4',
                'required'	=> true,		//必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                    array(
                        'name'    => 'StringLength',	//限制长度
                        'options' => array(
                            'min'      => 1,
                            'max'      => 3,
                        ),
                    ),
                ),
            ));


            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function getInputFilterSpecification(){//过滤图片
    }
}