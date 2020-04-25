<?php
//  module/Student/src/Student/Form/DBFSearchForm.php:
namespace Manage\Form;

use Zend\Form\Form;
//构建表单对象
class UserSearchForm extends Form
{
    public function __construct($name = null)
    {
        // 我们可以忽略名称传递
        parent::__construct('u_user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
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
                    'Uname' => '用户名',
                    'Realname' => '姓名',
                    'Mobile' => '移动电话',
                    'Email' => '电子邮箱',
                    'YXSM' => '学院编号',
                ),
            )
        ));

        $this->add(array(
            'name' => 'parame',
            'attributes' => array(
                'id' => 'parame',
                'type'  => 'text',
                'class' => 'form-control',
                'placeholder' => "请输入检索关键词",
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