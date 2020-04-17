<?php

/**
 * Created by PhpStorm.
 * User: ly
 * Date: 2020/4/16
 * Time: 11:33
 */

//module/Login/src/Login/Form/LoginForm.php
namespace Manage\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface
{
    //protected $captcha;
    protected $inputFilter;

    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('login');//忽视外部传入给AlbumForm的参数，直接用album作为表名
        //$this->captcha = $captcha;

        //给表添加元素
        $this->add(array(//uid Text
            'name' => 'email',
            'type' => 'Text',
            // 'options'=>array(
            // 	'label'=>'邮箱',
            // ),
            'attributes' => array(
                'placeholder' => '邮箱',
            ),
        ));
        $this->add(array(//密码 password类型的标签
            'name' => 'password',
            'type' => 'Password',
            // 'options'=>array(
            // 	'label'=>'密码',
            // ),
            'attributes' => array(
                'placeholder' => '密码',
            ),
        ));

        $this->add(array(//验证码
            'name' => 'captcha',
            'type' => 'Text',
            // 'options'=>array(
            // 	'label'=>'验证码',
            // 	//'captcha' => new Captcha\Dumb(),
            // ),
            'attributes' => array(
                'placeholder' => '验证码',
                'class' => 'captcha-input',
            ),
        ));


        $this->add(array(//submit列
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '登录',
                'id' => 'submitbutton',
                'class' => 'btn btn-large btn-primary',
            ),
        ));
        //$this->setInputFilter(new InputFilter());
    }

    public function getInputFilterSpecification()
    {
        $captchaValidator = new ImgCaptchaValidator();
        return array(
            'email' => array(
                'required' => true,    //必需的
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                    array(
                        'name' => 'StringLength',    //验证长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 45,
                        ),
                    ),
                    array('name' => 'EmailAddress'),
                ),
            ),
            'password' => array(
                'required' => true,    //必需的
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'validators' => array(    //验证器
                    array(
                        'name' => 'StringLength',    //验证长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 48,
                        ),
                    ),//其它验证
                ),
            ),
            'captcha' => array(
                'required' => true,    //必需的
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),    //过滤空格
                ),
                'validators' => array(    //验证器
                    array(
                        'name' => 'StringLength',    //验证长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 4,
                            'max' => 4,
                        ),
                    ),//其它验证
                    $captchaValidator,
                ),
            ),

        );
    }
}