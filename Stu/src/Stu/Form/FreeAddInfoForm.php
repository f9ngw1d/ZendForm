<?php

namespace Stu\Form;

use Zend\Form\Element;
use Zend\Form\Form;
//use Zend\Captcha\AdapterInterface;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\Filter\File\Rename;
use Zend\Session\Container;

class FreeAddInfoForm extends Form implements InputFilterProviderInterface
{

    public $inputFilter;
    private $uid;
    private $id;

    public function __construct($id, $teachArr)
    {
        // we want to ignore the name passed
        $this->id = $id;
        parent::__construct('FreeAddInfo');
        $uploadclass = "span4";
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
            'attributes' => array(
                'value' => $this->uid,
            )
        ));
        //上传照片
        $this->add(array(
            'name' => 'photo',
            'type' => 'File',
            'attributes' => array(
                'id' => 'photo-upload',
            ),
        ));

        //上传成绩单
        for ($i = 1; $i <= 3; $i++) {
            $this->add(array(
                'name' => 'report' . $i,
                'type' => 'File',
                'attributes' => array(
                    'id' => 'report' . $i . '-upload',
                ),
            ));
        }
//		$this->add(array(
//			'name' => 'report1',
//			'type' => 'File',
//			'attributes' => array(
//				'id' => 'report1-upload',
//                //'class' => $uploadclass
//            ),
//		));

        //填写研究经历/自述
        // $this->add(array(
        // 	'name' => 'experience',
        // 	'type' => 'Textarea',
        // 	// 'options' => array(
        // 	// 	'label' => '研究经历/自述',
        // 	// ),
        // ));
        //意向导师 Select
        $this->add(array(
            'name' => 'idealprofessor',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                //'label'=>'意向导师',
                'empty_option' => '请选择',
                'value_options' => $teachArr,
            ),
        ));

        //上传推荐信
        for ($i = 1; $i <= 4; $i++) {
            $this->add(array(
                'name' => 'recommendation' . $i,
                'type' => 'File',
                'attributes' => array(
                    'id' => 'recomm' . $i . '-upload',
                ),
            ));
        }
        //上传外语考试证书
        for ($i = 1; $i <= 2; $i++) {
            $this->add(array(
                'name' => 'foreign_language' . $i,
                'type' => 'File',
                'attributes' => array(
                    'id' => 'foreign_language' . $i . '-upload',
                ),
            ));
        }
        //上传优秀营员证书
        $this->add(array(
            'name' => 'camp',
            'type' => 'File',
            'attributes' => array(
                'id' => 'camp-upload',
            ),
        ));

        //上传推免资格审核证明
        $this->add(array(
            'name' => 'qualification',
            'type' => 'File',
            'attributes' => array(
                'id' => 'qualification-upload',
            ),
        ));

        //提交按钮
        $this->add(array(//submit Submit
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '提交信息',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        //从session取uid
        //文件上传后保存到临时目录的filter
        $realtedPath = __DIR__ . "/../../../../../public/img/upload/";

        $renameUpload0 = new Rename(array(//身份证号+后缀
            "target" => $realtedPath . "photo/free/" . $this->id . ".png",
            "overwrite" => true,
        ));
//		$renameUpload1 = array();
//		for($i=1;$i<=3;$i++){
//            $renameUpload1[] = new Rename(array(//id+后缀
//                "target"    => $realtedPath."report/".$this->id."_".$i.".png",
//                "overwrite" => true,
//            ));
//        }
        $renameUpload11 = new Rename(array(//id+后缀
            "target" => $realtedPath . "report/" . $this->id . "_1.png",
            "overwrite" => true,
        ));
        $renameUpload12 = new Rename(array(//id+后缀
            "target" => $realtedPath . "report/" . $this->id . "_2.png",
            "overwrite" => true,
        ));
        $renameUpload13 = new Rename(array(
            "target" => $realtedPath . "report/" . $this->id . "_3.png",
            "overwrite" => true,
        ));
        $renameUpload2 = array();//推荐信的新文件路径
        for ($i = 1; $i <= 4; $i++) {
            $renameUpload2[] = new Rename(array(
                "target" => $realtedPath . "recomm/" . $this->id . "_$i.png",
                "overwrite" => true,
            ));
        }

        $renameUpload3 = array();//外语凭证的新文件路径
        for ($i = 1; $i <= 2; $i++) {
            $renameUpload3[] = new Rename(array(
                "target" => $realtedPath . "foreign_language/" . $this->id . "_$i.png",
                "overwrite" => true,
            ));
        }
        //优秀营员凭证的新文件路径
        $renameUpload4 = new Rename(array(//id+后缀
            "target" => $realtedPath . "camp/" . $this->id . ".png",
            "overwrite" => true,
        ));
        //推免资格凭证的新文件路径
        $renameUpload5 = new Rename(array(//id+后缀
            "target" => $realtedPath . "qualification/" . $this->id . ".png",
            "overwrite" => true,
        ));
        //验证文件扩展名
        $extention = new Extension('png,jpeg,jpg,bmp');
        //验证文件大小
        $size = new Size(array('min' => '1', 'max' => '2097152'));//,2M
        //验证文件MIME类型
        $mime = new MimeType(array('image/png', 'image/jpeg', 'image/jpg', 'image/bmp', 'enableHeaderCheck' => true));

        return array(
            'photo' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload0,
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'report1' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload11,
                    //$renameUpload1[0]
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'report2' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload12,
                    //$renameUpload1[1]
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'report3' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload13,
                    //$renameUpload1[2]
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            //          'experience'=>array(
            // 	'required'	=> true,
            // 	'filters'	=> array(
            // 		array('name'=>'StripTags'),	//过滤html标签
            // 		array('name'=>'StringTrim'),//过滤空格
            // 	),
            // 	'validators'=>array(
            // 		array('name'=>'NotEmpty'),
            // 		array(
            // 			'name'=>'StringLength',	//验证长度
            // 			'options'=>array(
            // 				'encoding'	=> 'UTF-8',
            // 				'min'		=> 1,
            // 				'max'		=> 500,
            // 			),
            // 		),
            // 	),
            // 	//'allow_empty' => true,
            // ),
            'idealprofessor' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),    //过滤html标签
                    array('name' => 'StringTrim'),//过滤空格
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',    //验证长度
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 16,
                        ),
                    ),
                ),
                'allow_empty' => true,
            ),
            'recommendation1' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload2[0],
                ),
                'validators' => array(
                    $extention,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'recommendation2' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload2[1],
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'recommendation3' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload2[2],
                ),
                'validators' => array(
                    $extention,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'recommendation4' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload2[3],
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'foreign_language1' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload3[0],
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'foreign_language2' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload3[1],
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'camp' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload4,
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
            'qualification' => array(
                'required' => true,
                'filters' => array(
                    $renameUpload5,
                ),
                'validators' => array(
                    $extention,
                    $size,
                    $mime,
                ),
                'allow_empty' => true,
            ),
        );
    }
}