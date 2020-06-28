<?php


namespace Manage\Controller;

use Info\Model\Mailqueue;
use StuData\Model\TStuBase;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Manage\Form\ChangeStatusForm;
use College\Model\StuStatus;
use Manage\Form\ChangeVolunteerForm;

class SuperChargeController extends AbstractActionController
{
    protected $check_table;
    protected $config_table;
    protected $stubase_table;
    protected $college_table;
    protected $team_table;
    protected $stu_project_table;
    protected $stu_honor_table;
    protected $einfo_table;
    protected $validatemailTable;
    protected $UsrStuTable;
    protected $mailqueue;
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

        if(!in_array(9, $rid_arr) && (!in_array(11, $rid_arr)) && (!in_array(10, $rid_arr))&& (!in_array(99, $rid_arr))){
            //不是（10研究生院、院长、院秘书）
            echo "<script> alert('您尚无权删除该学生！');window.location.href='/info';</script>";
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

            //3-stu_project
            $isexist_project = $this->getStuProjectTable()->getProjectBystuid($delstuid);
            if($isexist_project){
                $delprojectnum = $this->getStuProjectTable()->deleteProjectByUid($delstuid);
                // $delprojectnum = $this->getProjectTable()->deleteProject($delstuid);
//                echo "<script> alert('delproject:".$delprojectnum."') </script>";
            }


            //4-stu_honour
            $isexist_honour = $this->getStuHonorTable()->getHonourBystuid($delstuid);
            if($isexist_honour){
                $delhonournum = $this->getStuHonorTable()->deleteStuAllHonour($delstuid);
//                echo "<script> alert('delhonour:".$delhonournum."') </script>";
            }


            //5-stu_einfo_map
            $deleinfonum = $this ->getEinfoTable()->deleteStuAllEinfo($delstuid);
//            echo "<script> alert('deleinfonum:".$deleinfonum."') </script>"; //9

            //6-info_validatemail  删除激活信息
            $this->getvalidatemailTable()->deleteStuAllEinfo($delstu_email);
//            echo "<script> alert('delmailactive:".$delstu_email."') </script>"; //9
            //2-stu_check
//            $this->getCheckTable()->deleteStuStatus($delstuid);
            $this->getStuBaseTable()->deleteStu($delstuid);
            //7-usr_stu
            $this->getUsrStuTable()->deleteUserStu($delstuid);

            /*  ******************** 删除学生上传的资料 ********************  */
//            $delstuid_files = "public/img/stu/".(int)$delstuid;
            $delstuid_files = "public/img/student/".$delstuid;

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
            $school_name=$this->getConfigTable()->getConfigKey('school_name');
            $sys_name=$this->getConfigTable()->getConfigKey('system_name');
            $recv = array(  //邮箱信息
                'receiver' => $delstu_email,
                'status' => '-1',
                'title' => $school_name.$sys_name.'【重新注册提示】 ',
                'content' => $delstu_name.'同学你好，已经根据你的邮件，将注册信息从系统删除，以便于你重新注册时修改错误！',
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
    //lrn 超级管理员和研究生院均可随意修改学生状态
    public function changeStuStatusAction(){
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        if (is_null($uid)) {
            echo "<script> alert('非法访问！');window.location.href='/info';</script>";
        }
        $container_rid = new Container('rid');
        $rid_arr = $container_rid->item;
        if(is_null($rid_arr)){
            $rid_arr=array('9','99');
        }
        $status = $this->getCheckTable()->getCheck($uid);
        if($status==false){
            echo "<script> alert('未查询到该生状态，非法访问！');window.location.href='/info/Article/ArticleList';</script>";
        }
        $now_status=$status->status;
        $stu_status = $this->getConfigTable()->getConfigValueByKey("stu_status",$now_status);
        $all_status_arr = $this->getConfigTable()->getConfigValueByKey('stu_status',array(),false,true);
        $user = $this->getStuBaseTable()->getStu($uid);
        $form = new ChangeStatusForm($all_status_arr,$user->user_name,$stu_status[$now_status]['value_cn']);

            //涉及表单提交
            $request = $this->getRequest();
            if ($request->isPost()) {
                $arrayData = $request->getPost()->toArray();
                if (isset($_POST['submit'])) {//strcmp($arrayData['submit'], '保存')
                    $postData = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray()
                    );
                    $form->setData($postData);
//                    var_dump($postData);
                    if ($form->isValid()){
                        if(in_array(99,$rid_arr)||in_array(10,$rid_arr)){
                            $status_data = array(
                                    'uid' => $uid,
                                    'status' => $postData['target_status'],
                                );
                                $check_table = new StuStatus();
                                $check_table->exchangeArray($status_data);
                                $result2 = $this->getCheckTable()->updateStatus($check_table);
                                if($result2){
                                    echo "<script>alert('修改成功，返回查看结果');window.location.href='/manage/SuperCharge/changeStuStatus/uid/'.$uid';</script>";
                                }else{
                                    echo "<script>alert('修改失败，返回重试');window.location.href='/manage/SuperCharge/changeStuStatus/uid/'.$uid';</script>";
                                }
                            }
                    }
                    else{
                        echo "<script> alert('表单数据有误！');window.location.href='/manage/SuperCharge/changeStuStatus/uid/'.$uid';</script>";
                    }
                }
            }
        $college_info = $this->getCollegeTable()->getCollege($user->target_college);
        $team_info = $this->getTeamTable()->getTeam($user->target_team);
            return array(
                'form' => $form,
                'uid' => $uid,
                'username' => $user->user_name,
                'status' => $stu_status[$now_status]['value_cn'],
                'target_college' => isset($college_info->college_name)?$college_info->college_name:"未填写",
                'target_team' => isset($team_info->team_name)?$team_info->team_name:"未填写",
            );

    }
    /*
       * author:lrn
       * function:改志愿   改导师
       * attention:
       */
    public function changeVolunteerAction()
    {
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        if (is_null($uid)) {
            echo "<script> alert('非法访问！');window.location.href='/info';</script>";
        }
        $container_rid = new Container('rid');
        $rid_arr = $container_rid->item;
        $container_username = new Container('username');
        $username = $container_username->item;
        $status = $this->getCheckTable()->getCheck($uid);
        if($status==false){
            echo "<script> alert('未查询到该生状态，非法访问！');window.location.href='/info';</script>";
        }
        $now_status=$status->status;
            $all_college = $this->getCollegeTable()->fetchAll();//获取所有学院
            $target_college = array();
            foreach ($all_college as $key => $row) {
                if (!empty($row)) {
                    $cid = $row->college_id;//学院id
                    $cname = $row->college_name;//学院名
                    $target_college[$cid] = $cname;
                }
            }
            $form = new ChangeVolunteerForm($target_college);
            $user = $this->getStuBaseTable()->getStu($uid);
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
                        if (in_array(10, $rid_arr) || in_array(99, $rid_arr) ) {
                            $data = array(
                                'uid' => $uid,
                                'target_college' => $postData['target_college'],
                                'target_team' => $postData['target_team'],
                            );
                            $stu_base_table = new TStuBase();
                            $stu_base_table->exchangeArray($data);
                            $result = $this->getStuBaseTable()->updateStuVolunteer($stu_base_table);
//                           if($now_status>=3 && $now_status<=5){
//                                $status_data = array(
//                                    'uid' => $uid,
//                                    'status' => 3,
//                                );
//                                $check_table = new StuStatus();
//                                $check_table->exchangeArray($status_data);
//                                $result2 = $this->getCheckTable()->updateStatus($check_table);
//                            }
//                            else{
//                                $status_data = array(
//                                    'uid' => $uid,
//                                    'status' => 7,
//                                );
//                                $check_table = new StuStatus();
//                                $check_table->exchangeArray($status_data);
//                                $result2 = $this->getCheckTable()->updateStatus($check_table);
//                            }
                            if($result){
                                echo "<script>alert('修改成功，返回查看结果');window.location.href=''/manage/SuperCharge/changeVolunteer/uid/".$uid."';</script>";
                            }else{
                                echo "<script>alert('修改失败，返回重试');window.location.href=''/manage/SuperCharge/changeVolunteer/uid/".$uid."';</script>";
                            }
                            }//if  有权操作
                        else {
                            echo "<script>alert('无权操作');window.location.href='/info';</script>";
                        }
                    } else {
                        echo "<script>alert('the form is not valid！');window.location.href='/manage/stu/SuperCharge/uid/".$uid."';</script>";
                    }
                }//if   点击的是submit，提交志愿
            }//if    ispost
            $status_arr= $this->getConfigTable()->getConfigValueByKey("stu_status",array(),false,true);
            return array(
                'form' => $form,
                'uid' => $uid,
                'username' => $user->user_name,
                'status' => $status_arr[$now_status],//this->getConfigTable()->getConfigValueByKey('stu_status',$now_status),
                'college' => empty($this->getCollegeTable()->getCollege($user->target_college)->college_name)?'无':$this->getCollegeTable()->getCollege($user->target_college)->college_name,
                'team' => empty($this->getTeamTable()->getTeam($user->target_team)->team_name)?'无':$this->getTeamTable()->getTeam($user->target_team)->team_name,
            );
    }
    public function exportDbfAction(){
    }

    /*
           * author:lrn
           * function:根绝college选学科
           * attention:
           */
    public function selectSubByCidAction()
    {
        $cid = $this->params()->fromRoute('param3', 0);
        $teams = $this->getTeamTable()->getTeamIDByCollegeID($cid);
        $target_team = array();
        foreach ($teams as $key => $row) {
            if (!empty($row)) {
                $sid = $row->team_id;//专业id
                $sname = $row->team_name;//专业名
                $target_team[$sid] = $sname;
            }
        }
        return array('target_team' => $target_team, 'cid' => $cid);
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
        if (!$this->stubase_table) {
            $sm = $this->getServiceLocator();
            $this->stubase_table = $sm->get('StuData\Model\TStuBaseTable');
        }
        return $this->stubase_table;
    }
    //lrn
    public function getConfigTable() {
        if(!$this->config_table){
            $sm = $this->getServiceLocator();
            $this->config_table = $sm->get('Manage\Model\ConfigKeyTable');
        }
        return $this->config_table;
    }
    //lrn
    protected function getCollegeTable()
    {
        if (!$this->college_table) {
            $sm = $this->getServiceLocator();
            $this->college_table = $sm->get('Manage\Model\TBaseCollegeTable');
        }
        return $this->college_table;
    }
    //lrn
    protected function  getTeamTable()
    {
        if(!$this->team_table){
            $sm = $this->getServiceLocator();
            $this->team_table = $sm->get('Leader\Model\TBaseTeamTable');
        }
        return $this->team_table;
    }
    /*
    * author:lrn
    * function:
    * attention:
    */
    public function getStuProjectTable()
    {
        if (!$this->stu_project_table) {
            $sm = $this->getServiceLocator();
            $this->stu_project_table = $sm->get('Manage\Model\ProjectTable');
        }
        return $this->stu_project_table;
    }
    //lrn
    public function getStuHonorTable(){
        if (!$this->stu_honor_table) {
            $sm = $this->getServiceLocator();
            $this->stu_honor_table = $sm->get('Manage\Model\HonourTable');
        }
        return $this->stu_honor_table;
    }
    //lrn
    public function getEinfoTable()
    {
        if (!$this->einfo_table) {
            $sm = $this->getServiceLocator();
            $this->einfo_table = $sm->get('Manage\Model\EinfoTable');
        }
        return $this->einfo_table;
    }
    //lrn
    public function getvalidatemailTable()//InfovalidatemailTable
    {
        if (!$this->validatemailTable) {
            $sm = $this->getServiceLocator();
            $this->validatemailTable = $sm->get('Manage\Model\InfovalidatemailTable');
        }
        return $this->validatemailTable;
    }
    //lrn
    public function getUsrStuTable()
    {
        if (!$this->UsrStuTable) {
            $sm = $this->getServiceLocator();
            $this->UsrStuTable = $sm->get('Manage\Model\UsrStuTable');
        }
        return $this->UsrStuTable;
    }
    //lrn
    public function getMailqueue()
    {//获取发送邮件类
        if (!$this->mailqueue) {
            $um = $this->getServiceLocator();
            $this->mailqueue = $um->get('Info\Model\MailqueueTable');
        }
        return $this->mailqueue;
    }
}