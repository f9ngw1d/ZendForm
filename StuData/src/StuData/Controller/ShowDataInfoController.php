<?php
/**
 * @author cry
 * @function 学生补充信息的相关处理
 */
namespace StuData\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
//use Stu\Model\Honour;
//use Stu\Model\Project;
//use Stu\Form\EinfoForm;
//use Stu\Form\AddInfoForm;
//use Info\Model\Mailqueue;

class ShowDataInfoController extends AbstractActionController
{

    public function __construct()
    {
//        $pc = new \Basicinfo\Model\PermissionControll();
//        $pc -> judgePermisson();
    }
    public  function showDataInfoAction(){

    }
    /**
     * @author cry
     * @function 删除对应uid的学生的全部信息，好让其重新注册！
     * @return bool
     */

}