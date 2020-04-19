<?php
namespace Manage\Controller;

use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Manage\Form\LoginForm;
use Manage\Controller\ImageCode;
use Manage\Model\MyAuth;
use Manage\Form\ImgCaptchaValidator;
use Manage\Model\UsrTeacher;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;


class AccountController extends AbstractActionController{
    private $usrteacherTable;
    private $userroleTable;
    private $permissionTable;
    private $rolepermissionTable;
    private $staffTable;

    /**
     * @author  ly
     * @brief   教师登录
     * @param   NULL
     * @return  NULL
     */
    public function loginTeacherAction() {
        if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['captcha'])) {
            //setcookie('sign', $_POST['username'], 0, '/');
            $containerCaptcha = new Container('captcha');
            //echo $_POST['captcha']."cap: ".$containerCaptcha->item."<br>"; exit;
            $user = $this->getUsrteacherTable()->getUserByEmail($_POST['username']);
            if (!$user) {
                echo "<script>alert('该教师未注册，请重新输入');</script>";
            }
            else if ($_POST['captcha'] != $containerCaptcha->item) {
                echo "<script>alert('验证码错误，请重新输入');</script>";
            }
            else{
                if ($this->TryLoginTec($user, $_POST['password'])) {
                    $session = new Container('pids');
                    var_dump($session->item);
                    $session = new Container('pidArr');
                    var_dump($session->item);
                    $session = new Container('purlArr');
                    var_dump($session->item);
                    $session = new Container('purlArr2');
                    var_dump($session->item);
                    $session = new Container('college_id');
                    var_dump($session->item);
                    $session = new Container('user_name');
                    var_dump($session->item);
                    $session = new Container('staff_id');
                    var_dump($session->item);
                    $session = new Container('uid');
                    var_dump($session->item);
                    $session = new Container('rid');
                    var_dump($session->item);
                    echo "登录成功<br/>";

//                    $sso_info = $this->generteUrlParams();
//                    setcookie('sign', $sso_info, 0, '/');

                    $this->deleteCaprchaImg();
                    exit;
                }
                else echo "<script>alert('密码错误，请重新输入');</script>";
            }
        }

        $this->deleteCaprchaImg();

        return array('imgSrc'=>$this->createCaptcha());
    }

    //教师登陆尝试
    public function TryLoginTec($user,$password) {
        $staffid = $user->staff_id;
        $email = $user->email;
        $tablename = "usr_teacher";
        $identityColumn = "email";
        if($this->authenticate($email,$password,$tablename,$identityColumn)){
            //2.用staffid查所有的rid userrole表
            $staffids = array("uid"=>$staffid);//定义一个数组，键名为uid，值为staffid
            $userroles = $this->getUserroleTable()->getUserrole($staffids);
            $ridArr = array();
            $purlArr = array();
            $pidArr = array();
            foreach ($userroles as $key => $urrow) {
                if(!$urrow){
                    //throw new \Exception("could not find row");
                }
                else{
                    //echo "&nbsp;rid = ".$urrow->rid."<br/>";
                    $ridArr[] = $urrow->rid;
                    $pids="";
                    //3.用每个rid查所有的pid rolepermission表
                    $rpids = array("rid"=>$urrow->rid);
                    $rolepermissions = $this->getRolepermissionTable()
                        ->getRolepermission($rpids);

                    foreach ($rolepermissions as $key => $rprow) {
                        $pidArr1 = array();
                        if(!$rprow){
                            //throw new \Exception("could not find row");
                        }
                        else{
                            $pidArr1[] = $rprow->pid;
                            $pidArr = array_merge($pidArr,$pidArr1);
                            //echo "&nbsp;&nbsp;pid = ".$rprow->pid."<br/>";
                            if($pids=="")
                                $pids = $rprow->pid;
                            else
                                $pids.=",".$rprow->pid;
//                            echo $rprow->pid."<br/>";
                        }
                    }
                    $containerPids = new Container('pids');
                    $containerPids->item = $pids;
//                     echo "<br/>pid Arr =";
//                     var_dump($pidArr);
//                     echo "<br/>pid = ".$pids."<br/><br/>";
                }
            }
//             print_r($pidArr);
//
//             print_r(array_unique($pidArr));

            $containerPidArr = new Container('pidArr');
            $containerPidArr->item = $pidArr;

            $purlArr = $this->getPermissionTable()->getPermissionStringArrByPidArr($pidArr);
//            var_dump($purlArr);
            $containerPidArr = new Container('purlArr');
            $containerPidArr->item = $purlArr;

            $purlArr2 = $this->getPermissionTable()->getPermissionArr($pidArr);
            $containerPidArr = new Container('purlArr2');
            $containerPidArr->item = $purlArr2;

            //设置session
            $staff = $this->getStaffTable()->getStaff($user->staff_id);
            $containerCol = new Container('college_id');
            $containerCol->item = $staff->college_id;
            $containerUname = new Container('username');
            $containerUname->item = $user->username;

            $containerStaffid = new Container('staff_id');
            $containerStaffid->item = $user->staff_id;

            $containerUid = new Container('uid');
            $containerUid->item = $user->staff_id;

            $containerRid = new Container('rid');
            $containerRid->item = $ridArr;

            return true;
        }
        return false;
    }

    /** TOOLS **/
    public function captchaImgAction() {
        $this->deleteCaprchaImg();
        $imgSrc = $this->createCaptcha();
        $view = new ViewModel(array(
            "imgSrc"=>$imgSrc
        ));
        $view->setTerminal(true);
        return $view;
    }
    public function createCaptcha() {//产生验证码图片，返回图片路径
        $sessionKey_Captcha = 'sesscaptcha';
        $img = new ImageCode();
        $img
            ->setFontSize(28)
            ->setHeight(20)
            ->setWidth(60)
            ->setDotNoiseLevel(8)
            ->setLineNoiseLevel(1)
            ->setImgDir('public/img/captcha')
            ->setImgUrl('')
            ->setWordlen(4)
        ;
        $img->generate();
        $imgSrc = $img->getImgUrl(). $img->getId() .$img->getSuffix();
        $captchaWord = $img->getWord();
        //$sessionStorage = new SessionArrayStorage();
        //$sessionStorage->offsetSet($sessionKey_Captcha, $captchaWord);
        $containerCaptcha = new Container('captcha');
        $containerCaptcha->item = $captchaWord;
        $containerCaptchaImgPath = new Container('captchaImgPath');
        $containerCaptchaImgPath->item = $imgSrc;
        return $imgSrc;
    }
    public function deleteCaprchaImg(){
        $containerCaptchaImgPath = new Container('captchaImgPath');
        $imgSrc = $containerCaptchaImgPath->item;
        $realtedPath = __DIR__."/../../../../../public/img/captcha";
//        if(is_file("/usr/local/www3/data/public/img/captcha".$imgSrc))
//            unlink("/usr/local/www3/data/public/img/captcha".$imgSrc);
        if(is_file($realtedPath.$imgSrc))
            unlink($realtedPath.$imgSrc);
    }

