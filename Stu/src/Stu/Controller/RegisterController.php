<?php
/**
 * @author cry
 * @function 学生注册Controller
 */
namespace Stu\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Stu\Form\RegisterForm;
use Stu\Model\StuBase;
use Stu\Model\UsrStu;
use Stu\Model\Check;
use Stu\Model\MyBarcode;


class RegisterController extends AbstractActionController
{
    protected $msg;
    protected $validatemailTable;
    protected $stubaseTable;
    protected $universityfreeTable;
    protected $usrstuTable;
    protected $check_table;
    protected $config_table;
    protected $managetimeTable;
    protected $electronicinfoTable;
    protected $einfoTable;
    protected $nationalityTable;
    protected $politicalstatusTable;


    /**
     * @author cry
     * @function:测试个别功能入口，正式为不存在，不可访问
     */
    public function indexAction()
    {
        echo "this is index of register";
        exit();
        /* 2 - Transaction test */
//        $this->adapter->getDriver()->getConnection()->beginTransaction();   //Start Transaction
//        $tablegate = new TableGateway();
//        $adapter =  $this->getServiceLocator()->get ( 'Zend\Db\Adapter\Adapter' );
//        try {
//            $adapter->getDriver()->getConnection()->beginTransaction();
//            $this->getEinfoTable()->saveStuEinfo(1, 2, 0);
//            var_dump("112");
//            $adapter->getDriver()->getConnection()->commit();
//        }catch(Exception $e) {
//            var_dump("500");
//            $adapter->getDriver()->getConnection()->rollback();
//        }
        /*1 - barcode test*/
//        $barcode = new MyBarcode(); //生成可以生成一维码的类
//        $content = "011100199607210522"; //用生成一维码的数据
//        $img_url = "/img/barcodetmp/1.png"; //临时存放图片地址""
//        $barcode->createBarcode($content,"public".$img_url);//默认有$content文字显示在下面
//        $barcode->createBarcode($content);//直接生成png
//        $barcode->createBarcode($content,"public".$img_url,false);//没有$content文字显示在下面
    }

    /**
     * @author cry
     * @function:推免学生入口注册
     * @return ViewModel
     */
    public function registerAction()
    {
        /* 状态判断 */
        //判断是否开启了注册时间段。若开起了，判断是否在注册时间内
        $canregister = $this->getManagetimeTable()->findid(1);//查看注册项目的具体控制信息(1为注册项目的id)
        date_default_timezone_set(PRC);
        $currenttime = date('Y-m-d H:i:s');//freebsd 修改系统时间date 201908301458

        if( $canregister->status == 1 ){//时间控制开启了
            if(strtotime($currenttime) < strtotime($canregister->start_time)){
                echo "<script>alert('当前时间 ".$currenttime."!学生注册通道尚未开启！请密切关注！');</script>";
                echo "<script type=\"text/javascript\">window.location.replace('/info');</script>";
                return false;
            }else if(strtotime($currenttime) > strtotime($canregister->end_time)){
                echo "<script>alert('当前时间 ".$currenttime."!学生注册通道已关闭！');</script>";
                echo "<script type=\"text/javascript\">window.location.replace('/info');</script>";
                return false;
            }
        }

        /*注册表单*/
        $graduate_university_pro = $this->getUniversityFreeTable()->getProvinceArr();//获取有推免资格大学的省份
        $nationalityArr = $this -> getNationalityTable()->fetchAllArr(); //民族
        $politicalstatusArr = $this->getPoliticalStatusTable()->fetchAllArr();//政治面貌

        $form = new RegisterForm($graduate_university_pro,$nationalityArr,$politicalstatusArr);

        $mail_api = $this->getConfigTable()->getConfigKey("mail_api"); //邮箱发送后端api

        $request = $this->getRequest();
        if($request->isPost()) { //验证是否是有post方式传入的表单

            $province_select = $_POST['graduate_university_province'];
            $graduate_university = $this->getUniversityFreeTable()->getUniArrByPid($province_select);

            $form = new RegisterForm($graduate_university_pro,$nationalityArr,$politicalstatusArr,$graduate_university);
            $form->setData($request->getPost());//数据从request post里面取，并用表单的setData()返回在表单中

            if ($form->isValid()) {//如果数据是有效的
//                $form->getInputFilter()->getValues();
                $this->stuBaseInvalid($form);//调用stuBaseInvalid函数，对推免生注册的信息进行验证
            }else{
                $this->formInvalidMessage($form);//输出表单错误信息
                //echo "<script> alert('表单填写出错！') </script>";
                //获取本科高校$province_select
            }
        }
        $view =new ViewModel(array(
            'mail_api' =>$mail_api,
            'form' => $form,
        ));
        return $view;
    }

