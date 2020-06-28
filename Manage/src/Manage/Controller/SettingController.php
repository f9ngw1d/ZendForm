<?php

namespace Manage\Controller;

use Manage\Form\DeleteAllDataForm;
use Manage\Form\DnsConfigForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Manage\Form\IpConfigForm;

class SettingController extends AbstractActionController
{

    protected $TStuBaseTable;
    protected $TBaseCollegeTable;

    public function __construct(){
        $rid_arr_container = new Container('rid');
        $rid_arr = $rid_arr_container->item;
        $redirect_url = "/info";
        if(!$rid_arr || !in_array(10,$rid_arr)){
            echo "<script language='javascript'>alert('没有访问权限');window.location.href='".$redirect_url."';</script>";
            exit();
        }
    }
    public function getTBaseCollegeTable()
    {
        if (!$this->TBaseCollegeTable) {
            $sm = $this->getServiceLocator();
            $this->TBaseCollegeTable = $sm->get('Manage\Model\TBaseCollegeTable');
        }
        return $this->TBaseCollegeTable;
    }
    public $config_table;
    /*author:lrn
    配置信息帮助界面*/
    public function indexAction(){
        $this->getConfigTable();
        $stu_status = $this->config_table->getConfigValueByKey('stu_status');
        $stu_status2 = $this->config_table->getConfigValueByKey('stu_status',10);
        $stu_status3 = $this->config_table->getConfigValueByKey('stu_status',array(2,3));
        $stu_status4 = $this->config_table->getConfigValueByKey('stu_status',array(),false,true);

        $school_name = $this->config_table->getConfigKey('school_name');
        $sys_info = $this->config_table->getConfigKey(array('system_name','current_year'));
        $sys_info2 = $this->config_table->getConfigKey(array('system_name','current_year'),true,false);

        return array(
            'stu_status'=>$stu_status,
            'stu_status2'=>$stu_status2,
            'stu_status3'=>$stu_status3,
            'stu_status4'=>$stu_status4,
            'school_name'=>$school_name,
            'sys_info'=>$sys_info,
            'sys_info2'=>$sys_info2,
        );
    }
/*lrn  配置信息界面*/
    public function configAction(){
        //获取学校、系统名称、技术支持、版权所有、当前招生年份信息
        $column = array(
            'id'=>'编号',
            'key_cn'=>'配置项',
            'key_value'=>'配置值',
            'create_at'=>'创建时间',
            'update_at'=>'更新时间',
            'operation'=>'操作',
        );
        $setting_data = $this->getConfigTable()->getConfigKey(array('school_name','system_name','current_year','short_name','DWDM','KDH','full_marks','copy_right','tech_support',),false,true);
        return array(
            'setting'=>$setting_data,
            'column'=>$column,
        );
    }
    //lrn ip配置
    public function configIpAction(){
        // 权限判断
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
        if (!in_array("99", $rid_arr) && !in_array("10", $rid_arr)) {//url中取得用户角色不属于该用户的话
            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";

        }
        $ip_info = $this->getConfigTable()->getConfigKey(array('ip','net_mask','default_gateway'));
        $form = new IpConfigForm();
        //涉及表单提交
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayData = $request->getPost()->toArray();
            if (isset($_POST['submit'])) {//strcmp($arrayData['submit'], '保存')
                $postData = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray()
                );
                $form->setData($postData);
                //var_dump($postData);
                if ($form->isValid()){
                   // var_dump($postData);
                   // echo "<br><br>";
                    $content = "hostname='xly'
ifconfig_em0='inet ".trim($postData['ip_address1']).".".trim($postData['ip_address2']).".".trim($postData['ip_address3']).".".trim($postData['ip_address4'])." netmask ".trim($postData['net_mask'])."'\n
defaultrouter=".trim($postData['default_gateway'])."
sshd_enable='YES'
# Set dumpdev to 'AUTO' to enable crash dumps, 'NO' to disable
dumpdev='AUTO'
git_daemon_enable='YES'
sendmail_enable='NO'
sendmail_submit_enable='NO'
sendmail_outbound_enable='NO'
sendmail_msp_queue_enable='NO'
";
                    //var_dump($content);
                    $res=file_put_contents("/etc/rc.conf", $content);
                    if($res!=0){
                        $result = $this->getConfigTable()->saveConfigKey(array('id'=>23,'key_value'=>$postData['ip_address1'].".".$postData['ip_address2'].".".$postData['ip_address3'].".".$postData['ip_address4']));
                        $result1 = $this->getConfigTable()->saveConfigKey(array('id'=>24,'key_value'=>$postData['net_mask']));
                        $result2 = $this->getConfigTable()->saveConfigKey(array('id'=>25,'key_value'=>$postData['default_gateway']));
                        echo "<script>alert('已成功设置并写入文件');window.location.href='/manage/Setting/configIp';</script>";
                        $res1 = system('/bin/sh  /usr/local/www/xly/ipanddns.sh',$return_status1);
//                        $res2 = system('/bin/sh  /etc/rc.d/routing  restart',$return_status2);
                        if($return_status1==0){
                            echo "<script>alert('netif restart success')</script>";
                        }
                        else{
                            echo "<script>alert('netif restart failed!')</script>";
                        }
//                        if($return_status2==0){
//                            echo "<script>alert('routing restart success')</script>";
//                        }
//                        else{
//                            echo "<script>alert('routing restart failed!')</script>";
//                        }

                    }else{
                        echo "<script>alert('设置失败，请检查文件读写权限并再次检查')</script>";
                    }
                 }
                else{
                    echo "<script> alert('表单数据有误！请按规范填写');window.location.href='/manage/Setting/configIp';</script>";
                }
            }
        }
        return array(
            'form' => $form,
            'uid' => $login_id,
            'ip' => empty($ip_info['ip']['key_value'])?'未设置':$ip_info['ip']['key_value'],
            'net_mask' =>empty($ip_info['net_mask']['key_value'])?'未设置':$ip_info['net_mask']['key_value'],
            'default_gateway' => empty($ip_info['default_gateway']['key_value'])?'未设置':$ip_info['default_gateway']['key_value'],
        );
    }
    //lrn dns配置
    public function configDnsAction(){
        // 权限判断
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
        if (!in_array("99", $rid_arr) && !in_array("10", $rid_arr)) {//url中取得用户角色不属于该用户的话
            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";

        }
        $dns_info = $this->getConfigTable()->getConfigKey(array('dns_name','dns1','dns2'));
        $form = new DnsConfigForm();
        //涉及表单提交
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayData = $request->getPost()->toArray();
            if (isset($_POST['submit'])) {//strcmp($arrayData['submit'], '保存')
                $postData = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray()
                );
                $form->setData($postData);
                //var_dump($postData);
                if ($form->isValid()){
                    // var_dump($postData);
                    // echo "<br><br>";
                    $content = "search ".trim($postData['dns_name'])."
nameserver ".trim($postData['dns1'])."
nameserver ".trim($postData['dns2']);
                    //var_dump($content);
                    $res=file_put_contents("/etc/resolv.conf", $content);
                    if($res!=0){
                        $result = $this->getConfigTable()->saveConfigKey(array('id'=>26,'key_value'=>$postData['dns_name']));
                        $result1 = $this->getConfigTable()->saveConfigKey(array('id'=>27,'key_value'=>$postData['dns1']));
                        $result2 = $this->getConfigTable()->saveConfigKey(array('id'=>28,'key_value'=>$postData['dns2']));
                        echo "<script>alert('已成功设置并写入文件');window.location.href='/manage/Setting/configDns';</script>";
                    }else{
                        echo "<script>alert('设置失败，请检查文件读写权限并再次检查')</script>";
                    }
                    }
                else{
                    echo "<script> alert('表单数据有误！请按规范填写');window.location.href='/manage/Setting/configDns';</script>";
                }
            }
        }
        return array(
            'form' => $form,
            'uid' => $login_id,
            'dns_name' => empty($dns_info['dns_name']['key_value'])?'未设置':$dns_info['dns_name']['key_value'],
            'dns1' =>empty($dns_info['dns1']['key_value'])?'未设置':$dns_info['dns1']['key_value'],
            'dns2' => empty($dns_info['dns2']['key_value'])?'未设置':$dns_info['dns2']['key_value'],
        );
    }
    //lrn 上传logo
    public function uploadLogoAction(){
        $request = $this->getRequest();
        $public_root = __DIR__ . "/../../../../../public";
        $photo_upload_root = "/img/system_logo/";
        $new_addr = $public_root.$photo_upload_root."sys_logo.".pathinfo($_FILES['upload']['name'])['extension'];
        $logo_addr = $photo_upload_root = "/img/system_logo/"."sys_logo.".pathinfo($_FILES['upload']['name'])['extension'];
        if ($request->isPost()) {
//            var_dump($_FILES['upload']);
//            var_dump(pathinfo($_FILES['upload']['name']));
//            var_dump($new_addr);
            if (strcmp($_FILES['upload']['name'], "") != 0) {//修改了图片
                //echo "save修改了图片<br>";
                if (move_uploaded_file($_FILES['upload']['tmp_name'],$new_addr)) {
                    $result = $this->getConfigTable()->saveConfigKey(array('id'=> 21,'key_value'=>$logo_addr));
                    if($result){
                        echo "<script>alert('系统logo上传成功')</script>";
                    }
                    else{
                        echo "<script>alert('系统logo上传失败，请重试')</script>";
                    }
                } else {
                    echo "<script>alert('系统logo上传失败')</script>";
                }
            }
        }
        else{
            echo "not post";
        }
    }
    //lrn
    public function saveConfigAction(){
        $post_data = $this->getRequest()->getPost()->toArray();
        $id = (int)$post_data['id'];
        $key_value = trim($post_data['key_value']);

        //validate data
        if(!is_int($id)){
            echo json_encode(array('code'=>'1','msg'=>'配置项编号有误'));
            exit();
        }
        if(empty($key_value)){
            echo json_encode(array('code'=>'1','msg'=>'值不能为空'));
            exit();
        }
        if(strlen($key_value)>75){
            echo json_encode(array('code'=>'1','msg'=>'值过长，请勿超过75个字符'));
            exit();
        }

        //save into db
        $result = $this->getConfigTable()->saveConfigKey(array('id'=>$id,'key_value'=>$key_value));
        if($result){
            echo json_encode(array('code'=>'0','msg'=>'成功更新'));
            exit();
        }
        else{
            echo json_encode(array('code'=>'1','msg'=>'更新失败，请检查后重试'));
            exit();
        }
        exit();
    }
    /*
     * lrn
     * 备份数据库
     */
