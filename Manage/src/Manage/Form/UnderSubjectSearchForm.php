<?php
//  module/Students/src/Students/Form/MetaSearchForm.php:
namespace Manage\Form;

use Zend\Form\Form;
//构建表单对象
class UnderSubjectSearchForm extends Form
{
    public function __construct($name = null)
    {
        // 我们可以忽略名称传递
        parent::__construct('under_subject');
        $this->setAttribute('method', 'get');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
                'value' => '1',
            ),
        ));

        $this->add(array(
            'name'=>'column',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'column',
                'class' => 'form-control',
            ),
            'options'=>array(
                'label'=>'',
                'value_options'=>array(
                    '' => '请选择查询项',
                    'id'   => '专业编号',
                    'name' => '专业名称',
                )
            )
        ));

        $this->add(array(
            'name' => 'parame',
            'attributes' => array(
                'type'  => 'text',
                'id' 	=> 'parame',
                'class' => 'form-control',
                'placeholder' => '请输入检索关键字'
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => '检索',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }
}