    /**
     * @author cry
     * @function 验证邮箱查看邮箱是否注册，是否可以使用
     */
    public function validatemailAction()
    {
        $addr =$this->params()->fromRoute('mail');//通过url获取邮箱地址
//        $row =  $this->getvalidatemailTable()->getValidatemail(trim($addr));//在是否激活邮箱表里查
        $ifregisterEmail = $this->getStuBaseTable()->checkTstuRegister("email",trim($addr));
        $ifactive = $this->getvalidatemailTable()->getIfValiateByactive(md5(trim($addr)));
        $response = $this->getResponse();
        if($ifregisterEmail == -1){//邮箱在学生表已经被注册
            $response->setContent(json_encode(array('data' => '该邮箱已注册！请更换邮箱','ifregisterEmail' => 1,'ifactive'=>1)));
            return $response;
        }
        else if($ifactive){ //
            $response->setContent(json_encode(array('data' => '该邮箱已经被激活，请完成注册并提交！','ifregisterEmail' => 0,'ifactive'=>1)));
            return $response;
        }
        else{
            $response->setContent(json_encode(array('data' => '准备向该邮箱发送邮件中！请及时查看邮箱是否接到邮件，并进行激活！(Tips:若未收到邮件，请查看一下是否在垃圾邮箱内！)','ifregisterEmail' => 0,'ifactive'=>0)));
            return $response;
        }
    }
    /**
     * @author cry
     * @function:点击链接激活邮件(插入数据库)
     */
    public function activemailAction()
    {
//        $addrencrypt = base64_decode($this->params()->fromRoute('active'));//取得加密的邮箱地址
        $addrencrypt =$this->params()->fromRoute('active');//通过url获取邮箱地址
        if( $addrencrypt ){
            $rowactive = $this->getvalidatemailTable()->getValidatemailbyactive($addrencrypt);//检查是否激活过
            if (!$rowactive) {//未找到条目，此邮箱还未激活
                $email = array(
                    'active' => $addrencrypt,
                    'status' => 1 //1代表激活
                );

                if (!$this->getvalidatemailTable()->saveValidatemail($email)) { //保存该条目
                    echo "<script>alert('激活失败！请重新验证邮箱');opener=null;window.close();</script>";
                    return;
                }
                else {
                    echo "<script>alert('激活成功！请尽快按照要求填写完注册信息，并提交！');opener=null;window.close();</script>";
                    return;
                }
            }
            elseif ($rowactive->status == 1) {//已成功验证的
                echo "<script>alert('已激活请勿重复激活！');opener=null;window.close();</script>";
                return;
            }
            else {
                echo "<script>alert('激活失败！');opener=null;window.close();</script>";
                return;
            }
        }

    }