//    public function backupDatabaseAction(){
//        $filename='tmk_database'.date("Y-m-d").'-'.time();
//        $dir="tmkbackup";
//        $file_path2="mkdir tmkbackup";
//        chdir("/usr/local/www/tm/public");
//        //echo "当前路径：".getcwd() . "<br>";
//        if(is_dir($dir)){
//        }else{
//            $res=mkdir($dir,0777,true);
//        }
//        // mysqldump --all-databases new_freetest > /new/new_freetest3.sql
//        $db_name="new_freetest";
//        $name="/usr/local/www/tm/public/".$dir."/".$filename.".sql";//数据库文件存储路径
//        $exec="mysqldump --databases ".$db_name." > ".$name;
//        $result=exec($exec);
//        return array(
//            'data_addr'=>"./".$dir."/".$filename.".sql",
//            'file_name'=> $filename.".sql",
//        );
//    }
//    public function backupUserFilesAction(){
//        $filename='/tmk_usrupload_data'.date("Y-m-d").'-'.time().".tar.gz";
//        $source_dir="./img/stu/*";
//        $dir="tmkbackup";
//        $file_path2="mkdir tmkbackup";
//        chdir("/usr/local/www/tm/public");
//        // echo "当前路径：".getcwd() . "<br>";
//        if(is_dir($dir)){
//            //echo "yicunzai  mulu";
//        }else{
//            $res=mkdir($dir,0777,true);
            /*if ($res){
                echo "目录 $dir 创建成功";
            }else{
                echo "目录 $dir 创建失败";
            }*/
            //exec($file_path2);
            //exec($file_path2);
            //echo "bucunzai ";
