<?php
/**
 * @author cry
 * @function 学生补充信息的相关处理
 */
namespace Stu\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Stu\Model\Honour;
use Stu\Model\Project;
use Stu\Form\EinfoForm;
use Stu\Form\AddInfoForm;
use Info\Model\Mail;

class AddInfoController extends AbstractActionController
{
    protected $undersubjectTable;
    protected $collegeTable;
    protected $subjectTable;
    protected $professionTable;
    protected $professionstaffTable;
    protected $stubaseTable;
    protected $staffTable;
    protected $projectTable;
    protected $honourTable;
    protected $usrstuTable;
    protected $electronicinfoTable;
    protected $einfoTable;
    protected $check_table;
    protected $universityfreeTable;
    protected $nationalityTable;
    protected $politicalstatusTable;
    protected $registerfilterTable;
    protected $suitabilityTable;
    protected $validatemailTable;

    public function __construct()
    {
//        $pc = new \Basicinfo\Model\PermissionControll();
//        $pc -> judgePermisson();
    }

    /**
     * @author cry
     * @function 删除对应uid的学生的全部信息，好让其重新注册！
     * @return bool
     */
    public function delStuAction()
    {
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

    /**
     * @author cry
     * @function: 学生补充信息  1-基本信息
     */
    public function addInfoAction()
    {
        /* 权限判断 */
        $rid_container =  new Container('rid');
        $ridArr = $rid_container->item;//login 用户的权限rid

        $login_Container = new Container('uid');
        $login_id = $login_Container->item;

        $uid = $this->params()->fromRoute('uid'); //从路由尝试取uid

        if (!in_array(1, $ridArr) && (!in_array(8, $ridArr)) && (!in_array(9, $ridArr)) && !in_array(10, $ridArr) && !in_array(11, $ridArr) && !in_array(12, $ridArr)) {
            //1学生 8/12 学科方向 9/11 院长院秘书 10 研究生院
            echo "<script>alert('您不具有访问权限！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/info');</script>";
            return false;
        }else if (in_array(1,$ridArr) && ($this->getCheckTable()->getCheck($login_id)->status != 2)) {
            //学生状态控制 3(提交审核)->跳转
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        } //权限判断

        if (is_null($uid)){ //学生自己登陆的话，url上面是空的
            $uid = $login_id;
        }

        /* 初始form 以及post 处理 */
        $graduate_subjectArr  = $this->getUnderSubjectTable()->getAllSubjectArr();//获取本科学科门类数组
        $target_collegeArr = $this->getCollegeTable()->getCollegesIDNameArr();//获取目标院系数组
        $form = new AddInfoForm($graduate_subjectArr,$target_collegeArr);//生成学生补充信息 添加基础信息的表单

        $request = $this->getRequest();
        if($request->isPost()){//验证是否是有post方式传入的表单
            $form->setData($request->getPost());//数据从request post里面取，并用表单的setData()返回在表单中

            if ($form->isValid()){ //验证表单数据是否有效 会自动走表单的验证函数
                $filterdValues = $form->getData(); //获取数据，再进行判断
//                $stu_addinfo = array(
//
//                );
                if($this->getStuBaseTable()->saveStuAddinfo($uid,$filterdValues)){ //保存学生数据
                    echo "<script> alert('保存成功！') </script>";
                }else{
                    echo "<script> alert('提交失败！') </script>";
                }

            }else{
                echo "<script> alert('表单填写出错！请仔细检查！') </script>";
            }//is valid
        }//ispost

        /* 回写表单 */
        //填写数据的回写 为了联动下拉框
        $stuinfo = $this->getStuBaseTable()->getStu($uid);//根据uid获取学生信息
        $stubase = array();
        if($stuinfo->target_university){//用target_university字段判断学生是否已经填写了添加信息表单，如果填写了进行回写
            $stubase = array( //生成可视化数组，传入表单再用setData回写
                'graduate_college' => $stuinfo->graduate_college,
                'graduate_subject' => $stuinfo->graduate_subject,
                'graduate_professional_class' =>$stuinfo->graduate_professional_class,
                'graduate_profession' => $stuinfo->graduate_profession,
                'pro_stu_num' => $stuinfo->pro_stu_num,
                'ranking' => $stuinfo->ranking,
                'grade_point' => $stuinfo->grade_point,
                'value_cet6' => $stuinfo->value_cet6,
                'target_university' => $stuinfo->target_university,
                'target_college' => $stuinfo->target_college,
                'target_subject' => $stuinfo->target_subject,
                'target_profession' => $stuinfo->target_profession,
                'target_professor' => $stuinfo->target_professor,
                'target_professor2'=> $stuinfo->target_professor2 ,
                'target_professor3'=> $stuinfo->target_professor3 ,
                'foreign_language' => $stuinfo->foreign_language,
                'gre_score' => $stuinfo->gre_score,
                'toefl_score' => $stuinfo->toefl_score,
            );

            $graduate_professional_classarr = $this->getUnderSubjectTable()->getProclassByCid($stuinfo->graduate_subject);//获取本科专业类
            $graduate_professionalarr = $this->getUnderSubjectTable()->getProByPcid($stuinfo->graduate_professional_class);//本科专业
            $target_subjectarr = $this->getSubjectTable()->getSubjectsIDNameArr($stuinfo->target_college);//目标专业
            $target_professionarr = $this->getProfessionTable()->getFulltimeDirectionsIDNameArr($stuinfo->target_college,$stuinfo->target_subject);//目标方向
            $target_professorarr = $this->getProfessionTable()->getProfessionFullTeachByPidSidCid($stuinfo->target_profession,$stuinfo->target_subject,$stuinfo->target_college);//意向导师

            $newform = array( //将获取的数组信息形成大数组，方便回传给表单
                'graduate_professional_class' => (!empty($graduate_professional_classarr) ? $graduate_professional_classarr :null),
                'graduate_profession' => (!empty($graduate_professionalarr) ? $graduate_professionalarr :null),
                'target_subject' => (!empty($target_subjectarr) ? $target_subjectarr :null),
                'target_profession' =>(!empty($target_professionarr) ? $target_professionarr :null),
                'target_professor' => (!empty($target_professorarr) ? $target_professorarr :null),
            );


            $form = new AddInfoForm($graduate_subjectArr,$target_collegeArr,$newform); //绘制新表单
            $form->setData($stubase); //表单回写
        }

        /*注册信息显示*/
        //学生注册时信息相关数组
        $apply_type = array(
            'putong' => '普通推免',
            'zhibo' => '直博生',
            'zhijiaotuan' => '支教团',
            'techangsheng' => '特长生',
            '2jia' => '2+2/3',
            'gugan' => '少数民族骨干计划',
            'shibing' => '退役大学生士兵计划',
        );
        $gender = array(
            '1' => '女',
            '2' => '男',
        );
//        $political_status = array(
//            'masses' => '群众',
//            'cyl' => '共青团员',
//            'cpc_candidate' => '预备党员',
//            'cpc' => '中共党员',
//        );
        $nationality = $this->getNationalityTable()->getNationality($stuinfo->nationality)->nationality_name;
        $political_status = $this->getPoliticalStatusTable()->getPoliticalStatus($stuinfo->political_status)->political_status_name;
        $graduate_university = $this->getUniversityFreeTable()->getUniversityByUid($stuinfo->graduate_university)->university_name;//本科毕业大学
        $stu_register_info = array(
            'user_name' => $stuinfo->user_name,
            'apply_type'    =>  $apply_type[$stuinfo->apply_type],
            'gender'    =>  $gender[$stuinfo->gender],
            'idcard'     =>  $stuinfo->idcard,
            'nationality'   =>  (!empty($nationality) ? $nationality  : null ),
            'political_status'  => (!empty($political_status) ? $political_status  : null ),
            'phone' =>  $stuinfo->phone,
            'email' =>  $stuinfo->email,
            'value_cet4'    =>  $stuinfo->value_cet4,
            'graduate_university'   =>  $graduate_university,

        );

        $view =new ViewModel(array(
            'form' => $form,
            'stubase'=>$stubase,                        //用于显示学生基础信息信息
            'stu_register_info' => $stu_register_info, //用于显示学生注册信息
            'uid' => $uid,
            'rid'=> $ridArr,
        ));
        return $view;
    }
    /**
     * @author cry
     * @function: 学生补充信息  科研经历
     */
    public function addInfoProjectAction()//科研经历
    {
        /* 权限判断 */
        $rid_container =  new Container('rid');
        $ridArr = $rid_container->item;//login 用户的权限rid

        $login_Container = new Container('uid');
        $login_id = $login_Container->item;

        $uid = $this->params()->fromRoute('uid'); //从路由尝试取uid

        if (!in_array(1, $ridArr) && (!in_array(8, $ridArr)) && (!in_array(9, $ridArr)) && !in_array(10, $ridArr) && !in_array(11, $ridArr) && !in_array(12, $ridArr)) {
            //1学生 8/12 学科方向 9/11 院长院秘书 10 研究生院
            echo "<script>alert('您不具有访问权限！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/info');</script>";
            return false;
        }else if (in_array(1,$ridArr) && ($this->getCheckTable()->getCheck($login_id)->status != 2)) {
            //学生状态控制 3(提交审核)->跳转
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        } //权限判断
        //var_dump($uid);
        //echo "<br>uid<br>";
        //var_dump($login_id);
        //echo "<br>loginid<br>";
        if (is_null($uid)){ //学生自己登陆的话，url上面是空的
            $uid = $login_id;
        }


        $request = $this->getRequest();
        if ($request->isPost()) {
            if($_FILES['certificate']['size']){

                $image_format = explode("/",$_FILES['certificate']['type']);
                $time = substr(md5(time()), 0, 8);//当前时间加密，以便上传图片相同命名也是唯一的
                $image_name = $uid."_".$time.".".$image_format[1];

                $certificate__abs ="public/img/stu/".$uid."/project";

                if (!is_dir($certificate__abs)) { //创建存储的目录
                    $res=mkdir($certificate__abs,0777,true);
                    if(!$res){
                        echo "目录 $certificate__abs 创建失败";
                    }
                }

            }
            //处理post的数据
            $certificate_level = array(
                '1' => '国家级',
                '2' => '省市级',
                '3' => '其他'
            );
            $projectarr = array(
                'uid' => $uid,   //从session取
                'project_name' => strip_tags($_POST['project_name']),
                'abstract' => strip_tags($_POST['abstract']),
                'conclusion' => strip_tags($_POST['conclusion']),
                'achievement' => strip_tags($_POST['achievement']),
                'certificate' => (!empty($_FILES['certificate']['name'])) ? $certificate__abs."/".$image_name : null,
                'certificate_level' => $certificate_level[$_POST['certificate_level']],
                'create_at' => date('y-m-d h:i:s'),
            );
            if($this->getProjectTable()->getProjectByUidPname($uid,$_POST['project_name'])){//是否有相同的项目名字
                $project = new Project();
                $project->exchangeArray($projectarr);
                $project_flag = $this->getProjectTable()->saveProject($project);
                if($_FILES['certificate']['size']) {
                    $projectfile_flag = move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate__abs . "/" . $image_name);
                }else{
                    $projectfile_flag = 1;
                }
                if($project_flag && $projectfile_flag){
                    echo "<script>alert('项目添加成功！请在网页下方查看！');</script>";
                }else{
                    echo "<script>alert('项目添加失败！请稍后重新上传!');</script>";
                }
            }else{
                echo "<script>alert('您的列表存在相同名字的项目！项目添加失败！');</script>";
            }
        }
        //var_dump($uid);
        $project_table =  $this->getProjectTable()->getProjectByUid($uid);
        //var_dump($project);
        $view = new ViewModel(array(
            'project_table' => $project_table,
            'uid' => $uid,
            'rid'=> $ridArr,
        ));
        return $view;

//        return $this->redirect()->toRoute('stu/default',array('controller'=>'addinfo','action'=>'addInfo'));

    }
    public function deleteProjectAction()
    {
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        /* 状态控制 */
        //rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }
        //学生状态控制 3(提交审核)->跳转
        if($this->getCheckTable()->getCheck($uid)->status != 2){
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        }

        $projid = $this->params()->fromRoute('id');

        if($this->getProjectTable()->getProject($projid)->certificate){//存在图片要先删除图片
            $certificate_addr = $this->getProjectTable()->getProject($projid)->certificate;

            if (is_file($certificate_addr)) {
                if (!unlink($certificate_addr)){//删除文件
                    echo ("Error deleting $certificate_addr");
                }else{
                    echo ("Deleted $certificate_addr");
                }
            }else{
                echo ("$certificate_addr is not found!");
            }
        }

        $this->getProjectTable()->deleteProject($projid);//更新数据库状态
//        echo "<script>alert('".$projid."/".$a."');</script>";
        return $this->redirect()->toRoute('stu/default',array('controller'=>'addinfo','action'=>'addInfoProject'));

    }
    /**
     * @author cry
     * @function: 学生补充信息  个人奖励（含优秀营员）
     */
    public function addInfoHonourAction()
    {
        /* 权限判断 */
        $rid_container =  new Container('rid');
        $ridArr = $rid_container->item;//login 用户的权限rid

        $login_Container = new Container('uid');
        $login_id = $login_Container->item;

        $uid = $this->params()->fromRoute('uid'); //从路由尝试取uid

        if (!in_array(1, $ridArr) && (!in_array(8, $ridArr)) && (!in_array(9, $ridArr)) && !in_array(10, $ridArr) && !in_array(11, $ridArr) && !in_array(12, $ridArr)) {
            //1学生 8/12 学科方向 9/11 院长院秘书 10 研究生院
            echo "<script>alert('您不具有访问权限！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/info');</script>";
            return false;
        }else if (in_array(1,$ridArr) && ($this->getCheckTable()->getCheck($login_id)->status != 2)) {
            //学生状态控制 3(提交审核)->跳转
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        } //权限判断

        if (is_null($uid)){ //学生自己登陆的话，url上面是空的
            $uid = $login_id;
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            if($_FILES['certificate']['size']){//如果上传了凭证
                $image_format = explode("/",$_FILES['certificate']['type']);
                $time = substr(md5(time()), 0, 8);//当前时间加密，以便上传图片相同命名也是唯一的
                $image_name = $uid."_".$time.".".$image_format[1];

                $certificate__abs ="public/img/stu/".$uid."/honour";

                if (!is_dir($certificate__abs)) { //创建存储的目录
                    $res=mkdir($certificate__abs,0777,true);
                    if(!$res){
                        echo "目录 $certificate__abs 创建失败";
                    }
                }

            }
            $certificate_level = array(
                '1' => '国家级',
                '2' => '省市级',
                '3' => '校级',
                '4' => '院级',
                '5' => '其他',

            );
            $honourarr = array(
                'uid' => $uid,   //从session取
                'honour_name' => strip_tags(trim($_POST['honour_name'])),
                'specificdesc' => strip_tags(trim($_POST['specificdesc'])),
                'certificate' => (!empty($_FILES['certificate']['name'])) ? $certificate__abs."/".$image_name : null,
                'certificate_level' => $certificate_level[$_POST['certificate_level']],
                'honour_at' => strip_tags(trim($_POST['honour_at'])),
                'create_at' => date('y-m-d h:i:s'),
            );
            if($this->getHonourTable()->getHonourByUidHname($uid,$_POST['honour_name'])){//是否有相同的项目名字
                $honour = new Honour();
                $honour->exchangeArray($honourarr);
                $honour_flag = $this->getHonourTable()->saveHonour($honour);
                if($_FILES['certificate']['size']) {//存图片
                    $honourfile_flag = move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate__abs . "/" . $image_name);
                }else{
                    $honourfile_flag = 1;
                }
                if($honour_flag && $honourfile_flag){
                    echo "<script>alert('添加成功！请在网页下方查看！');</script>";
                }else{
                    echo "<script>alert('添加失败！请稍后重新上传!');</script>";
                }

            }
        }
        $honour_table =  $this->getHonourTable()->getHonourByUid($uid);
        $view = new ViewModel(array(
            'honour_table' => $honour_table,
            'uid' => $uid,
            'rid'=> $ridArr,
        ));
        return $view;
    }
    public function deleteHonourAction()
    {
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        /* 状态控制 */
        //rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        //学生状态控制 3(提交审核)->跳转
        if($this->getCheckTable()->getCheck($uid)->status != 2){
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        }

        $honourid = $this->params()->fromRoute('id');
        if($this->getHonourTable()->getHonour($honourid)->certificate){//如果存在图片要先删除图片
            $certificate_addr = $this->getHonourTable()->getHonour($honourid)->certificate;

            if (is_file($certificate_addr)) {
                if (!unlink($certificate_addr)){//删除文件
//                    echo ("Error deleting $certificate_addr");
                }else{
//                    echo ("Deleted $certificate_addr");
                }
            }else{
//                echo ("$certificate_addr is not found!");
            }
        }
        $honourdel = $this->getHonourTable()->deleteHonour($honourid);
        if( $honourdel ){
            echo "<script>alert('删除成功！');</script>";
        }else{
            echo "<script>alert('删除失败！请稍后重试！');</script>";
        }
//        echo "<script>alert('".$projid."/".$a."');</script>";
//        return $this->redirect()->toRoute('stu/default',array('controller'=>'addinfo','action'=>'addInfoHonour'));
        echo "<script type=\"text/javascript\">window.location.replace('/stu/addinfo/addInfoHonour');</script>";
        return;
    }
    /**
     * @author cry
     * @function: 学生补充信息  文件凭证
     */
    public function addInfoFileAction()
    {
        /* 权限判断 */
        $rid_container =  new Container('rid');
        $ridArr = $rid_container->item;//login 用户的权限rid

        $login_Container = new Container('uid');
        $login_id = $login_Container->item;

        $uid = $this->params()->fromRoute('uid'); //从路由尝试取uid

        if (!in_array(1, $ridArr) && (!in_array(8, $ridArr)) && (!in_array(9, $ridArr)) && !in_array(10, $ridArr) && !in_array(11, $ridArr) && !in_array(12, $ridArr)) {
            //1学生 8/12 学科方向 9/11 院长院秘书 10 研究生院
            echo "<script>alert('您不具有访问权限！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/info');</script>";
            return false;
        }else if (in_array(1,$ridArr) && ($this->getCheckTable()->getCheck($login_id)->status != 2)) {
            //学生状态控制 3(提交审核)->跳转
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        } //权限判断

        if (is_null($uid)){ //学生自己登陆的话，url上面是空的
            $uid = $login_id;
        }

        //具有登陆资格的学生页面
        $einfo_addr ="/img/stu/" ; //凭证存储的位置,del要访问这里
        $einfo_list = $this->getElectronicinfoTable()->fetchAll();//取学生被要求上传的电子文件信息
        $form = new EinfoForm($einfo_list);

        $request = $this->getRequest();
        if($request->isPost()) {//post提交注册的表单数据
            if(isset($_POST["save"])) {//上传文件
                $issavefiles = 0;//用于表示是否本次post提交了文件，0-未上传，1-上传了
                $savefileArr = array();//用于存储每次用户已经上传了哪些文件

                foreach ($einfo_list as $row) {
                    for( $i = 1 ; $i <= $row->maxnum ; $i++ ) {
                        if (isset($_FILES['einfo_' . $row->id."_".$i]) && $_FILES['einfo_' . $row->id."_".$i]['size']) {
                            $file_format = explode("/", $_FILES['einfo_' . $row->id."_".$i]['type']);

                            $file_name = $row->id ."_".$i. "." . $file_format[1];//文件命名方式
                            $einfo__abs = "public" . $einfo_addr . $uid . "/einfo/". $row->id;// $einfo_addr ="/img/stu/"

                            if (!is_dir($einfo__abs)) { //创建文件夹
                                $res = mkdir($einfo__abs, 0777, true);
                            }
                            if (!is_file($einfo__abs . "/" . $file_name)){
                                $isupload = move_uploaded_file($_FILES['einfo_' . $row->id."_".$i]['tmp_name'], $einfo__abs . "/" . $file_name);//保存文件

                                if($isupload){
                                    $issavefiles = 1;//用于表示是否本次post提交了文件，0-未上传，1-上传了
//                                    $currentfilenum =$this->getEinfoTable()->getStuEinfo($uid, $row->id)->status + 1;
                                    $currentfilenum =$this->getDirFilenum($einfo__abs);
                                    $savefileArr[(int)$row->id] = $currentfilenum;//??
                                    $this->getEinfoTable()->saveStuEinfo($uid, $row->id, $currentfilenum);
                                }
                            }

                        }
                    }
                }//循环存储文件
                if($issavefiles==1){//若上传文件进行提示
                    echo "<script>alert('上传成功！请查看页面！');</script>";
                }

            }
        }


        $srcArr = array();
        $einfomapsurfix =array();

        $einfo_status = $this->getEinfoTable()->getStuEinfoByUid($uid);//查看该学生电子文件上传状态以及份数

        foreach ($einfo_list as $einfo_item){ //获取上传了的文件名
            $surfixArr = explode("|", $einfo_item->surfix); //获取要求的合法后缀

            foreach ($surfixArr as $surfix) {
                for( $i = 1 ; $i <= $einfo_item->maxnum ; $i++ ) {
                    $einfoname[$einfo_item->id][$i] = $einfo_item->id ."_".$i. $surfix;//filename
                    $einfo_file = $einfo_addr . $uid . "/einfo/" . $einfo_item->id ."/". $einfoname[$einfo_item->id][$i];// $einfo_addr ="/img/stu/"
//                        var_dump($einfo_file);
                    if (is_file("public" . $einfo_file)) {
                        $srcArr[$einfo_item->id][$i] = $einfo_file; //文件放置位置
                        $einfomapsurfix[$einfo_item->id][$i] = substr($surfix, 1);//对应的文件后缀
                        continue;//跳出当前循环 ???
                    }
                }
            }
//                var_dump($srcArr);
        }

        $view =new ViewModel(array(
            'form' => $form,
            'einfo_list'=>$einfo_list,
            'einfo_status' => $einfo_status ,
            'srcArr'=>$srcArr,
            'einfo_surfix' =>$einfomapsurfix,
            'uid' => $uid,
            'rid'=> $ridArr,
        ));
        return $view;
    }
    public function deleteFileAction()
    {//实现删除按钮：1-删图，2-修改数据库状态
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        /* 状态控制 */
        //rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        //学生状态控制 3(提交审核)->跳转
        if($this->getCheckTable()->getCheck($uid)->status != 2){
            echo "<script>alert('您已经提交了审核！不能再填写信息！可以查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return false;
        }

        $eid = $this->params()->fromRoute('id');
        $i = $this->params()->fromRoute('param3');
        $einfo_surfix = $this->params()->fromRoute('param2');
        //1-删图
        $einfo_addr ="/img/stu/" ;
        $einfo__abs ="public".$einfo_addr.$uid."/einfo/".$eid."/";
        $einfo_name = $eid."_".$i.".".$einfo_surfix;
        $einfo_file = $einfo__abs.$einfo_name;
        if (is_file($einfo_file)) {
            if (!unlink($einfo_file)){//删除文件
//                echo ("Error deleting $einfo_file");
            }else{
//                echo ("Deleted $einfo_file");
            }
        }else{
//            echo ("$einfo_file is not found!");
        }
        //2-修改数据库状态
        $currentfilenum =$this->getDirFilenum($einfo__abs);
        $this->getEinfoTable()->saveStuEinfo($uid, $eid , $currentfilenum);

        $einfo_delname = $this->getElectronicinfoTable()->getElectronicinfo($eid)->name;
//        echo "<script>alert('".$eid." ".$i."');</script>";
        echo "<script>alert('".$einfo_delname."文件删除成功！');</script>";
//        echo "<script>alert('文件删除成功！');</script>";
        echo "<script type=\"text/javascript\">window.location.replace('/stu/addinfo/addInfoFile');</script>";
        return;
    }

    /**
     * @author cry
     * @function 点击提交按钮后，学生是否可以提交审核，并跳转到相应页面
     */
    public function stuConfirmcheckAction()
    {
        //session获取当前登陆者rid uid
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        /* 状态控制 */
        //rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }
//        echo "<script>alert('".$uid."');</script>";
//        $stu_status_check = $this->getCheckTable()->getCheck($uid);
        //判断是否可以审核
        $stubase_info = $this->getStuBaseTable()->getStu($uid);//用stubase表中target_university字段标识基本信息有无填写
        $stubase_status = $stubase_info->target_university;
        $stueinfo_status = $this->getEinfoTable()->getStuEinfoStatus($uid);

        if(!$stubase_status){//未填写基本信息必填项
            echo "<script>alert('您的基本信息未填写！请完成基本信息的填写！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/addinfo/addInfo');</script>";
            return;
        }
        elseif ($stueinfo_status){//未填写文件凭证必填项
            echo "<script>alert('您的文件凭证必填项".$stueinfo_status."未上传！请补充完毕再提交审核！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/addinfo/addInfoFile');</script>";
            return;
        }else{//提交审核
            //学生信息
            $stu_target_college = $stubase_info->target_college;//获取学生目标院系
            $stu_target_subject = $stubase_info->target_subject;//获取学生目标专业
            $stu_graduate_university = $stubase_info->graduate_university;//获取学生本科大学
            $stu_graduate_profession = $stubase_info->graduate_profession;//获取学生本科专业
            $stu_famous_ut = $this->getUniversityFreeTable()->getUniversitybyId($stu_graduate_university)->is985;//获取本科学生毕业的大学是不是985
            $stu_second_ut = $this->getUniversityFreeTable()->getUniversitybyId($stu_graduate_university)->is211;//获取本科学生毕业的大学是不是211
            $stu_cet4 = (int)$stubase_info->value_cet4; //学生的CET4成绩
            $stu_cet6 = (int)$stubase_info->value_cet6; //学生的CET6成绩

            //过滤条件
            $stuaddinfo_filter = $this->getRegisterFilterTable()->getSubjectid($stu_target_subject,$stu_target_college);//获取学科设置的过滤条件
            $filter_cet4 = (int) $stuaddinfo_filter->value_cet4;
            $filter_cet6 = (int) $stuaddinfo_filter->value_cet6;

//            echo "<script>alert('".$stu_target_subject."/".$stu_target_college."');</script>";
            $gra_under_filter = $this->getSuitabilityTable()->getRelationUnder($stu_target_subject,$stu_target_college);
//            var_dump($gra_under_filter);
//            echo "<script>alert('".$stu_target_college."/".$stu_target_subject."/".$stu_graduate_university."/".$stu_famous_ut."/".$stu_second_ut."/".var_dump($stuaddinfo_filter)."');</script>";
            if( $gra_under_filter && (!in_array($stu_graduate_profession,$gra_under_filter)) ){
//                echo "<script>alert('本科专业不匹配！');</script>";
                $this->changeStuStatus($uid , -1);
                return ;
            }
            else if ( $stuaddinfo_filter->famous_ut && ($stu_famous_ut != 1) ){//famous_ut 1:限制为985 ；0：不限制。要求985，但学生本科学校不是985
//                echo "<script>alert('学科限制要求985，但学生本科不是985');</script>";
                $this->changeStuStatus($uid , -1);
                return ;
            }
            elseif ($stuaddinfo_filter->second_ut && ($stu_second_ut != 1)){//second_ut 1:限制为211 ；0：不限制。要求211，但学生本科学校不是211
//                echo "<script>alert('学科限制要求211，但学生本科不是211');</script>";
                $this->changeStuStatus($uid , -1);
                return ;

            }
            else if ( $stuaddinfo_filter->CET4 && ($stu_cet4 < $filter_cet4)){//CET4  1:限制对CET4分数有要求 ；0：不限制.学生CET4成绩不达标
//                echo "<script>alert('学科限制要求CET4分数，但学生CET4未达标');</script>";
                $this->changeStuStatus($uid , -1);
                return ;
            }
            elseif ($stuaddinfo_filter->CET6 && (empty($stu_cet6) ||  $stu_cet6 < $filter_cet6)){//CET6  1:限制对CET6分数有要求 ；0：不限制.学生CET6成绩不达标
//                echo "<script>alert('学科限制要求CET6分数，但学生CET6未达标或者没有填写');</script>";
                $this->changeStuStatus($uid , -1);
                return ;
            }
            else{
                $this->changeStuStatus( $uid , 3 );
                return ;
            }
        }
    }

    /*==============================联动下拉 ajax返回==============================*/
    public function selectProclassByCidAction() //根据本科学科门类id查询本科专业类
    {
        /* *****权限控制  ***** */
        //session获取当前登陆者rid uid
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        //状态控制：rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        $cid= $this->params()->fromRoute('select');
        $proclassarr = $this->getUnderSubjectTable()->getProclassByCid($cid);
//        var_dump($proclassarr);
        $response = $this->getResponse();
        $response->setContent(json_encode(array('data' => $proclassarr)));
////        return array('collegearr'=>$collegeArr);
        return $response;
    }
    public function selectProByPcidAction()//根据本科专业类id查询本科专业类
    {
        /* *****权限控制  ***** */
        //session获取当前登陆者rid uid
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        //状态控制：rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        $pcid= $this->params()->fromRoute('select');
        $proarr = $this->getUnderSubjectTable()->getProByPcid($pcid);
//        var_dump($proarr);
        $response = $this->getResponse();
        $response->setContent(json_encode(array('data' => $proarr)));
        return $response;
    }
    public function selectProByCollegeidAction()//根据目标院系(BFU)id查询目标专业
    {
        /* *****权限控制  ***** */
        //session获取当前登陆者rid uid
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        //状态控制：rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        $pcid= $this->params()->fromRoute('select');
        $bfu_proarr = $this->getSubjectTable()->getSubjectsIDNameArr($pcid);
//        var_dump($bfu_proarr);
        $response = $this->getResponse();
        $response->setContent(json_encode(array('data' => $bfu_proarr)));
        return $response;
    }
    public function selectDirectionByProidAction()
    {
        /* *****权限控制  ***** */
        //session获取当前登陆者rid uid
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        //状态控制：rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        $cid= $this->params()->fromRoute('select');
        $pid= $this->params()->fromRoute('param3');
        $bfu_directionarr = $this->getProfessionTable()->getFulltimeDirectionsIDNameArr($cid,$pid);
//        var_dump($bfu_directionarr);
        $response = $this->getResponse();
        $response->setContent(json_encode(array('data' => $bfu_directionarr)));
        return $response;
    }
    public function selectTeacherByTargetAction()
    {
        /* *****权限控制  ***** */
        //session获取当前登陆者rid uid
        $rid_container =  new Container('rid');
        $rid = $rid_container->item;
        $uid_container =  new Container('uid');
        $uid = (int)$uid_container->item;

        //状态控制：rid是不是学生身份 && 从stu_base取学生uid是否存在来判断是否登录，是否可以进入这个页面
        if(  !in_array(1,$rid) || (!$this->getUsrStuTable()->getUserByid($uid)) ){//判断是否登录，是否可以进入这个页面
            echo "<script>alert('您不是具有推免资格的学生！不能登陆该页面');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/register/register');</script>";
            return false;
        }

        $cid= $this->params()->fromRoute('param3');
        $pid= $this->params()->fromRoute('param4');
        $directid= $this->params()->fromRoute('select');

        $bfu_staffarr=$this->getProfessionTable()->getProfessionFullTeachByPidSidCid($directid,$pid,$cid);


        $response = $this->getResponse();
        $response->setContent(json_encode(array('data' => $bfu_staffarr)));
        return $response;
    }


    /**
     * @function 改变学生状态
     * @param $uid
     * @param $status
     * @return bool
     */
    public function changeStuStatus($uid,$status){
//        echo "<script>alert('".$uid."/".$status."');</script>";
        if($this->getCheckTable()->updateStuStatus($uid,$status)){//提交审核后，学生状态更改为3
            echo "<script>alert('提交审核成功！可查看您的个人信息！');</script>";
            echo "<script type=\"text/javascript\">window.location.replace('/stu/stu/detail');</script>";
            return true;
        }
        else{
            echo "<script>alert('提交审核失败！请稍后重试！');</script>";
            echo "<script type=\"text/javascript\">window.history.back(-1); ;</script>";//回退到上一页面
            return false;
        }
    }

    /**
     * @author cry
     * @function 获取目录下文件个数
     * @param $dir
     * @return int
     */
    public function getDirFilenum($dir){
        $handle = opendir($dir);
        $i = 0;
        while( false !== ($file=readdir($handle)) ){
            if($file!='.' && $file!='..'){
                $i++;
            }
        }
        closedir($handle);
        return $i;
    }
    /**
     * @param $dirName
     * @return bool
     */
    /**
     * @param $dirName
     * @return bool
     */
    public function delDirAndFiles($dirName)
    {
        if ( $handle = opendir( "$dirName" ) ) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dirName/$item")) {
                        delDirAndFiles("$dirName/$item");
                    } else {
                        if (unlink("$dirName/$item")) {
//                            echo "成功删除文件：" . $dirName ."/". $item . "\n";
                        } else {
                            echo "未能删除文件：" . $dirName ."/". $item . "\n";
                        }
                    }
                }
            }
            closedir( $handle );
            if( rmdir( $dirName ) ){
//                echo "成功删除目录： $dirName  \n";
                return true;
            }else {
                echo "未能删除目录： $dirName  \n";
                return false;
            }
        }else {
            echo "未能打开文件夹：" . $dirName  . "\n";
            return false;
        }
    }
    /*==============================数据库交互==============================*/
    // 全局配置表
    public function getConfigTable()
    {
        if (!$this->config_table) {
            $sm = $this->getServiceLocator();
            $this->config_table = $sm->get('Setting\Model\ConfigTable');
        }
        return $this->config_table;
    }
    public function getMailqueue()
    {//获取发送邮件类
        if (!$this->mailqueue) {
            $um = $this->getServiceLocator();
            $this->mailqueue = $um->get('Info\Model\MailqueueTable');
        }
        return $this->mailqueue;
    }
    public function getvalidatemailTable()//InfovalidatemailTable
    {
        if (!$this->validatemailTable) {
            $um = $this->getServiceLocator();
            $this->validatemailTable = $um->get('Stu\Model\InfovalidatemailTable');
        }
        return $this->validatemailTable;
    }
    public function getUnderSubjectTable() //db_under_subject
    {
        if (!$this->undersubjectTable) {
            $sm = $this->getServiceLocator ();
            $this->undersubjectTable = $sm->get( 'Stu\Model\UnderSubjectTable' );
        }
        return $this->undersubjectTable;
    }
    public function getCollegeTable()//base_college
    {
        if (!$this->collegeTable) {
            $sm = $this->getServiceLocator();
            $this->collegeTable = $sm->get('Basicinfo\Model\CollegeTable');
        }
        return $this->collegeTable;
    }
    public function getSubjectTable()//base_subject
    {
        if (!$this->subjectTable) {
            $sm = $this->getServiceLocator();
            $this->subjectTable = $sm->get('Basicinfo\Model\SubjectTable');
        }
        return $this->subjectTable;
    }
    public function getProfessionTable()//base_profession
    {
        if (!$this->professionTable) {
            $sm = $this->getServiceLocator();
            $this->professionTable = $sm->get('Basicinfo\Model\ProfessionTable');
        }
        return $this->professionTable;
    }
    public function getProfessionstaffTable()//base_profession
    {
        if (!$this->professionstaffTable) {
            $sm = $this->getServiceLocator();
            $this->professionstaffTable = $sm->get('Basicinfo\Model\ProfessionstaffTable');
        }
        return $this->professionstaffTable;
    }
    protected function getStuBaseTable()
    {
        if (!$this->stubaseTable) {
            $sm = $this->getServiceLocator();
            $this->stubaseTable = $sm->get('Stu\Model\StuBaseTable');
        }
        return $this->stubaseTable;
    }
    protected function getStaffTable()
    {
        if (!$this->staffTable) {
            $sm = $this->getServiceLocator();
            $this->staffTable = $sm->get('Basicinfo\Model\StaffTable');
        }
        return $this->staffTable;
    }
    protected function getProjectTable()
    {
        if (!$this->projectTable) {
            $sm = $this->getServiceLocator();
            $this->projectTable = $sm->get('Stu\Model\ProjectTable');
        }
        return $this->projectTable;
    }
    protected function getHonourTable()
    {
        if (!$this->honourTable) {
            $sm = $this->getServiceLocator();
            $this->honourTable = $sm->get('Stu\Model\HonourTable');
        }
        return $this->honourTable;
    }
    public function getUsrStuTable() //StuBaseTable
    {
        if (! $this->usrstuTable) {
            $sm = $this->getServiceLocator ();
            $this->usrstuTable = $sm->get ( 'Stu\Model\UsrStuTable' );
        }
        return $this->usrstuTable;
    }
    public function getElectronicinfoTable() {
        if (!$this->electronicinfoTable) {
            $um = $this->getServiceLocator();
            $this->electronicinfoTable = $um->get('Stu\Model\ElectronicinfoTable');
        }
        return $this->electronicinfoTable;
    }
    public function getEinfoTable() {
        if (!$this->einfoTable) {
            $um = $this->getServiceLocator();
            $this->einfoTable = $um->get('Stu\Model\EinfoTable');
        }
        return $this->einfoTable;
    }
    public function getCheckTable()
    {
        if (!$this->check_table) {
            $sm = $this->getServiceLocator();
            $this->check_table = $sm->get('Stu\Model\CheckTable');
        }
        return $this->check_table;
    }
    public function getUniversityFreeTable()//UniversityTable
    {
        if (! $this->universityfreeTable) {
            $sm = $this->getServiceLocator ();
            $this->universityfreeTable = $sm->get ( 'Stu\Model\UniversityFreeTable' );
        }
        return $this->universityfreeTable;
    }
    public function getNationalityTable() {
        if (!$this->nationalityTable) {
            $um = $this->getServiceLocator();
            $this->nationalityTable = $um->get('Stu\Model\NationalityTable');
        }
        return $this->nationalityTable;
    }
    public function getPoliticalStatusTable() {
        if (!$this->politicalstatusTable) {
            $um = $this->getServiceLocator();
            $this->politicalstatusTable = $um->get('Stu\Model\PoliticalStatusTable');
        }
        return $this->politicalstatusTable;
    }
    public function getRegisterFilterTable(){
        if (!$this->registerfilterTable) {
            $um = $this->getServiceLocator();
            $this->registerfilterTable = $um->get( 'Basicinfo\Model\RegisterFilterTable' );
        }
        return $this->registerfilterTable;
    }
    public function getSuitabilityTable() {
        if (! $this->suitabilityTable) {
            $sm = $this->getServiceLocator ();
            $this->suitabilityTable = $sm->get ('Basicinfo\Model\SuitabilityTable');
        }
        return $this->suitabilityTable;
    }
}