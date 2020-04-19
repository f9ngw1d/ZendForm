<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/25
 * Time: 23:04
 */

namespace Manage\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UsrRole implements InputFilterAwareInterface
{
    public $uid;
    public $rid;
    protected $inputFilter;

    //数组转换成对象里的属性，插入新用户的时候用
    public function exchangeArray($data)
    {
        $this->uid = (!empty($data['uid'])) ? $data['uid'] : null;
        $this->rid = (!empty($data['rid'])) ? $data['rid'] : null;

    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    //InputFilterAwareInterface的方法，不需要是实现，但是这里不需要，所以直接抛出异常
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    //返回一个过滤器
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            /*$inputFilter->add(array(
                'name' => 'id',
                'required' => true,    //必需的
                'filters' => array(
                    array('name' => 'Int'),    //只能是整数
                ),
            ));*/
            $inputFilter->add(array(
                'name' => 'rid',
                'required' => true,    //必需的
                'filters' => array(
                    array('name' => 'Int'),    //只能是整数
                ),
            ));
            $inputFilter->add(array(
                'name' => 'uid',
                'required' => true,    //必需的
                'filters' => array(
                    array('name' => 'Int'),    //只能是整数
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}