//        }
//        $tar="tar -zcvf ./".$dir.$filename." ".$source_dir;///backup/tmk200.tar.gz /usr/local/www/tm/public/img/*"
//        //echo $tar;
//        $result=exec($tar);
//        return array(
//            'data_addr'=>"./".$dir.$filename,
//            'file_name' => $filename,
//        );
//    }

    public function backupAction(){
        return new ViewModel(

        );
    }
//lrn
    protected function getConfigTable()
    {
        if (!$this->config_table) {
            $sm = $this->getServiceLocator();
            $this->config_table = $sm->get('Manage\Model\ConfigKeyTable');
        }
        return $this->config_table;
    }
//lrn
    public function getTStuBaseTable()//获取数据库Article
    {
        if (!$this->TStuBaseTable) {
            $sm = $this->getServiceLocator();
            $this->TStuBaseTable = $sm->get('StuData\Model\TStuBaseTable');
        }
        return $this->TStuBaseTable;
    }
    public function deleteAllDataAction()
    {
        $filename1='tmk_database'.date("Y-m-d").'-'.time();
        $dir1="tmkbackup";
        $file_path2="mkdir tmkbackup";
        chdir("/usr/local/www/xly/public");
        //echo "当前路径：".getcwd() . "<br>";
        if(is_dir($dir1)){

        }
        else{
            $res=mkdir($dir1,0777,true);
        }
        // mysqldump --all-databases new_freetest > /new/new_freetest3.sql
        $db_name="student_camp";
        $name="/usr/local/www/xly/public/".$dir1."/".$filename1.".sql";//数据库文件存储路径
        $exec="mysqldump --set-gtid-purged=off --databases ".$db_name." > ".$name;
        $result=exec($exec);
//        return array(
//            'data_addr1'=>"./".$dir1."/".$filename1.".sql",
//            'file_name1'=> $filename1.".sql",
//        );
        $filename2='/tmk_usrupload_data'.date("Y-m-d").'-'.time().".tar.gz";
        $source_dir2="./img/stu/*";
        $dir2="tmkbackup";
        $file_path="mkdir tmkbackup";
        chdir("/usr/local/www/xly/public");
        // echo "当前路径：".getcwd() . "<br>";
        if(is_dir($dir2)){
            //echo "yicunzai  mulu";
        }else{
            $res=mkdir($dir2,0777,true);
            /*if ($res){
                echo "目录 $dir 创建成功";
            }else{
                echo "目录 $dir 创建失败";
            }*/
            //exec($file_path2);
            //exec($file_path2);
            //echo "bucunzai ";
        }
        $tar="tar -zcvf ./".$dir2.$filename2." ".$source_dir2;///backup/tmk200.tar.gz /usr/local/www/tm/public/img/*"
        //echo $tar;
        $result=exec($tar);
        return array(
            'data_addr1'=>"./".$dir1."/".$filename1.".sql",
            'file_name1'=> $filename1.".sql",
            'data_addr2'=>"./".$dir2.$filename2,
            'file_name2' => $filename2,
        );
    }
    public function deleteAction()
    {
        $this->getTBaseCollegeTable()->deleteAll();
        $redirect_url = "/info";
        echo "<script language='javascript'>window.location.href='".$redirect_url."';</script>";
    }
    public function backupRecoverAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            var_dump($_FILES["uploadSqlFile"]);
            $dir1 = "backupsql";
            if ($_FILES["uploadSqlFile"]["error"] > 0)
            {
                echo "错误：" . $_FILES["uploadSqlFile"]["error"] . "<br>";
            }
            $filename = date("YmdHis").rand(100,999).".sql";
            if(is_dir("/usr/local/www/xly/public/".$dir1)){

            }
            else{
                $res=mkdir("/usr/local/www/xly/public/".$dir1,0777,true);
            }
            if(is_uploaded_file($_FILES['uploadSqlFile']['tmp_name'])){
                if(!move_uploaded_file($_FILES["uploadSqlFile"]["tmp_name"], "/usr/local/www/xly/public/".$dir1."/" . $filename))
                {
                    echo "不能将文件移动到指定目录";
                }
                else{
                    echo "存储在: " . "/usr/local/www/xly/public/".$dir1."/" . $filename;
                }
            }
            else{
                echo "上传文件：".$_FILES['uploadSqlFile']['name']."不是一个合法文件";
            }
            $name="/usr/local/www/xly/public/".$dir1."/".$filename;//数据库文件存储路径
            echo $name;
            exec("mysql student_camp <".$name);
        }
        $redirect_url = "http://211.71.149.246/manage/Setting/deleteAllData";
        echo "<script language='javascript'>window.location.href='".$redirect_url."';</script>";
    }
}
