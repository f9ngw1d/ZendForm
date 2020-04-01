<?php
namespace  Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TBaseCollege implements InputFilterAwareInterface
{
    public $college_id;
    public $college_name;
    public $dean_id;
    public $total_stu;
    public $free_stu;
    public $phone;
    public $manager_id;

    //简单地将 data 数组中的数据拷贝到College实体属性。ORM映射
    public function exchangeArray($data){
        $this->college_id = (!empty($data['college_id'])) ? $data['college_id'] : null;
        $this->college_name = (!empty($data['college_name']))? $data['college_name'] : null;
        $this->dean_id = (!empty($data['dean_id']))?$data['dean_id']:null;
        $this->total_stu = (!empty($data['total_stu']))?$data['total_stu']:null;
        $this->free_stu = (!empty($data['free_stu']))?$data['free_stu']:null;
        $this->phone = (!empty($data['phone']))?$data['phone']:null;
        $this->manager_id = (!empty($data['manager_id']))?$data['manager_id']:null;
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'college_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

        }
    }
}