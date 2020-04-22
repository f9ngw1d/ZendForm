<?php

namespace Manage\Controller;

use Manage\Form\DeleteAllDataForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;


class SettingController extends AbstractActionController
{

    protected $TStuBaseTable;

    public function __construct(){
          $rid_arr_container = new Container('rid');
          $rid_arr = $rid_arr_container->item;
          $redirect_url = "/info";
//        if(!$rid_arr || !in_array(10,$rid_arr)){
//            echo "<script language='javascript'>alert('没有访问权限');window.location.href='".$redirect_url."';</script>";
//            exit();
//        }
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
        $setting_data = $this->getConfigTable()->getConfigKey(array('school_name','system_name','current_year','short_name','DWDM','KDH','copy_right','tech_support',),false,true);
        return array(
            'setting'=>$setting_data,
            'column'=>$column,
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
    public function backupDatabaseAction(){
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
    }
    public function backupUserFilesAction(){
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
    }

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
        chdir("/usr/local/www/tm/public");
        //echo "当前路径：".getcwd() . "<br>";
        if(is_dir($dir1)){
        }else{
            $res=mkdir($dir1,0777,true);
        }
        // mysqldump --all-databases new_freetest > /new/new_freetest3.sql
        $db_name="new_freetest";
        $name="/usr/local/www/tm/public/".$dir1."/".$filename1.".sql";//数据库文件存储路径
        $exec="mysqldump --databases ".$db_name." > ".$name;
        $result=exec($exec);
//        return array(
//            'data_addr1'=>"./".$dir1."/".$filename1.".sql",
//            'file_name1'=> $filename1.".sql",
//        );
        $filename2='/tmk_usrupload_data'.date("Y-m-d").'-'.time().".tar.gz";
        $source_dir2="./img/stu/*";
        $dir2="tmkbackup";
        $file_path="mkdir tmkbackup";
        chdir("/usr/local/www/tm/public");
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
        $sql_query = "
        SELECT DISTINCT * FROM stu_base
        JOIN stu_check ON stu_base.uid=stu_check.uid";

        if ($status != "all")
            $sql_query .= " WHERE stu_check.status=".$status;

        $rowSet = $this->tableGateway->getAdapter()->query($sql_query)->execute();
        return $rowSet;
    }
}