    /**
     * @author cry
     * @function:ajax请求 根据前端选择的省份，返回该省份具有推免资格的大学
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function selectUniByPidAction()
    {
        $response = $this->getResponse();
        $pid = $this->params()->fromRoute('select');
        if($pid){
            $unis = $this->getUniversityFreeTable()->getUniArrByPid($pid);
//            var_dump($unis);
        }else{
            $unis = "";
        }
        $response->setContent(json_encode(array('data' =>$unis )));
        return $response;
    }

    /**
     * @author cry
     * @function: 对推免生注册的数据进行处理，表单验证
     * @table：stu_base,
     * @param $form
     */
    public function stuBaseInvalid($form)
    {
        $filterdValues = $form->getInputFilter()->getValues();
        $ifregisterIdcard = $this->getStuBaseTable()->checkTstuRegister("idcard",$filterdValues["idcard"]);//检查StuBaseTable身份证号是否已经注册
        $ifregisterEmail = (!empty($filterdValues["email"])) ? $this->getStuBaseTable()->checkTstuRegister("email",$filterdValues["email"]) : null;//检查StuBaseTable该邮箱是否已经注册
        $ifregisterEmailActive = (!empty($filterdValues["email"])) ? $this->getvalidatemailTable()->getValidatemail(md5($filterdValues["email"]))->status :null;
//        $freetest_qualified = $this->getUniversityFreeTable->getUniversitybyId($filterdValues["graduate_university"]);//检查传入的大学名称是否具有推免资格
        $cet4 = (int)$filterdValues['value_cet4'];

        //判断
        if($ifregisterIdcard == -1){//身份证号已经被注册
            echo "<script> alert('该身份证号已注册！') </script>";
            return ;
        }
        else if($ifregisterEmail == -1){//邮箱已经被注册
            echo "<script> alert('该邮箱已经注册！') </script>";
            return;
        }
        else if($ifregisterEmailActive != 1 ) {//检查InfovalidatemailTable中该邮箱是否被激活了
            echo "<script> alert('该邮箱未激活，请验证邮箱！') </script>";
            return;
        }
        else if( $cet4 < 425 && ($cet4!=0)){
            echo "<script> alert('您的英语四级成绩(必填)".$cet4."分，未达到推免资格的英语四级成绩标准425分！') </script>";
            return;
        }
//        else if( $freetest_qualified !=1){
//            echo "<script> alert('您的本科大学不具备推免资格！有疑问请联系管理员邮箱！') </script>";
//        }
        else{//将注册信息存储到相应的数据表中
            echo "<script> alert('正在上传您的注册信息。温馨提示：本系统用户名为电子邮箱，初始密码为身份证号') </script>";

            try{
                /*  事务  */
                $adapter =  $this->getServiceLocator()->get ( 'Zend\Db\Adapter\Adapter' );
                $adapter->getDriver()->getConnection()->beginTransaction();

                //1、存入usr_stu表
                $usrstu = new UsrStu();
                $salt = substr(md5(uniqid()),1,5);
                $password = md5($salt.strtoupper(trim($filterdValues['idcard'])).$salt);
                $usrstudata = array(
                    'user_name' => strip_tags(trim($filterdValues['user_name'])),
                    'email' => strip_tags(trim($filterdValues['email'])),
                    'salt' => $salt,
                    'password' => $password,
                    'create_at' => date("Y-m-d H:i"),
                    'update_at' => date("Y-m-d H:i"),
                );
                $usrstu->exchangeArray($usrstudata);
                $this->getUsrStuTable()->saveUserRegister($usrstu); //可以提交或者回滚的标志
//            if($this->getUsrStuTable()->saveUser($usrstu)){
//                echo "usr_stu success'";
//            }else{
//                echo "usr_stu fail";
//            }

                //2、存入stu_base表
                $idcard = trim($filterdValues['idcard']);
                if(strtoupper(substr($idcard,-1))=='X'){//身份证xX结尾转换为X
                    $filterdValues['idcard'] = strtoupper($filterdValues['idcard']);
                }

                $graduate_university_info = $this->getUniversityFreeTable()->getUniversitybyId($filterdValues['graduate_university']);
                if($graduate_university_info->is985 == 1){
                    $universitylevel = 9;
                }elseif (($graduate_university_info->is985 != 1) && ($graduate_university_info->is211 == 1)){
                    $universitylevel = 2;
                }else{
                    $universitylevel = -1;
                }

                $stu_register = array(//strip_tags(trim())
                    'apply_type'    => $filterdValues['apply_type'],
                    'user_name'     =>  strip_tags(trim($filterdValues['user_name'])),
                    'gender'        =>  $filterdValues['gender'],
                    'idcard'        =>  strip_tags(trim($filterdValues['idcard'])),
                    'nationality'   =>  $filterdValues['nationality'],
                    'political_status'  =>  $filterdValues['political_status'],
                    'phone'         =>  strip_tags(trim($filterdValues['phone'])),
                    'email'         =>  strip_tags(trim($filterdValues['email'])),
                    'graduate_university' =>  $filterdValues['graduate_university'],
                    'universitylevel'   =>  $universitylevel,
                    'value_cet4'     =>  strip_tags(trim($filterdValues['value_cet4'])),
                );
                $stubase = new StuBase();
                $stubase->exchangeArray($stu_register);
                $stubase->uid = $this->getUsrStuTable()->getUserByemail(trim($filterdValues['email']))->uid;
                $this->getStuBaseTable()->saveStu($stubase);


                //3、存入stu_check表
                $nowtime = date("Y-m-d H:i:s");
                $data = array(
                    'uid' => $stubase->uid,
                    'status' => 2 , //  数据库中stu_check该行状态变为 2
                    'create_at' => $nowtime,
                    'update_at' => $nowtime,

                );
                $check_table = new Check();
                $check_table->exchangeArray($data);
                $this->getCheckTable()->saveCheck($check_table);//可以提交或者回滚的标志
//            if($this->getCheckTable()->saveCheck($check_table)){
//                echo "stu_check success'";
//            }else{
//                echo "stu_check fail";
//            }

                //4、存入电子凭证表  初始设置0
                $einfo_list = $this->getElectronicinfoTable()->fetchAll();//取学生被要求上传的电子文件信息
                foreach ($einfo_list as $row) {
                    $this->getEinfoTable()->saveStuEinfo($stubase->uid, $row->id,0);
//                if($this->getEinfoTable()->saveStuEinfo($stubase->uid, $row->id,0)){
//                    echo "stu_einfo success'";
//                }else{
//                    echo "stu_einfo fail'";
//                }
                }
                $adapter->getDriver()->getConnection()->commit();  //提交
                echo "<script> alert('注册成功！请使用电子邮箱作为用户名，身份证号作为初始密码点击网页右上端“登录”登录本系统，进一步补充资料。') </script>";
                echo "<script type=\"text/javascript\">window.location.replace('/user/login/login');</script>";
            }catch(\Exception $e){
                $adapter->getDriver()->getConnection()->rollback(); //出现异常，回滚
                $message = $e->getMessage();
                $code = $e->getCode();
                echo "<script> alert('出现异常，注册失败！$message && $code') </script>";
            }

        }


    }

