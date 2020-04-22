<?php
namespace Manage\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class PasswordForm extends Form implements InputFilterProviderInterface{
    //protected $captcha;
    protected $inputFilter;

    public function __construct(){
        parent::__construct('Password');

        //给表添加元素
        $this->add(array(//uid Text
            'name'=>'uid',
            'type'=>'hidden',
            'options'=>array(
                'label'=>'账号',
            ),
        ));
        $this->add(array(//密码 password类型的标签
            'name'=>'password1',
            'type'=>'Password',
            'options'=>array(
                'label'=>'旧密码',
            ),
        ));
        $this->add(array(//密码 password类型的标签
            'name'=>'password2',
            'type'=>'Password',
            'options'=>array(
                'label'=>'新密码',
            ),
        ));
        $this->add(array(//密码 password类型的标签
            'name'=>'password3',
            'type'=>'Password',
            'options'=>array(
                'label'=>'新密码',
            ),
        ));

        $this->add(array(//submit列
            'name'=>'submit',
            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'修改',
                'id'=>'submitbutton',
                'class'=>'btn btn-large btn-primary',
            ),
        ));
        //$this->setInputFilter(new InputFilter());
    }

    public function getInputFilterSpecification(){
        $captchaValidator = new ImgCaptchaValidator();
        return array(
            'uid'=>array(
                'required'	=> true,	//必需的
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),	//过滤空格
                ),
                'validators'=>array(
                    array('name'=>'NotEmpty'),
                    array(
                        'name'=>'StringLength',	//验证长度
                        'options'=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 0,
                            'max'		=> 48,
                        ),
                    ),
                    //array('name'    => 'EmailAddress'),
                ),
            ),
            'password1'=>array(
                'required'	=> true,	//必需的
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),	//过滤空格
                ),
                'validators'=>array(	//验证器
                    array(
                        'name'		=>'StringLength',	//验证长度
                        'options'	=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 48,
                        ),
                    ),//其它验证
                ),
            ),

            'password2'=>array(
                'required'	=> true,	//必需的
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),	//过滤空格
                ),
                'validators'=>array(	//验证器
                    array(
                        'name'		=>'StringLength',	//验证长度
                        'options'	=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 48,
                        ),
                    ),//其它验证
                ),
            ),
            'password3'=>array(
                'required'	=> true,	//必需的
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),	//过滤空格
                ),
                'validators'=>array(	//验证器
                    array(
                        'name'		=>'StringLength',	//验证长度
                        'options'	=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 48,
                        ),
                    ),//其它验证
                ),
            ),


        );
    }
}