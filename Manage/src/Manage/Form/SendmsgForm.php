<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 2018/9/10
 * Time: 21:56
 */
//module/Basicinfo/src/Basicinfo/Form/EmailmsgForm.php
namespace Manage\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class SendmsgForm extends Form implements InputFilterProviderInterface
{
    protected $inputFilter;

    public function __construct()
    {
        parent::__construct('Sendmsg');

        //给表添加元素
        $this->add(array(
            'name'=>'receiver',
            'type'=>'Textarea',
            'attributes'=>array(
                'id'=>'receiver',
            ),
            'options'=>array(
//                'label'=>'收件人',
            ),
        ));

        $this->add(array(
            'name'=>'title',
            'type'=>'Text',
            'attributes'=>array(
                'id'=>'title',
            ),
            'options'=>array(
//                'label'=>'标题',
            ),
        ));

        $this->add(array(
            'name'=>'content',
            'type'=>'Textarea',
            'attributes'=>array(
                'id'=>'content',
            ),
            'options'=>array(
//                'label'=>'消息内容',
            ),
        ));

        $this->add(array(//submit Submit
            'name'=>'submit',
            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'发送消息',
                'id'=>'submitbutton',
                'class'=>'btn btn-primary',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        $filterArrNoEmpty = array(
            'required'	=> true,	//必需的
            'filters'	=> array(
                array('name'=>'StripTags'),	//过滤html标签
                array('name'=>'StringTrim'),	//过滤空格
            ),
            'validators'=>array(
                //array('name'=>'NotEmpty'),

                array(
                    'name'=>'StringLength',	//验证长度
                    'options'=>array(
                        'encoding'	=> 'UTF-8',
                        'min'		=> 1,
                        'max'		=> 16,
                    ),
                ),
            ),
            'allow_empty' => false,
        );
        return array('receiver'=>$filterArrNoEmpty);
    }
}