    /**
     * @author cry
     * @function:表单验证无效的原因报错的显示
     * @param $form
     */
    public function formInvalidMessage($form)
    {
        $messages = $form->getMessages();
        $translate = $this->getStubaseDetailColumns();
        $msgCn = $this->getMsgCn();
        echo "<script>alert('表单验证无效：";
        foreach ($messages as $key => $value) {
            echo $translate[$key] . " 有\"";
            foreach ($value as $key1 => $value1) {
                if (isset($msgCn[$key1])) {
                    echo $msgCn[$key1];
                } else {
                    echo $key1;
                }
                echo ":" . $value1 . "\" ";
            }
            echo "错误，";
        }
        echo "请检查更正后重新提交');</script>";
    }
    public function getMsgCn()
    {
        return array(
            'stringLengthTooShort' => '长度不够',
            'stringLengthTooLong' => '长度过长',
            'notDigits' => '不是数字',
        );
    }
    public function getStubaseDetailColumns()
    {
        return array(
            'idcard'    =>  '身份证号码',
            'phone'     =>  '手机',
            'email'     =>  '邮箱',
        );
    }

    /*==============================数据库交互==============================*/
    public function getvalidatemailTable()//InfovalidatemailTable
    {
        if (!$this->validatemailTable) {
            $um = $this->getServiceLocator();
            $this->validatemailTable = $um->get('Stu\Model\InfovalidatemailTable');
        }
        return $this->validatemailTable;
    }
    public function getStuBaseTable() //StuBaseTable
    {
        if (! $this->stubaseTable) {
            $sm = $this->getServiceLocator ();
            $this->stubaseTable = $sm->get ( 'Stu\Model\StuBaseTable' );
        }
        return $this->stubaseTable;
    }
    public function getUsrStuTable() //StuBaseTable
    {
        if (! $this->usrstuTable) {
            $sm = $this->getServiceLocator ();
            $this->usrstuTable = $sm->get ( 'Stu\Model\UsrStuTable' );
        }
        return $this->usrstuTable;
    }
    public function getUniversityFreeTable()//UniversityTable
    {
        if (! $this->universityfreeTable) {
            $sm = $this->getServiceLocator ();
            $this->universityfreeTable = $sm->get ( 'Stu\Model\UniversityFreeTable' );
        }
        return $this->universityfreeTable;
    }
    public function getCheckTable()
    {
        if (!$this->check_table) {
            $sm = $this->getServiceLocator();
            $this->check_table = $sm->get('Stu\Model\CheckTable');
        }
        return $this->check_table;
    }
    public function getConfigTable()
    {
        if (!$this->config_table) {
            $sm = $this->getServiceLocator();
            $this->config_table = $sm->get('Setting\Model\ConfigTable');
        }
        return $this->config_table;
    }
    public function getManagetimeTable()
    {
        if (!$this->managetimeTable) {
            $sm = $this->getServiceLocator();
            $this->managetimeTable = $sm->get('Basicinfo\Model\ManagetimeTable');
        }
        return $this->managetimeTable;
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
}