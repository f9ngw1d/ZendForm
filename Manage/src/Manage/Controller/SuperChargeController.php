<?php


namespace Manage\Controller;

use Info\Model\Mailqueue;
use Stu\Form\ChangeProfessorForm;
use Stu\Form\ChangeVolunteerForm;
use Stu\Model\Check;
use Stu\Model\StuBase;
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
    protected $check_table;
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
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        if (is_null($uid)) {
            echo "<script> alert('非法访问！');window.location.href='/info';</script>";
        }
        $container_rid = new Container('rid');
        $rid_arr = $container_rid->item;
       // $stu_info = $this->
        $status = $this->getCheckTable()->getCheck($uid);

        if($status==false){
            echo "<script> alert('未查询到该生状态，非法访问！');window.location.href='/info';</script>";
        }
        $now_status=$status->status;

            $form = new ChangeStatusForm($target_college);

            $user = $this->getStuBaseTable()->getStu($uid);
            $prof_id_unique = $user->target_college . $user->target_subject . $user->target_profession . '1';
            $staff_info = $this->getProfessionStaffTable()->getStaffByPid($prof_id_unique);
            $staff_arr = array();
            foreach ($staff_info as $key => $row) {
                if (!empty($row)) {
                    $sid = $row->staff_id;//学院id
                    $staff_name = $this->getStaffTable()->getStaff($sid)->staff_name;
                    $staff_arr[$sid] = $staff_name;
                }
            }//转化
            //var_dump($staff_arr);
            //empty($this->getStaffTable()->getStaff($stu_info->target_professor)->staff_name) ? '无' : $this->getStaffTable()->getStaff($stu_info->target_professor)->staff_name,
            //echo "staff<br><br>";
            $change_tutor_form = new ChangeProfessorForm($staff_arr, $staff_arr, $staff_arr);
            if(!is_null($user->target_professor)||!is_null($user->target_professor2)||!is_null($user->target_professor3)){
                $value = array(
                    'target_professor' => is_null($user->target_professor)?NULL:$user->target_professor,//$this->getStaffTable()->getStaff($user->target_professor)->staff_name,
                    'target_professor2' => is_null($user->target_professor2)?null:$user->target_professor2,
                    'target_professor3' => is_null($user->target_professor3)?null:$user->target_professor3,
                );
                $change_tutor_form->setData($value);
            }
            //涉及表单提交
            $request = $this->getRequest();
            if ($request->isPost()) {
                $arrayData = $request->getPost()->toArray();
                if (isset($_POST['submit'])) {//strcmp($arrayData['submit'], '保存')
                    $postData = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray()
                    );
                    $form->setData($postData);
                    if ($form->isValid()) {
                        //var_dump($postData);
                        //可修改  ，将这些信息填入stu_base的相应地方，然后状态置为3
                        // if()//学院在进入学生进入复试审核时可改   录取审核  可改  都可改学生的志愿
                        // if (((in_array(9, $rid_arr) || in_array(11, $rid_arr)) && (($now_status == 4) || ($now_status == 8)))) {
                        //if (((in_array(9, $rid_arr) || in_array(11, $rid_arr)) && (($now_status >=3) && ($now_status <=5)))) {
                        if (((in_array(9, $rid_arr) || in_array(11, $rid_arr)) && (($now_status >=3) && ($now_status <10)&&$now_status!=6))) {
                            $data = array(
                                'uid' => $uid,
                                'target_college' => $postData['target_college'],
                                'target_subject' => $postData['target_subject'],
                                'target_profession' => $postData['target_profession'],
                            );
                            $stu_base_table = new StuBase();
                            $stu_base_table->exchangeArray($data);
                            $result = $this->getStuBaseTable()->updateStuVolunteer($stu_base_table);
                            $data_change = array(
                                'uid' => $uid,
                                'target_professor' => null,
                                'target_professor2' => null,
                                'target_professor3' => null,
                            );
                            $stu_base_table->exchangeArray($data_change);
                            $result3 = $this->getStuBaseTable()->updateTargetProfessor($stu_base_table);
                            if($now_status>=3 && $now_status<=5){
                                $status_data = array(
                                    'uid' => $uid,
                                    'status' => 3,
                                );
                                $check_table = new Check();
                                $check_table->exchangeArray($status_data);
                                $result2 = $this->getCheckTable()->updateStatus($check_table);
                            }
                            else{
                                $status_data = array(
                                    'uid' => $uid,
                                    'status' => 7,
                                );
                                $check_table = new Check();
                                $check_table->exchangeArray($status_data);
                                $result2 = $this->getCheckTable()->updateStatus($check_table);
                            }
                            /* $status_data = array(
                                 'uid' => $uid,
                                 'status' => 3,
                             );
                             $check_table = new Check();
                             $check_table->exchangeArray($status_data);
                             $result2 = $this->getCheckTable()->updateStatus($check_table);*/
                            echo "<script>alert('返回查看结果');window.location.href=''/stu/stu/changeVolunteer/uid/".$uid."';</script>";
                            //return $this->redirect()->toRoute("stu/default", array("Controller" => "Stu", "action" => "changeVolunteer"));
                        }//if  有权操作
                        else {
                            echo "<script>alert('无权操作');window.location.href='/info';</script>";
                        }
                    } else {
                        echo "<script>alert('the form is not valid！');window.location.href='/stu/stu/changeVolunteer/uid/".$uid."';</script>";
                    }
                }//if   点击的是submit，提交志愿
                else {
                    //学科在录取之前都能改学生的导师
                    //修改导师，在4以前都可以
                    $postData = $this->getRequest()->getPost()->toArray();
                    $change_tutor_form->setData($postData);
                    if ($change_tutor_form->isValid()) {
                        //if (((in_array(8, $rid_arr) || in_array(12, $rid_arr) || in_array(9,$rid_arr) || in_array(11,$rid_arr)) && ($now_status < 10) && ($now_status > 0))) {//可改导师
                        if (((in_array(9,$rid_arr) || in_array(11,$rid_arr)) && ($now_status < 10) && ($now_status > 0))) {//可改导师
                            $data2 = array(
                                'uid' => $uid,
                                'target_professor' => $postData['target_professor'],
                                'target_professor2' => $postData['target_professor2'],
                                'target_professor3' => $postData['target_professor3'],
                            );
                            $stu_base_table = new StuBase();
                            $stu_base_table->exchangeArray($data2);
                            $result3 = $this->getStuBaseTable()->updateTargetProfessor($stu_base_table);
                            //var_dump($result3);
                            echo "<script>alert('已更改，返回查看');window.location.href='/stu/stu/changeVolunteer/uid/".$uid."';</script>";
                        } else {
                            echo "<script>alert('该生已不可更改导师！');window.location.href='/check/check/stulist/rid/".$rid."/now_stage/".$now_stage."';</script>";
                        }
                        //return $this->redirect()->toRoute("/stu/stu/changeVolunteer");//"stu/default", array("Controller" => "Stu", "action" => "changeVolunteer"));

                    }//valid
                    else {
                        echo "<script>alert('the form is not valid！');window.location.href='/stu/stu/changeVolunteer/uid/".$uid."';</script>";
                    }//not valid
                    //return $this->redirect()->toRoute("stu/default", array("Controller" => "Stu", "action" => "changeVolunteer"));
                }//else  点击的是submit2,修改导师
            }//if    ispost
            return array(
                'form' => $form,
                'uid' => $uid,
                'rid' => $rid,
                'now_stage' => $now_stage,
                'change_tutor_form' => $change_tutor_form,
                'username' => $user->user_name,
                'college' => empty($this->getCollegeTable()->getCollege($user->target_college)->college_name)?'无':$this->getCollegeTable()->getCollege($user->target_college)->college_name,
                'profession' => empty($this->getProfessionTable()->getProfessionByPidSidCid($user->target_profession, $user->target_subject, $user->target_college)->profession_name)?'无':$this->getProfessionTable()->getProfessionByPidSidCid($user->target_profession, $user->target_subject, $user->target_college)->profession_name,
                'subject' => empty($this->getSubjectTable()->getSubjectsByCidSidFull($user->target_college,$user->target_subject,1)->subject_name)?'无':$this->getSubjectTable()->getSubjectsByCidSidFull($user->target_college,$user->target_subject,1)->subject_name,
                'professor1' => empty($this->getStaffTable()->getStaff($user->target_professor)->staff_name) ? '无' : $this->getStaffTable()->getStaff($user->target_professor)->staff_name,
                'professor2' => empty($this->getStaffTable()->getStaff($user->target_professor2)->staff_name) ? '无' : $this->getStaffTable()->getStaff($user->target_professor2)->staff_name,//$user->target_professor2,
                'professor3' => empty($this->getStaffTable()->getStaff($user->target_professor3)->staff_name) ? '无' : $this->getStaffTable()->getStaff($user->target_professor3)->staff_name,//$user->target_professor3,
                'status' => $now_status,
            );
            /*return array(
                'form' => $form,
                'change_tutor_form'=>$change_tutor_form,
                'username' => $username,
                'college' => $user->target_college,
                'profession' => $user->target_profession,
                'subject' => $user->target_subject,
                'professor1' => $user->target_professor,
                'professor2' => $user->target_professor2,
                'professor3' => $user->target_professor3,
                'status' => $now_status,
                'value'=>$value,
            );*/

    }
    public function exportDbfAction(){

    }


    //lrn
    public function getCheckTable()
    {
        if (!$this->check_table) {
            $sm = $this->getServiceLocator();
            $this->check_table = $sm->get('College\Model\StuStatusTable');
        }
        return $this->check_table;
    }
    //lrn
    protected function getStuBaseTable()
    {
        if (!$this->stubaseTable) {
            $sm = $this->getServiceLocator();
            $this->stubaseTable = $sm->get('StuData\Model\TStuBaseTable');
        }
        return $this->stubaseTable;
    }
}