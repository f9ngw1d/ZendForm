<?php


namespace Manage\Controller;

use Info\Model\Mailqueue;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;


class SuperChargeController extends AbstractActionController
{
    public function __construct()
    {
    }
    //lrn delete stu
    public function delStuAction(){
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        if (is_null($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (is_null($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
        }
        //从session中获取教师所在学院
        $containerCol = new Container('college_id');
        $teach_college_id = $containerCol->item;

        //从url  uid中获取要删除的学生id，及其目标学院
        $delstuid = $this->params()->fromRoute('uid');
        $stu_college_id = $this->getStuBaseTable()->getStu($delstuid)->target_college;//DB获取！！！！

        if(!in_array(9, $rid_arr) && (!in_array(11, $rid_arr)) && (!in_array(10, $rid_arr))){
            //不是（10研究生院、院长、院秘书）
            echo "<script> alert('您无该项角色权限，无权访问！您尚无权删除该学生！');window.location.href='/info';</script>";
            return false;
        }
        else if( ( in_array(9, $rid_arr) || (in_array(11, $rid_arr)) ) && ($teach_college_id != $stu_college_id)){
            //（院长、院秘书）
            echo "<script> alert('您尚无权删除该学生！');window.location.href='/info';</script>";
            return false;
        }


        $url_last = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''; //获取上一个url，方便跳转回去

        try {
            /*  ******************** 事务开始 ********************   */
            $adapter =  $this->getServiceLocator()->get ( 'Zend\Db\Adapter\Adapter' );
            $adapter->getDriver()->getConnection()->beginTransaction();

            /*  删除该学生相关数据库 */
            //1-stu_base
            $delstuid_base = $this->getStuBaseTable()->getStu($delstuid); //从stu_base获取该学生全部信息
//            var_dump($delstuid_base) ;
            $delstu_name = $delstuid_base->user_name;
            $delstu_email = $delstuid_base->email;
            $delstu_phone = $delstuid_base->phone;
            $this->getStuBaseTable()->deleteStu($delstuid);

            //2-stu_check
            $this->getCheckTable()->deleteCheck($delstuid);

            //3-stu_project
            $isexist_project = $this->getProjectTable()->getProjectBystuid($delstuid);
            if($isexist_project){
                $delprojectnum = $this->getProjectTable()->deleteProjectByUid($delstuid);
                // $delprojectnum = $this->getProjectTable()->deleteProject($delstuid);
//                echo "<script> alert('delproject:".$delprojectnum."') </script>";
            }


            //4-stu_honour
            $isexist_honour = $this->getHonourTable()->getHonourBystuid($delstuid);
            if($isexist_honour){
                $delhonournum = $this->getHonourTable()->deleteStuAllHonour($delstuid);
//                echo "<script> alert('delhonour:".$delhonournum."') </script>";
            }


            //5-stu_einfo_map
            $deleinfonum = $this ->getEinfoTable()->deleteStuAllEinfo($delstuid);
//            echo "<script> alert('deleinfonum:".$deleinfonum."') </script>"; //9

            //6-info_validatemail  删除激活信息
            $this->getvalidatemailTable()->deleteStuAllEinfo($delstu_email);
//            echo "<script> alert('delmailactive:".$delstu_email."') </script>"; //9

            //7-usr_stu
            $this->getUsrStuTable()->deleteUserStu($delstuid);

            /*  ******************** 删除学生上传的资料 ********************  */
//            $delstuid_files = "public/img/stu/".(int)$delstuid;
            $delstuid_files = "public/img/stu/".$delstuid;

//            if(is_dir($delstuid_files."/") && !($this->delDirAndFiles($delstuid_files))){
//                echo "!!!!!!!!!!";//http://yzb.bjfu.edu.cn:8083/stu/addinfo/delStu/uid/239
////                throw new \Exception("del stu files fail");
//            }else{
//                echo "ok";
//            }
            if(is_dir($delstuid_files."/") ){
                $rmfiles = exec("rm -rf ".$delstuid_files,$output, $return_var);
            }
//
            /*  ******************** 给学生发邮件/发短信 ********************  */ //$delstu_phone   $delstu_email
//            $delstu_email = "ruiyangchen@qq.com";//测试邮箱
            $recv = array(  //邮箱信息
                'receiver' => $delstu_email,
                'status' => '-1',
                'title' => 'BFU test ',
                'content' => $delstu_name.'同学你好，已经根据你的邮件，将注册信息删除，以便于你重新注册时修改错误！',
            );
            $mailtable = new Mailqueue();
            $mailtable->exchangeArray($recv);
            $this->getMailqueue()->saveMailqueue($mailtable); //插入到发邮件的数据表当中

            $adapter->getDriver()->getConnection()->commit();  //提交

            /*  ******************** 提交以后，像邮箱队列发信号 ********************  */
            $MSGKEY = 123321;   //发信号
            $msg_id = msg_get_queue ($MSGKEY, 0600);
            /* Send message to C program */
            if(!msg_send($msg_id, 12, "start\0",false)){
                throw new \Exception("邮件发送失败 请联系管理员");
            }
            echo "<script type='text/javascript'> alert('成功删除“".$delstu_name."”该学生全部信息，并且已发消息通知他/她！'); </script>";
            echo "<script> window.location.replace('".$url_last."');</script>";
            return true;
        }catch(\Exception $e){
            $adapter->getDriver()->getConnection()->rollback(); //出现异常，回滚
            $message = $e->getMessage();
            $code = $e->getCode();
            echo "<script> alert('删除“".$delstu_name."”该学生全部信息失败！$message && $code');</script>";
            echo "<script> window.location.replace('".$url_last."');</script>";
            return false;
        }
    }
    public function changeStuStatusAction(){

    }
    public function exportDbfAction(){

    }
}