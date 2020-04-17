<?php

namespace Manage\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class DeleteAllDataForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => '清空数据库',
                'class' => 'btn btn-primary',
            )
        ));
    }
}