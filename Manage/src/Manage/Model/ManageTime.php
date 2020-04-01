<?php
namespace  Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ManageTime implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $start_time;
    public $end_time;
    public $description;
    public $status;
    protected $inputFilter;

    public function exchangeArray($data){
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->start_time = (!empty($data['start_time'])) ? $data['start_time'] : null;
        $this->end_time = (!empty($data['end_time'])) ? $data['end_time'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
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

    }
}