//===================登录身份认证========================
    public function authenticate($identity,$credential,$tablename,$identityColumn) {
        $auth = new MyAuth();
        if($auth->auth($identity,$credential,$tablename,$identityColumn)){
//            echo "<script>alert('验证成功');</script>";
            return true;
        }
        else{
//            echo "<script>alert('密码错误，请重试');</script>";
            return false;
        }
        //exit;
    }

    //教师登录表
    public function getUsrteacherTable() {
        if (! $this->usrteacherTable) {
            $sm = $this->getServiceLocator ();
            $this->usrteacherTable = $sm->get ( 'Manage\Model\UsrTeacherTable' );
        }
        return $this->usrteacherTable;
    }
    //用户角色表
    public function getUserroleTable() {
        if (! $this->userroleTable) {
            $sm = $this->getServiceLocator ();
            $this->userroleTable = $sm->get ( 'Manage\Model\UsrRoleTable' );
        }
        return $this->userroleTable;
    }
    //角色权限表
    public function getRolepermissionTable() {
        if (! $this->rolepermissionTable) {
            $sm = $this->getServiceLocator ();
            $this->rolepermissionTable = $sm->get ( 'Manage\Model\RolepermissionTable' );
        }
        return $this->rolepermissionTable;
    }
    //员工表
    public function getStaffTable() {
        if (! $this->staffTable) {
            $sm = $this->getServiceLocator ();
            $this->staffTable = $sm->get ( 'User\Model\StaffTable' );
        }
        return $this->staffTable;
    }
    //权限表
    public function getPermissionTable(){
        if (! $this->permissionTable) {
            $sm = $this->getServiceLocator ();
            $this->permissionTable = $sm->get ( 'User\Model\PermissionTable' );
        }
        return $this->permissionTable;
    }

}