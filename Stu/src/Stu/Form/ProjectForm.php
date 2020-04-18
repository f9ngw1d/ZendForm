<?php
namespace Stu\Form;

use Zend\Form\Element;
use Zend\Form\Form;
//use Zend\Captcha\AdapterInterface;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Session\Container;

use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\Filter\File\Rename;

class ProjectForm extends Form implements InputFilterProviderInterface{
    public $inputFilter;
    private $uid;
    private $id;

    public function __construct($id){
        parent::__construct('project'.date("s"));
        $this->id = $id;
        //取当前用户的uid
        $containerUname = new Container('uid');
        $this->uid = $containerUname->item;

        //给表添加元素
        $this->add(array(//uname Text
            'name' => 'uid',
            'type' => 'Hidden',
            'options' => array(
                'label' => '用户编号',
            ),
            'attributes'=>array(
                'value'=>$this->uid,
            )
        ));
        //项目名称
        $this->add(array(
            'name' => 'project_name',
            'type' => 'Text',
            // 'attributes' => array(
            // 	'id' => 'project_name1',
            // ),
        ));
        //项目摘要
        $this->add(array(
            'name' => 'abstract',
            'type' => 'Textarea',
            // 'attributes' => array(
            // 	'id' => 'project_abstract1',
            // ),
        ));
        //项目研究结论
        $this->add(array(
            'name' => 'conclusion',
            'type' => 'Text',
            // 'attributes' => array(
            // 	'id' => 'project_conclusion1',
            // ),
        ));
        //项目成果
        $this->add(array(
            'name' => 'achievement',
            'type' => 'Textarea',
            'attributes' => array(
                //'id' => 'project_achievement1',
                'placeholder'=>'论文、专利等',
            ),
        ));

        //获奖凭证
        $this->add(array(
            'name' => 'certificate',
            'type' => 'File',
            'attributes' => array(
                'id' => 'certificate',
            ),
        ));

        //获奖等级：国家级/省市级
        $this->add(array(
            'name' => 'certificate_level',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'certificate_level',
            ),
            'options'=>array(
                //'label'=>'获奖等级'
                'value_options' => array(
                    '0'=>'请选择',
                    '1'=>'国家级',
                    '2'=>'省市级',
                ),
            ),
        ));

        $this->add(array(//提交按钮
            'name'=>'proj-submit',
            'type'=>'Submit',
            'attributes'=>array(
                'value'=>'添加科研或实践项目经历',
                'id'=>'proj-submit',
                'class'=>'btn btn-primary',
            ),
        ));
    }

    public function getInputFilterSpecification(){
        $tinytextFilter = array(
            'required' => true,
            'filters'	=> array(
                array('name'=>'StripTags'),	//过滤html标签
                array('name'=>'StringTrim'),//过滤空格
            ),
            'validators'=>array(
                array('name'=>'NotEmpty'),
                array(
                    'name'=>'StringLength',	//验证长度
                    'options'=>array(
                        'encoding'	=> 'UTF-8',
                        'min'		=> 1,
                        'max'		=> 255,
                    ),
                ),
            ),
            //'allow_empty' => true,
        );

        $realatedPath = __DIR__."/../../../../../public/img/upload/";

        $renameUpload= new Rename(array(//身份证号+后缀
            "target"  => $realatedPath."certificate/".$this->id."_".time().".png",
            "overwrite" => true,
        ));
        //验证文件扩展名
        $extention = new Extension('png,jpeg,jpg,bmp');
        //验证文件大小
        $size = new Size(array('min'=>'1', 'max'=>'2097152'));//2M
        //验证文件MIME类型
        $mime = new MimeType(array('image/png','image/jpeg','image/jpg','image/bmp','enableHeaderCheck' => true));


        return array(
            'project_name' => array(
                'required' => true,
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),//过滤空格
                ),
                'validators'=>array(
                    array('name'=>'NotEmpty'),
                    array(
                        'name'=>'StringLength',	//验证长度
                        'options'=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 20,
                        ),
                    ),
                ),
                //'allow_empty' => true,
            ),
            'abstract' => $tinytextFilter,
            'conclusion' => array(
                'required' => true,
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),//过滤空格
                ),
                'validators'=>array(
                    array('name'=>'NotEmpty'),
                    array(
                        'name'=>'StringLength',	//验证长度
                        'options'=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 64,
                        ),
                    ),
                ),
                'allow_empty' => true,
            ),
            'achievement' => $tinytextFilter,
            'certificate' => array(
                'required' => true,
                'filters'  => array(
                    $renameUpload,
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'certificate_level' => array(
                'required' => true,
                'filters'	=> array(
                    array('name'=>'StripTags'),	//过滤html标签
                    array('name'=>'StringTrim'),//过滤空格
                ),
                'validators'=>array(
                    array(
                        'name'=>'StringLength',	//验证长度
                        'options'=>array(
                            'encoding'	=> 'UTF-8',
                            'min'		=> 1,
                            'max'		=> 1,
                        ),
                    ),
                ),
                'allow_empty' => true,
            ),
        );
    }
}
