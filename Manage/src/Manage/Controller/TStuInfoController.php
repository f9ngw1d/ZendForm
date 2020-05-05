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
use LosPdf\View\Model\PdfModel;
use Zend\Http\HeaderLoader;
use Student\Model\MyBarcode;


class TStuInfoController extends AbstractActionController
{
    protected $stu_base_table;
    protected $stu_project_table;
    protected $stu_honor_table;
    protected $under_subject_table;
    protected $college_table;
    protected $team_table;
    protected $electronicinfo_table;
    protected $einfo_table;
    protected $config_table;
    protected $university_free_table;
    /*
     * author:lrn
     * function:查看学生个人信息
     * attention:
     */
    public function detailAction()
    {
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        $container_login_uid = new Container('uid');
        $login_id = $container_login_uid->item;
//        if (is_null($login_id)) {
//            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
//        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        $rid_arr = array('1');
//        if (is_null($uid)) {//单独用户登录使用session
//            $containerUid = new Container('uid');
//            $uid = $containerUid->item;
//        }
//        else{
//            if(in_array(1,$rid_arr)){//学生只可看自己的信息
//                if($login_id!=$uid){
//                    echo "<script> alert('非法操作，无权访问！');window.location.href='/info';</script>";
//                }
//            }
//        }
        $stu_info = $this->getStuBaseTable()->getStu($uid);
        $stu_project_info = $this->getStuProjectTable()->getProjectByUid($uid);
        $stu_honor_info = $this->getStuHonorTable()->getHonourByUid($uid);
        $apply_type = $this->getApplyTypeArr();
        if ($stu_info != null) {
            $graduate_subject = empty($this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_profession)->name) ? '未填写' : $this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_profession)->name;
            $graduate_prof_class = empty($this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_professional_class)->name) ? '未填写' : $this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_professional_class)->name;
            $rank1=empty($stu_info->ranking)?'无':$stu_info->ranking;
            $rank2=empty($stu_info->pro_stu_num)?'无':$stu_info->pro_stu_num;
            $photo_add="/img/student/".$uid."/einfo/1/1_1.";
            $server_root=$_SERVER['DOCUMENT_ROOT'];
            if(file_exists($server_root.$photo_add."jpg")==true){
                $photo_add_dir=$photo_add."jpg";
            }
            elseif(file_exists($server_root.$photo_add."jpeg")==true){
                $photo_add_dir=$photo_add."jpeg";
            }
            else{
                $photo_add_dir=$photo_add."png";
            }
            $value = array(
                'photo_add' => $photo_add_dir,//'/img/2.jpg',
                'user_name' => $stu_info->user_name,
                'idcard'=>$stu_info->idcard,
                'apply_type'=>empty($apply_type[$stu_info->apply_type]) ? '未填写' : $apply_type[$stu_info->apply_type],//$apply_type[$stu_info->apply_type],
                'gender' => ($stu_info->gender == 1) ? '女' : '男',//empty($stu_info->gender) ? '女' : '男',
                'birthday' => substr($stu_info->idcard, 6, 8),
                'graduate_university' => empty($this->getUniversityFreeTable()->getUniversityByUid($stu_info->graduate_university)->university_name)?'无':$this->getUniversityFreeTable()->getUniversityByUid($stu_info->graduate_university)->university_name,
                'graduate_college' => empty($stu_info->graduate_college)?'无':$stu_info->graduate_college,
                'graduate_subject' => $graduate_subject,//empty($stu_info->graduate_subject)?'无':$stu_info->graduate_subject,
                'graduate_professional_class' => $graduate_prof_class,//empty($stu_info->graduate_professional_class)?'无':$stu_info->graduate_professional_class,
                'value_cet4' => empty($stu_info->value_cet4)?'无':$stu_info->value_cet4,
                'value_cet6' => empty($stu_info->value_cet6)?'无':$stu_info->value_cet6,
                'gre_score' => empty($stu_info->gre_score)?'无':$stu_info->gre_score,
                'toefl_score' => empty($stu_info->toefl_score)?'无':$stu_info->toefl_score,
                'ranking' => $rank1 . '/' . $rank2,
                'target_college' => empty($this->getCollegeTable()->getCollege($stu_info->target_college)->college_name)?'无':$this->getCollegeTable()->getCollege($stu_info->target_college)->college_name,
                'target_team' => empty($this->getTeamTable()->getTeam($stu_info->target_team)->team_name) ? '未填写' : $this->getTeamTable()->getTeam($stu_info->target_team)->team_name,
              );
            //add for show things
            $project_table =  $this->getStuProjectTable()->getProjectByUid($uid);
            $honour_table =  $this->getStuHonorTable()->getHonourByUid($uid);
            $einfo_addr ="/img/student/" ; //凭证存储的位置,del要访问这里
            $einfo_list = $this->getElectronicinfoTable()->fetchAll();//取学生被要求上传的电子文件信息
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
        } else {
            echo "<script> alert('未查到该生信息！');window.location.href='/info';</script>";
        }
        return array(
            'uid' => $uid,
            'rid' => $rid_arr,
            'stu_info' => $value,
            'einfo_status' =>$einfo_status,
            'einfo_list'=>$einfo_list,
            'srcArr'=>$srcArr,
            'einfo_surfix' =>$einfomapsurfix,
            'project_table' => $project_table,
            'honour_table' => $honour_table,
            'stu_project_info' => $stu_project_info,
            'stu_honor_info'=>$stu_honor_info,
        );
    }
    /*
 * author:lrn
 * function:导出复式单pdf
 * attention:
 */
    public function stuPdfAction()
    {
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        $container_login_uid = new Container('uid');
        $login_id = $container_login_uid->item;
//        $login_id = 14;
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        $rid_arr = array('1');
        if (is_null($uid)) {//单独用户登录使用session
            $containerUid = new Container('uid');
            $uid = $containerUid->item;
        }
        else{
            if(in_array(1,$rid_arr)){//学生只可导自己的信息
                if($login_id!=$uid){
                    echo "<script> alert('非法操作，无权访问！');window.location.href='/info';</script>";
                }
            }
        }
        $stu_all_info = $this->detailAction();
        //var_dump($stu_all_info);
        $content = $this->constructStuInfoHtml($stu_all_info);
        //echo "content<br><br>";
        //echo $content;
        $render = $this->getServiceLocator()->get('ViewPdfRenderer');
        $pdf = new PdfModel();
        $render->getEngine()->WriteHTML($content);
        $pdf->setTerminal(true);
        $pdf->setOption("paperSize", "a4");
        return $pdf;
    }
    /*
       * author:lrn
       * function:绘制复式单pdf的html
       * attention:
       */
    public function constructStuInfoHtml($data)
    {

        // var_dump($data);
        $project_info = '';
        $honor_info='';
        if (empty($data['stu_project_info'])) {
            $project_info .= "无科研项目经历";
        } else {
            //var_dump($this->stu_project_info);
            for ($i = 0; $i < sizeof($data['stu_project_info']); $i++) {
                //   var_dump($data['stu_project_info'][$i]);
                $project_info .= ($i + 1) . "、《" . $data['stu_project_info'][$i]->project_name . "》&nbsp;&nbsp;项目简介：" . $data['stu_project_info'][$i]->abstract . "&nbsp;&nbsp;项目结论：" . $data['stu_project_info'][$i]->conclusion . "&nbsp;&nbsp;获奖级别：" . $data['stu_project_info'][$i]->certificate_level . "<br><br>";
            }
        }
        if (empty($data['stu_honor_info'])) {
            echo "无";
        } else {
            //var_dump($this->stu_project_info);
            for ($i = 0; $i < sizeof($data['stu_honor_info']); $i++) {
                //     echo "<tr><td>" . ($i + 1) . "、</td><td>《" . $this->stu_project_info[$i]->project_name . "》</td><td>" . $this->stu_project_info[$i]->abstract . "</td><td>" . $this->stu_project_info[$i]->conclusion . "</td><td>" . $this->stu_project_info[$i]->certificate_level . "</td></tr>";
                $honor_info .= ($i + 1) . "、《" . $data['stu_honor_info'][$i]->honour_name . "》&nbsp;&nbsp;获奖级别：" . $data['stu_honor_info'][$i]->certificate_level . "&nbsp;&nbsp;获奖时间：" . $data['stu_honor_info'][$i]->honour_at."<br><br>";
            }
        }
        $school_name = $this->getConfigTable()->getConfigKey('school_name',array(),false,true);
//        var_dump($school_name);
        $school_name = $school_name[0]['key_value'];
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        if (is_null($uid)) {//单独用户登录使用session
            $containerUid = new Container('uid');
            $uid = $containerUid->item;
        }
        /*barcode test*/
        $barcode = new MyBarcode(); //生成可以生成一维码的类
        $content = $data['stu_info']['idcard']; //用生成一维码的数据
        $img_url = "/img/barcodetmp/1.png"; //临时存放图片地址""
        $barcode->createBarcode($content,"public".$img_url);//默认有$content文字显示在下面
        //$barcode->createBarcode($content);//直接生成png
        //$barcode->createBarcode($content,"public".$img_url,false);//没有$content文字显示在下面

        // echo $project_info;
        //echo $data['stu_info']['user_name'];
        //echo $target_professor."ttttt<br><br>";
        //die();
        //数组 整体绘制表格
        $content = "<!doctype html>
<html lang=\"zh\">
<head>
    <meta charset=\"UTF-8\">
    <title>".$school_name."夏令营学生登记表</title>
</head>
<style>
body{
    background:url('/img/pdflogo500.png') no-repeat;
background-position: center;
}
</style>
<body>
<div style='text-align: center;'><h2>".$school_name."夏令营学生登记表</h2></div>
<div align='center'><img src=\"/img/barcodetmp/1.png\" style='position: center;'></div>
    <table width=\"800\" border=\"1\" align=\"center\" cellpadding=\"10\" cellspacing=\"1\" style='font-size:16px;table-layout:fixed; border-collapse: collapse;'>
        <tr>
            <td colspan=\"7\" style=\"font-weight: bold;\">1.基本信息</td>
        </tr>
        <tr>
        <td>姓名</td>
        <td>" . (empty($data['stu_info']['user_name']) ? '无' : $data['stu_info']['user_name'] ). "</td>
        <td>性别</td>
        <td>" . (empty($data['stu_info']['gender']) ? '未填写' : $data['stu_info']['gender'] ). "</td>
        <td>出生日期</td>
        <td>" . (empty($data['stu_info']['birthday']) ? '无' : $data['stu_info']['birthday'] ). "</td>
        <td rowspan=\"4\" align=\"center\" valign=\"middle\"><img src='" . (empty($data["stu_info"]["photo_add"]) ? "" : $data["stu_info"]["photo_add"]) . "' align=\"center\" height=\"180\" width=\"140\" id=\"photo\"></td>
         <tr>
            <td>本科毕业学校</td>
            <td>" . (empty($data['stu_info']['graduate_university']) ? '无' : $data['stu_info']['graduate_university'] ). "</td>
            <td>本科学院</td>
            <td>" . (empty($data['stu_info']['graduate_college']) ? '未填写' : $data['stu_info']['graduate_college'] ). "</td>
            <td>本科专业</td>
            <td>" . (empty($data['stu_info']['graduate_subject']) ? '无' : $data['stu_info']['graduate_subject'] ). "</td>
        </tr>
        <tr>
            <td>本科专业类别</td>
            <td>" . (empty($data['stu_info']['graduate_professional_class']) ? '未填写' : $data['stu_info']['graduate_professional_class'] ). "</td>
            <td>排名</td>
            <td>" . (empty($data['stu_info']['ranking']) ? '无' : $data['stu_info']['ranking'] ). "</td>
            <td>英语四级成绩</td>
            <td>" . (empty($data['stu_info']['value_cet4']) ? '无' : $data['stu_info']['value_cet4'] ). "</td>
       </tr>
        <tr>
            <td>英语六级成绩</td>
            <td>" . (empty($data['stu_info']['value_cet6']) ? '未填写' : $data['stu_info']['value_cet6'] ). "</td>
            <td >GRE成绩</td>
            <td>" . (empty($data['stu_info']['toefl_score']) ? '无' : $data['stu_info']['toefl_score'] ). "</td>
            <td>TOEFL成绩</td>
            <td>" . (empty($data['stu_info']['staff_name']) ? '无' : $data['stu_info']['staff_name'] ). "</td>
        </tr>
        <tr>
            <td colspan='7' style=\"font-weight: bold;\">2.报考志愿</td>
        </tr>
        <tr>
            <td>报考学院</td>
            <td>" . (empty($data['stu_info']['target_college']) ? '无' : $data['stu_info']['target_college'] ). "</td>
            <td colspan=\"2\">报考组</td>
            <td>" .  (empty($data['stu_info']['target_team']) ? '未填写' : $data['stu_info']['target_team']  ). "</td>
            <td >考生类别</td>
            <td >".(empty($data['stu_info']['apply_type'])?'未填写':$data['stu_info']['apply_type'])."</td>
        </tr>
        <tr>
        <td colspan=\"7\" style=\"font-weight: bold;\">3.科研经历</td>
        </tr>
        <tr>
        <td colspan=\"7\"> ".($project_info)."</td>
        </tr>
        <tr>
            <td colspan=\"7\" style=\"font-weight: bold;\">4.个人奖励</td>
        </tr>
    <tr>
        <td colspan=\"7\">".($honor_info)."</td>
    </tr>
    
        <tr>
        <td colspan=\"7\" style=\"font-weight: bold;\">5.本人承诺</td>
    </tr>
    <tr>
        <td colspan=\"7\">
        以上信息均属实。若有非属实信息，本人承担所有责任。
            <br><br><span style='margin-right:20px; text-align: right'>&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; 个人签字: &emsp; &emsp; &emsp; &emsp; &nbsp;</span>
            <br>&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; 日期：&emsp;年&emsp;月&emsp;日  
            <br>
        </td>
    </tr>
    <tr>
            <td colspan=\"7\" style=\"font-weight: bold;\">6.复试与评价（由学科填写）</td>
        </tr>
    <tr>
        <td colspan=\"7\">
            <br>
            <br><br><Br>
            <br>
              <br>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;复试组长签字:&emsp;&emsp;&emsp;&emsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日
        </td>
    </tr>
    <tr>
        <td rowspan='2'>复试成绩</td>
        <td>外语听力及口试</td>
        <td>基础知识</td>
        <td>基本技能</td>
        <td>综合面试</td>
        <td colspan='2'>总成绩（100）</td>
    </tr>
    <tr>
        <td><br></td>
        <td><br></td>
        <td><br></td>
        <td><br></td>
        <td colspan='2'><br></td>
    </tr>
    <tr>
        <td colspan='7' align='center'>加试成绩（不计入复试总成绩）</td>
    </tr>
    <tr>
        <td>科目一名称及成绩</td>
        <td colspan='3'></td>
        <td>科目二名称及成绩</td>
        <td colspan='2'></td>
    </tr>
    
    
    
    
    <tr>
        <td colspan=\"7\" style=\"font-weight: bold;\">7.学科审核意见（请勾选）</td>
    </tr>
    <tr>
        <td colspan=\"7\" style='text-align: right'>
            <br><span style='text-align: left'> 同意录取为硕士研究生___&emsp;&emsp;同意录取为直博生___&emsp; &emsp;不同意录取___ &emsp;&emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;</span>
            <br>
            <br><span style='margin-right:10%;'>学科负责人签字: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;                        </span>
            <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
            <br>
        </td>
    </tr>
    <tr>
        <td colspan=\"7\" style=\"font-weight: bold;\">8.导师意见</td>
    </tr>
    <tr>
    <td colspan='7' style='text-align: right'>
    拟录取导师对学生道德修养、科研能力、知识结构、外国语水平等的评语：&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
    <br><br><br><br><br><br><br>
            <br><span style=''>拟录取导师签字:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
            <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
            <br>
        </td>
    </tr>
    <tr>
        <td colspan=\"7\" style=\"font-weight: bold;\">9.学院意见</td>
    </tr>
    <tr>
    <td colspan='7' style='text-align: right'>
    <br>
          <br>
            <span style='text-align: left'> 同意录取为硕士研究生___&emsp;&emsp;同意录取为直博生___&emsp; &emsp;不同意录取___ &emsp;&emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;</span>
            <br>
            <br><span style='margin-right:10%;'>主管院长签字（盖章）: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;                        </span>
            <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
            <br>
    </td>
    </tr>
    </table>
</body>
</html>
";
        //echo $content;
        return $content;
    }
    //lrn
    public function getApplyTypeArr()
    {
        $stu_apply_type_arr = array(
            'putong' => '普通',
            'zhibo' => '直博',
            '2jia' => '2+2/3',
            'techangsheng' => '特长生',
            'zhijiaotuan' => '支教团',
        );
        return $stu_apply_type_arr;
    }
    //lrn
    protected function getStuBaseTable()
    {
        if (!$this->stu_base_table) {
            $sm = $this->getServiceLocator();
            $this->stu_base_table = $sm->get('StuData\Model\TStuBaseTable');
        }
        return $this->stu_base_table;
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
    public function getUnderSubjectTable()
    {
        if (!$this->under_subject_table) {
            $sm = $this->getServiceLocator();
            $this->under_subject_table = $sm->get('Manage\Model\TDbUnderSubjectTable');
        }
        return $this->under_subject_table;
    }
    //lrn
    public function getUniversityFreeTable()//UniversityTable
    {
        if (! $this->university_free_table) {
            $sm = $this->getServiceLocator ();
            $this->university_free_table = $sm->get ( 'Manage\Model\TDbUniversityTable' );
        }
        return $this->university_free_table;
    }
    //lrn
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
    //lrn
    public function getElectronicinfoTable() {
        if (!$this->electronicinfo_table) {
            $um = $this->getServiceLocator();
            $this->electronicinfo_table = $um->get('Manage\Model\ElectronicinfoTable');
        }
        return $this->electronicinfo_table;
    }
    //lrn
    public function getEinfoTable() {
        if (!$this->einfo_table) {
            $um = $this->getServiceLocator();
            $this->einfo_table = $um->get('Manage\Model\EinfoTable');
        }
        return $this->einfo_table;
    }
    //lrn
    public function getConfigTable() {
        if(!$this->config_table){
            $sm = $this->getServiceLocator();
            $this->config_table = $sm->get('Manage\Model\ConfigKeyTable');
        }
        return $this->config_table;
    }
}