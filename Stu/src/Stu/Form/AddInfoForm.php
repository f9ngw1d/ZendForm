<?php
/**
 * @author cry
 * @function 学生补充信息 添加基础信息的表单
 */
namespace Stu\Form;

use Zend\Form\Form;
use Zend\Form\Element\Number;
//use Zend\Validator\Identical\EmailAddress;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilter;

class AddInfoForm extends Form  implements InputFilterProviderInterface{

    protected $inputFilter;

    public function __construct($graduate_subjectArr,$target_collegeArr,$newform=null){
        parent::__construct('Register');
        $this->setAttribute('method','post');


        $this->add(array(//1.1 本科院系（必填）
            'name' => 'graduate_college',
            'type' => 'Text',
            'options' => array(
                'label' => '本科院系（必填）',
            ),
            'attributes'=>array(
                'id'=>'graduate_college',
            ),
        ));

        $this->add(array(//1.2 本科学科门类（必填）
            'name'=>'graduate_subject',
            'type'=>'Select',
            'options'=>array(
                'label'=>'本科学科门类（必填）',
                'empty_option' => '请选择',
                'value_options' => $graduate_subjectArr,

            ),
            'attributes'=>array(
                'id'=>'graduate_subject',
            ),
        ));

        $this->add(array(//1.3 本科专业类（必填）
            'name'=>'graduate_professional_class',
            'type'=>'Select',
            'options'=>array(
                'label'=>'本科专业类（必填）',
                'empty_option' => '请选择',
                'value_options' => (!empty($newform['graduate_professional_class'])) ? $newform['graduate_professional_class']:null,
            ),
            'attributes'=>array(
                'id'=>'graduate_professional_class',
            ),
        ));

        $this->add(array(//1.4 本科专业（必填）
            'name'=>'graduate_profession',
            'type'=>'Select',
            'options'=>array(
                'label'=>'本科专业（必填）',
                'empty_option' => '请选择',
                'value_options' => (!empty($newform['graduate_profession'])) ? $newform['graduate_profession']:null,
            ),
            'attributes'=>array(
                'id'=>'graduate_profession',
            ),
        ));
        $this->add(array(//1.5 本专业同年级人数（必填）
            'name' => 'pro_stu_num',
            'type' => 'number',
            'options' => array(
                'label' => '本专业同年级人数（必填）',
            ),
            'attributes'=>array(
                'id'=>'pro_stu_num',
                'placeholder' => '输入整数即可。例：24',
            ),
        ));
        $this->add(array(//1.6 本专业排名（必填）
            'name' => 'ranking',
            'type' => 'number',
            'options' => array(
                'label' => '本专业排名（必填）',
            ),
            'attributes'=>array(
                'id'=>'ranking',
                'placeholder' => '输入整数即可。例：1',
            ),
        ));
        $this->add(array(//1.7 本科绩点（必填）
            'name' => 'grade_point',
            'type' => 'number',
            'options' => array(
                'label' => '本科绩点/学积分（必填）',
            ),
            'attributes'=>array(
                'id'=>'grade_point',
                'placeholder' => '可精确到小数点后两位',
                'step' => '0.01',
                'min' => '0',
//                'data-toggle' => 'tooltip',
//                'title' => 'first tooltip',
            ),
        ));
        $this->add(array(//1.8 CET6英语六级成绩（选填）
            'name' => 'value_cet6',
            'type' => 'number',
            'options' => array(
                'label' => 'CET6英语六级成绩（选填）',
            ),
            'attributes'=>array(
                'id'=>'value_cet6',
            ),
        ));
        $this->add(array(//1.9 目标高校（必填）
            'name' => 'target_university',
            'type' => 'Text',
            'options' => array(
                'label' => '目标高校（必填）',
            ),
            'attributes'=>array(
                'id'=>'target_university',
                'readonly' =>'readonly',
                'value'=>'北京林业大学',
            ),
        ));

        $this->add(array(//1.10 目标院系(必填)
            'name' => 'target_college',
            'type' => 'Select',
            'options' => array(
                'label' => '目标院系(必填)',
                'empty_option' => '请选择',
                'value_options' => $target_collegeArr,
            ),
            'attributes'=>array(
                'id'=>'target_college',
            ),
        ));
        $this->add(array(//1.11 目标专业（必填）
            'name' => 'target_subject',
            'type' => 'Select',
            'options' => array(
                'label' => '目标专业（必填）',
                'empty_option' => '请选择',
                'value_options' => (!empty($newform['target_subject'])) ? $newform['target_subject']:null,
            ),
            'attributes'=>array(
                'id'=>'target_subject',
            ),
        ));
        $this->add(array(//1.12 目标方向（必填）
            'name' => 'target_profession',
            'type' => 'Select',
            'options' => array(
                'label' => '目标方向(必填)',
                'empty_option' => '请选择',
                'value_options' => (!empty($newform['target_profession'])) ? $newform['target_profession']:null,
            ),
            'attributes'=>array(
                'id'=>'target_profession',
            ),
        ));
        $this->add(array(//1.13.1 意向导师（选填）请选择一志愿
            'name' => 'target_professor',
            'type' => 'Select',
            'options' => array(
                'label' => '意向导师（一志愿必填）',
                'empty_option' => '- 请选择一志愿 -',
                'value_options' => (!empty($newform['target_professor'])) ? $newform['target_professor']:null,
            ),
            'attributes'=>array(
                'id'=>'target_professor',
                'class'=>'span3',
            ),
        ));
        $this->add(array(//1.13.2 意向导师（选填）请选择二志愿
            'name' => 'target_professor2',
            'type' => 'Select',
            'options' => array(
                'label' => '意向导师（选填）',
                'empty_option' => '- 请选择二志愿 -',
                'value_options' => (!empty($newform['target_professor'])) ? $newform['target_professor']:null,
            ),
            'attributes'=>array(
                'id'=>'target_professor2',
                'class'=>'span3',
            ),
        ));
        $this->add(array(//1.13.3 意向导师（选填）请选择三志愿
            'name' => 'target_professor3',
            'type' => 'Select',
            'options' => array(
                'label' => '意向导师（选填）',
                'empty_option' => '- 请选择三志愿 -',
                'value_options' => (!empty($newform['target_professor'])) ? $newform['target_professor']:null,
            ),
            'attributes'=>array(
                'id'=>'target_professor3',
                'class'=>'span3',
            ),
        ));
        $this->add(array(//1.14 外语语种（必填）
            'name'=>'foreign_language',
            'type'=>'Select',
            'options'=>array(
                'label'=>'外语语种（必填）',
                'empty_option' => '-请选择-',
                'value_options' => array(
                    '1' => '英语',
                    '2' => '日语',
                    '3' => '德语',
                    '4' => '法语',
                    '5' => '西班牙语',
                    '6' => '意大利语',
                    '7' => '韩语',
                    '8' => '俄语',
                    '9' => '其他',
                ),
            ),
            'attributes'=>array(
                'id'=>'foreign_language',
            ),
        ));
        $this->add(array(//1.15 GRE成绩（选填）
            'name' => 'gre_score',
            'type' => 'number',
            'options' => array(
                'label' => 'GRE成绩（选填）',
            ),
            'attributes'=>array(
                'id'=>'gre_score',
                'step' => '0.01',
                'min' => '0',
            ),
        ));
        $this->add(array(//1.16 TOEFL成绩（选填）
            'name' => 'toefl_score',
            'type' => 'number',
            'options' => array(
                'label' => 'TOEFL成绩（选填）',
            ),
            'attributes'=>array(
                'id'=>'toefl_score',
                'step' => '0.01',
                'min' => '0',
            ),
        ));

        $this->add(array(//submit
            'name'=>'addinfo_submit',
            'type'=>'button',
            'attributes'=>array(
                'value'=>'提交',
                'id'=>'addinfo_submit',
                'class'=>'btn btn-primary',
            ),
        ));
    }


    public function getInputFilter(){
        if(!$this->inputFilter){
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'		=> 'graduate_subject',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'graduate_college',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'graduate_professional_class',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'graduate_profession',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'pro_stu_num',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'ranking',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'grade_point',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'value_cet6',
                'required' =>false,
                'validators' => array(
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_university',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_college',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_subject',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_profession',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_professor',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_professor2',
                'required'	=> false,		            //必需的
                'validators' => array(
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'target_professor3',
                'required'	=> false,		            //必需的
                'validators' => array(
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'foreign_language',
                'required'	=> true,		            //必需的
                'validators' => array(
                    array('name' => 'NotEmpty'),		//不允许为空
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'gre_score',
                'required'	=> false,
                'validators' => array(
                ),
            ));
            $inputFilter->add(array(
                'name'		=> 'toefl_score',
                'required'	=> false,
                'validators' => array(
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function getInputFilterSpecification(){

    }
}