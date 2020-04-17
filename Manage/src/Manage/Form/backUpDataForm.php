<?php

namespace Manage\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class backUpDataForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit1',
            'attributes' => array(
                'value' => '备份数据库数据',
                'class' => 'btn btn-primary',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit2',
            'attributes' => array(
                'value' => '备份用户上传资料',
                'class' => 'btn btn-primary',
            )
        ));
    }
}