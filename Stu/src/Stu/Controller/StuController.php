<?php

namespace Stu\Controller;

use Stu\Form\ChangeProfessorForm;
use Stu\Form\SearchCondForm;
use Stu\Form\ChangeVolunteerForm;
use Stu\Model\StuBase;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use LosPdf\View\Model\PdfModel;
use Basicinfo\Controller\XLSXWriter;
use Basicinfo\Controller\XLSXWriter_BuffererWriter;
use Zend\Http\HeaderLoader;
use Stu\Form\CheckstatusForm;
use Stu\Model\Check;
use Stu\Model\MyBarcode;


class StuController extends AbstractActionController
{
    public $college_table;
    protected $check_table;
    protected $stu_base_table;
    protected $subject_table;
    protected $profession_table;
    protected $profession_staff_table;
    protected $staff_table;
    public  $stu_project_table;
    protected $university_table;
    protected $usr_stu_table;
    protected $status_trans_rules_table;
    public $config_table;
    protected $stu_score_table;
    protected $university_free_table;
    protected $under_subject_table;
    protected $stu_honor_table;
    protected $electronicinfoTable;
    protected $einfoTable;
    protected $projectTable;
    protected $honourTable;

    public function __construct()
    {
        // $pc = new \Basicinfo\Model\PermissionControll();
        //$pc->judgePermission();
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
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
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
        //echo $project_info."<br><br>";
        $target_professor = '';
        $target_professor .= "1、";
        if (empty($data['stu_info']['target_professor']))
            $target_professor .= '无';
        else
            $target_professor .= $data['stu_info']['target_professor'];
        $target_professor .= "   2、";
        if (empty($data['stu_info']["target_professor2"]))
            $target_professor .= "无";
        else
            $target_professor .= $data['stu_info']["target_professor2"];
        $target_professor .= "   3、";
        if (empty($data['stu_info']["target_professor3"]))
            $target_professor .= "无";
        else
            $target_professor .= $data['stu_info']["target_professor3"];

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
    <title>北京林业大学研究生复试单</title>
</head>
<style>
body{
    background:url('/img/pdflogo500.png') no-repeat;
background-position: center;
}
</style>
<body>
<div style='text-align: center;'><h2>北京林业大学推免生复试录取登记表</h2></div>
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
            <td>报考专业</td>
            <td>" .  (empty($data['stu_info']['target_subject']) ? '未填写' : $data['stu_info']['target_subject']  ). "</td>
            <td>报考方向</td>
            <td colspan='2'>" .  (empty($data['stu_info']['target_profession']) ? '无' : $data['stu_info']['target_profession']  ). "</td>
        </tr>
        <tr>
            <td>意向导师</td>
            <td colspan='2'>".($target_professor)."</td>
            <td >考生类别</td>
            <td >".(empty($data['stu_info']['apply_type'])?'未填写':$data['stu_info']['apply_type'])."</td>
            <td>是否同意调整导师（如导师名额已满，不同意者将不予录取）</td>
            <td ></td>
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
    /*
 * author:lrn
 * function:导出直博pdf
 * attention:
 */
    public function zhiboPdfAction()
    {
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        $container_login_uid = new Container('uid');
        $login_id = $container_login_uid->item;
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
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
        $content = $this->constructZhiboPdfHtml($stu_all_info);
        //echo "content<br><br><!------end of content----->";
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
       * function:绘制直博pdf的html
       * attention:
       */
    public function constructZhiboPdfHtml($data)
    {
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
        $content = "<!doctype html>
<html lang=\"zh\">
<head>
    <meta charset=\"UTF-8\">
    <title>北京林业大学2019年录取直接攻读博士生确认登记表</title>
</head>
<style>
body{
    background:url('/img/pdflogo500.png') no-repeat;
    background-position: center;
}
</style>
<body>
<div style='text-align: center;'><h2>北京林业大学2019年录取直接攻读博士生确认登记表</h2></div>
<div align='center'><img src=\"/img/barcodetmp/1.png\" style='position: center;'></div>
    <table width=\"800\" border=\"1\" align=\"center\" cellpadding=\"10\" cellspacing=\"1\" style='font-size:16px;table-layout:fixed; border-collapse: collapse;'>
        <tr>
            <td colspan=\"6\" style=\"font-weight: bold;\">1.基本信息</td>
        </tr>
        <tr>
            <td>姓名</td>
            <td colspan=''>" . (empty($data['stu_info']['user_name']) ? '无' : $data['stu_info']['user_name'] ). "</td>
            <td>性别</td>
            <td>" . (empty($data['stu_info']['gender']) ? '未填写' : $data['stu_info']['gender'] ). "</td>
            <td>身份证号</td>
            <td colspan=''>" . (empty($data['stu_info']['idcard']) ? '无' : $data['stu_info']['idcard'] ). "</td>
        </tr>
        <tr>
            <td colspan=''>录取学院</td>
            <td colspan='2'></td>
            <td colspan=''>录取导师姓名</td>
            <td colspan='2'></td>
        </tr>
        <tr>
            <td>录取学科</td>
            <td colspan='5'></td>
        </tr>
       
        <tr>
            <td colspan=\"6\" style=\"font-weight: bold;\">2.本人承诺</td>
        </tr>
        <tr>
            <td colspan=\"6\">
            本人同意在________________________学科以直接攻博方式入学攻读博士学位，并遵守北京林业大学培养管理的相关规定。
                <br><br><span style='margin-right:20px; text-align: right'>&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; 考生签字: &emsp; &emsp; &emsp; &emsp; &nbsp;</span>
                <br>&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; &emsp; &emsp; &emsp;&emsp; &emsp; &emsp; &emsp; 日期：&emsp;年&emsp;月&emsp;日  
                <br>
            </td>
        </tr>
        <tr>
            <td colspan=\"6\" style=\"font-weight: bold;\">3.导师意见（录取意见陈述不少于80字）</td>
        </tr>
        <tr>
        <td colspan='6' style='text-align: right'>
               <br><br><Br><Br><br><br><br><Br>
               <span style=''>导师签字:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
                <br>
            </td>
        </tr>
        <tr>
            <td colspan=\"6\" style=\"font-weight: bold;\">4.学科审核意见</td>
        </tr>
        <tr>
            <td colspan=\"6\" style='text-align: right'>
                <br>
                <br><br>
                <br><span style='margin-right:10%;'>学科负责人签字: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;                        </span>
                <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
                <br>
            </td>
        </tr>
   
        <tr>
            <td colspan=\"6\" style=\"font-weight: bold;\">5.学院意见</td>
        </tr>
    <tr>
        <td colspan='6' style='text-align: right'>
        <br><br><br><Br>
        <span style='margin-right:10%;'>主管院长签字: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;                        </span>
                <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
                
        </td>
    </tr>
    <tr>
        <td colspan=\"6\" style=\"font-weight: bold;\">6.研究生院意见</td>
    </tr>
    <tr>
        <td colspan='6' style='text-align: right'>
        <br><br>
        <br>
         <br>日期：&nbsp;&nbsp;&nbsp;&nbsp;年&nbsp;&nbsp;&nbsp;&nbsp;           月&nbsp;&nbsp;&nbsp;&nbsp;          日  
        </td>
    </tr>
    </table>
    注：此表由研招办留存，编号由研招办填写。
</body>
</html>
";
        //echo $content;
        return $content;
    }
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
        if (is_null($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (is_null($uid)) {//单独用户登录使用session
            $containerUid = new Container('uid');
            $uid = $containerUid->item;
        }
        else{
            if(in_array(1,$rid_arr)){//学生只可看自己的信息
                if($login_id!=$uid){
                    echo "<script> alert('非法操作，无权访问！');window.location.href='/info';</script>";
                }
            }
        }
        $stu_info = $this->getStuBaseTable()->getStu($uid);
        $stu_project_info = $this->getStuProjectTable()->getProjectByUid($uid);
        $stu_honor_info = $this->getStuHonorTable()->getHonourByUid($uid);
        $apply_type = $this->getApplyTypeArr();
        if ($stu_info != null) {
            $graduate_subject = empty($this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_profession)->name) ? '未填写' : $this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_profession)->name;
            $graduate_prof_class = empty($this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_professional_class)->name) ? '未填写' : $this->getUnderSubjectTable()->getUnderSubject($stu_info->graduate_professional_class)->name;
            $rank1=empty($stu_info->ranking)?'无':$stu_info->ranking;
            $rank2=empty($stu_info->pro_stu_num)?'无':$stu_info->pro_stu_num;
            $photo_add="/img/stu/".$uid."/einfo/1/1_1.";
            $server_root=$_SERVER['DOCUMENT_ROOT'];
            if(file_exists($server_root.$photo_add."jpg")==true){
                //echo "jpeg!";
                $photo_add_dir=$photo_add."jpg";
                //echo $photo_add_dir."<br><br>";
            }
           elseif(file_exists($server_root.$photo_add."jpeg")==true){
                //echo "jpeg!";
                $photo_add_dir=$photo_add."jpeg";
                //echo $photo_add_dir."<br><br>";
            }
            else{
               // echo "png!";
                $photo_add_dir=$photo_add."png";
                //echo $photo_add_dir."<br><br>";
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
                'target_college' => empty($this->getCollegeTable()->getCollege($stu_info->target_college)->college_name)?'无':$this->getCollegeTable()->getCollege($stu_info->target_college)->college_name,//$stu_info->target_college,
                'target_subject' => empty($this->getSubjectTable()->getSubjectsByCidSidFull($stu_info->target_college,$stu_info->target_subject, 1)->subject_name) ? '未填写' : $this->getSubjectTable()->getSubjectsByCidSidFull($stu_info->target_college,$stu_info->target_subject, 1)->subject_name,
                'target_profession' => empty($this->getProfessionTable()->getProfessionByPidSidCid($stu_info->target_profession, $stu_info->target_subject, $stu_info->target_college)->profession_name) ? '无' : $this->getProfessionTable()->getProfessionByPidSidCid($stu_info->target_profession, $stu_info->target_subject, $stu_info->target_college)->profession_name,//$stu_info->target_profession,
                'target_professor' => empty($this->getStaffTable()->getStaff($stu_info->target_professor)->staff_name) ? '无' : $this->getStaffTable()->getStaff($stu_info->target_professor)->staff_name,
                'target_professor2' => empty($this->getStaffTable()->getStaff($stu_info->target_professor2)->staff_name) ? '无' : $this->getStaffTable()->getStaff($stu_info->target_professor2)->staff_name,//$user->target_professor2,
                'target_professor3' => empty($this->getStaffTable()->getStaff($stu_info->target_professor3)->staff_name) ? '无' : $this->getStaffTable()->getStaff($stu_info->target_professor3)->staff_name,//$user->target_professor3,
            );
            //add for show things
            $project_table =  $this->getProjectTable()->getProjectByUid($uid);
            $honour_table =  $this->getStuHonorTable()->getHonourByUid($uid);
            $einfo_addr ="/img/stu/" ; //凭证存储的位置,del要访问这里
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
        //$statusArr = $this->getAllStatus();
        //  $stu_status2 = $this->getConfigTable()->getConfigValueByKey("stu_status",$now_status);
        $live_check_stu_status= $this->getConfigTable()->getConfigValueByKey("live_check_status",array(),false,true);
        //var_dump($live_check_stu_status);
        $checkstatusform = new CheckstatusForm($live_check_stu_status);
        $live_check_status_info=$this->getCheckTable()->getCheck($uid);
        $live_check_status_value=array(
            'live_check_status' => empty($live_check_status_info->live_check_status)?'0':$live_check_status_info->live_check_status,
        );
        $checkstatusform->setData($live_check_status_value);
        $request = $this->getRequest();
        if ($request->isPost()) {
            if (isset($_POST['submit2'])) {//strcmp($arrayData['submit'], '修改状态')仅院负责人 院秘书
                $postData = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray()
                );
                $checkstatusform->setData($postData);
                if ($checkstatusform->isValid()) {
                    $formdata = $checkstatusform->getInputFilter()->getValues();
                    //var_dump($formdata);
                    $data = array(
                        'uid' => $uid,
                        'live_check_status' => ($formdata['live_check_status']==$live_check_status_info->live_check_status)?12:$formdata['live_check_status'],
                        'live_check_staff' => $login_id,
                    );
                    $check_table = new Check();
                    $check_table->exchangeArray($data);
                    $result = $this->getCheckTable()->updateLiveCheckStatus($check_table);
                    echo "<script>alert('刷新查看结果')</script>";
                   /* if ($result == false) {
                        echo "<script>alert('现场确认不通过')</script>";
                    } else {
                        echo "<script>alert('点击失败')</script>";
                    }*/
                }//is_valid
                else {
                    echo "<script>alert('填写内容验证不通过')</script>";
                }
            }//submit2
            else{
                if (isset($_POST['submit3'])) {//strcmp($arrayData['submit'], 'tongguo')仅院负责人 院秘书
                    $postData = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray()
                    );
                    $checkstatusform->setData($postData);
                    if ($checkstatusform->isValid()) {
                        $formdata = $checkstatusform->getInputFilter()->getValues();
                        //var_dump($formdata);
                        $data = array(
                            'uid' => $uid,
                            'live_check_status' => 1,
                            'live_check_staff' => $login_id,
                        );
                        $check_table = new Check();
                        $check_table->exchangeArray($data);
                        $result = $this->getCheckTable()->updateLiveCheckStatus($check_table);
                        echo "<script>alert('刷新查看结果')</script>";
                        /*if ($result == false) {
                            echo "<script>alert('审核通过')</script>";
                        } else {
                            echo "<script>alert('审核通过失败')</script>";
                        }*/
                    }//is_valid
                    else {
                        echo "<script>alert('填写内容验证不通过')</script>";
                    }
                }//submit3
            }
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
            'checkstatusform' => $checkstatusform,
        );
    }
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
    /*
       * author:lrn
       * function:改志愿   改导师
       * attention:
       */
    public function changeVolunteerAction()
    {
        //$container_uid = new Container('uid');
        //$uid = $container_uid->item;
        $uid = $this->params()->fromRoute('uid'); //从路由取uid
        $rid = $this->params()->fromRoute('rid'); //从路由取uid
        $now_stage = $this->params()->fromRoute('now_stage'); //从路由取uid
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
        //var_dump($now_status);
        //echo "<br><br>";
        if ($now_status < 0) {
            echo "<script> alert('该生已被拒绝或不符合该方向具体招生条件，无法修改方向！');window.location.href='/info';</script>";
        } else {//ok  editable

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
    }
    /*
      * author:lrn
      * function:改志愿   改导师
      * attention:
      */
    public function changeVolunteer2originAction()
    {
        $container_uid = new Container('uid');
        $uid = $container_uid->item;
        if (is_null($uid)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $container_rid = new Container('rid');
        $rid_arr = $container_rid->item;
        $container_username = new Container('username');
        $username = $container_username->item;
        $status = $this->getCheckTable()->getCheck($uid);
        if($status==false){
            echo "<script> alert('未查询到您的状态，非法访问！');window.location.href='/info';</script>";
        }
        $now_status=$status->status;
        //var_dump($now_status);
        //echo "<br><br>";
        if ($now_status >= 4) {
            echo "<script> alert('您已进入审核流程，无法修改方向！');window.location.href='/info'; </script>";
        } elseif ($now_status < 0) {
            echo "<script> alert('您已被拒绝或不符合该方向具体招生条件，无法修改方向，尽快选择其他院校！');window.location.href='/info';</script>";
        } else {//ok  editable

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
                        if (((in_array(9, $rid_arr) || in_array(11, $rid_arr)) && (($now_status == 4) || ($now_status == 8))) || (in_array(1, $rid_arr))) {
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
                            $status_data = array(
                                'uid' => $uid,
                                'status' => 3,
                            );
                            $check_table = new Check();
                            $check_table->exchangeArray($status_data);
                            $result2 = $this->getCheckTable()->updateStatus($check_table);
                            echo "<script>alert('返回查看结果');window.location.href='/stu/stu/changeVolunteer';</script>";
                            //return $this->redirect()->toRoute("stu/default", array("Controller" => "Stu", "action" => "changeVolunteer"));
                        }//if  有权操作
                        else {
                            echo "<script>alert('无权操作');window.location.href='/info';</script>";
                        }
                    } else {
                        echo "<script>alert('the form is not valid！');window.location.href='/stu/stu/changeVolunteer';</script>";
                    }
                }//if   点击的是submit，提交志愿
                else {
                    //学科在录取之前都能改学生的导师
                    //修改导师，在4以前都可以
                    $postData = $this->getRequest()->getPost()->toArray();
                    $change_tutor_form->setData($postData);
                    if ($change_tutor_form->isValid()) {
                        if (($now_status < 4 && in_array(1, $rid_arr)) || ((in_array(8, $rid_arr) || in_array(12, $rid_arr)) && ($now_status < 10) && ($now_status > 0))) {//可改导师
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
                            echo "<script>alert('已更改，返回查看');window.location.href='/stu/stu/changeVolunteer';</script>";
                        } else {
                            echo "<script>alert('您已不可更改导师！');window.location.href='/stu/stu/changeVolunteer';</script>";
                        }
                        //return $this->redirect()->toRoute("/stu/stu/changeVolunteer");//"stu/default", array("Controller" => "Stu", "action" => "changeVolunteer"));

                    }//valid
                    else {
                        echo "<script>alert('the form is not valid！');window.location.href='/stu/stu/changeVolunteer';</script>";
                    }//not valid
                    //return $this->redirect()->toRoute("stu/default", array("Controller" => "Stu", "action" => "changeVolunteer"));
                }//else  点击的是submit2,修改导师
            }//if    ispost
            return array(
                'form' => $form,
                'change_tutor_form' => $change_tutor_form,
                'username' => $username,
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
    }
    /*
       * author:lrn
       * function:学生查看复试成绩
       * attention:
       */
    public function checkReexamResultAction(){
        $container_login_uid = new Container('uid');
        $login_id = $container_login_uid->item;
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        $now_status='';
        if (is_null($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        else{
            if(!in_array(1, $rid_arr)){
                echo "<script> alert('抱歉，您尚无权访问！');window.location.href='/info';</script>";
            }
            $status = $this->getCheckTable()->getCheck($login_id);
            $stu_score_arr=array();
            if($status==false){
                echo "<script> alert('非法访问！');window.location.href='/info';</script>";
            }
            if($status->status>=7){//进了录取审核，可以查看复试成绩
                $stu_score = $this->getStuScoreTable()->getbyid($login_id);
            }
            if(empty($stu_score)){
                $stu_score_arr[]="还未录入复试成绩，请耐心等待";
            }
            else{
                $stu_score_arr['基础理论']=$stu_score->course1;
                $stu_score_arr['基本技能']=$stu_score->course2;
                $stu_score_arr['外语']=$stu_score->course3;
                $stu_score_arr['综合面试']=$stu_score->course4;
                $stu_score_arr['总分']=$stu_score->total;
            }
        }
        return array(
            'stu_score' => $stu_score_arr,
        );
    }
    /*
       * author:lrn
       * function:学生查看审核进度
       * attention:
       */
    public function checkNowStatusAction(){
        $container_login_uid = new Container('uid');
        $login_id = $container_login_uid->item;
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        $now_status='';
        if (is_null($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        else{
            if(!in_array(1, $rid_arr)){
                echo "<script> alert('抱歉，您尚无权访问！');window.location.href='/info';</script>";
            }
            $status = $this->getCheckTable()->getCheck($login_id);
            if($status==false){
                echo "<script> alert('非法访问！');window.location.href='/info';</script>";
            }
            $now_status=$status->status;
            $stu_status2 = $this->getConfigTable()->getConfigValueByKey("stu_status",$now_status);
        }
        return array(
            'now_status' => $stu_status2,
        );
    }
    /*
           * author:lrn
           * function:学生确认参加复试
           * attention:
           */
    public function confirmReexamAction(){
        $container_uid = new Container('uid');
        $uid = $container_uid->item;
        if (is_null($uid)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $container_username = new Container('username');
        $username = $container_username->item;
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        $status = $this->getCheckTable()->getCheck($uid);
        if($status==false){
            echo "<script> alert('非法访问！');window.location.href='/info';</script>";
        }
        $now_status=$status->status;
        if($now_status!=5){
            echo "<script> alert('当前阶段不能确认！');window.location.href='/info';</script>";
        }
        $value=array();
        if($now_status==5&&in_array(1,$rid_arr)){
            $stu_info = $this->getStuBaseTable()->getStu($uid);
            if ($stu_info != null) {
                $value = array(
                    '您的姓名' => $stu_info->user_name,
                    '性别' => ($stu_info->gender == 1) ? '女' : '男',
                    '报考学院' => $this->getCollegeTable()->getCollege($stu_info->target_college)->college_name,//$stu_info->target_college,
                    '报考学科' => $this->getSubjectTable()->getSubjectsByCidSidFull($stu_info->target_college,$stu_info->target_subject,1)->subject_name,//$stu_info->target_subject,
                    '报考方向(可无)' => empty($this->getProfessionTable()->getProfessionByPidSidCid($stu_info->target_profession, $stu_info->target_subject, $stu_info->target_college)->profession_name) ? '无' : $this->getProfessionTable()->getProfessionByPidSidCid($stu_info->target_profession, $stu_info->target_subject, $stu_info->target_college)->profession_name,//$stu_info->target_profession,
                );
            } else {
                echo "<script> alert('未查到该生信息！');window.location.href='/info';</script>";
            }
        }
        else{
            echo "<script> alert('非法访问！');window.location.href='/info';</script>";
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $button_value = $request->getPost('del');
            if($button_value == '确认参加复试'){
                $data = array(
                    'uid' => $uid,
                    'status' => 6,
                );
                $check_table = new Check();
                $check_table->exchangeArray($data);
                $result = $this->getCheckTable()->updateStatus($check_table);
                if ($result != false) {
                    echo "<script>alert('您已确认');window.location.href='/info';</script>";
                } else {
                    echo "<script>alert('操作失败');window.location.href='/info';</script>";
                }
                //学生状态设为6
            }
            else{
                //学生状态设置为-4
                $data = array(
                    'uid' => $uid,
                    'status' => -4,
                );
                $check_table = new Check();
                $check_table->exchangeArray($data);
                $result = $this->getCheckTable()->updateStatus($check_table);
                if ($result != false) {
                    echo "<script>alert('您已拒绝');window.location.href='/info';</script>";
                } else {
                    echo "<script>alert('操作失败');window.location.href='/info';</script>";
                }
            }
            //return $this->redirect()->toRoute("export/default", array("Controller" => "exportdbf", "action" => "exportdbf"));
        }
        return array(
            'volunteer_info' =>$value,
        );
    }
    /*
       * author:lrn
       * function:根绝college选学科
       * attention:
       */
    public function selectSubByCidAction()
    {
        $cid = $this->params()->fromRoute('param3', 0);
        $subjects = $this->getSubjectTable()->getSubjectsByCid($cid);
        $target_subject = array();
        foreach ($subjects as $key => $row) {
            if (!empty($row)) {
                $sid = $row->subject_id;//专业id
                $sname = $row->subject_name;//专业名
                $target_subject[$sid] = $sname;
            }
        }
        return array('target_subject' => $target_subject, 'cid' => $cid);
    }
    /*
          * author:lrn
          * function:根据学科选方向
          * attention:
          */
    public function selectProBySidAction()
    {
        $sid = $this->params()->fromRoute('param3', 0);
        $professions = $this->getProfessionTable()->getProfessionsBySid($sid);
        $target_profession = array();
        foreach ($professions as $key => $row) {
            if (!empty($row)) {
                $pid = $row->profession_id;//方向id
                $pname = $row->profession_name;//方向名
                $target_profession[$pid] = $pname;
            }
        }
        return array('target_profession' => $target_profession, 'sid' => $sid);
    }
    /*
            * author:lrn
            * function:审核的学生条件搜索框
            * attention:
            */
    public function searchStuAction()
    {
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        //echo "loginid: ".$login_id."<br><br>";
        if (is_null($login_id)) {
            $login_id = '8004044';
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (is_null($rid_arr)) {
            $rid_arr = '9';
        }
        $now_stage = $this->params()->fromRoute('now_stage');
        $now_rid = $this->params()->fromRoute('rid'); //从路由取uid
        if (is_null($now_rid)) {//单独用户登录使用session
            $now_rid = $rid_arr;
        }
        if ($now_rid != '10') {//不是研究生院
            $college_id_container = new Container('college_id');
            $college_id = $college_id_container->item;
            if (is_null($college_id)) {
                $college_id = '004';
            }
            $college_info = $this->getCollegeTable()->getCollege($college_id);
            if (is_null($college_info)) {
                $college_info = $this->getCollegeTable()->getCollegebyStaffid($login_id);
            }
            $search_college_arr[$college_info->college_id] = $college_info->college_name;
        } else {
            $search_college_arr = $this->getCollegeTable()->getCollegesIDNameArr();
        }
        $search_status_arr = $this->getSearchStatus($now_rid, $now_stage);
        $search_cond_form = new SearchCondForm($search_college_arr, $search_status_arr);//实例化一个表单
        $request = $this->getRequest();
        if ($request->isPost()) {//用post传过来的话
            $search_cond_form->setData($request->getPost());//数据从post里面取
            if ($search_cond_form->isValid()) {//如果数据是有效的
                $filterd_values = $search_cond_form->getInputFilter()->getValues();
                //  $search_cond_form->setData($filterd_values);
                //echo "<br/><br/>filter value ：<br/>";
                //var_dump($filterd_values);
                //echo "<br>";
                //var_dump($filterd_values['status'][0]);
                $select_status = $filterd_values['status'][0];
                $cond_arr = array();
                $this->processCond($filterd_values, $cond_arr);
                //  echo "<br/><br/>condArr value ：<br/>";
                //  var_dump($cond_arr);
                //点击了不同的按钮，做不同的反馈：跳转到学生列表或生成学生列表excel
                if (isset($_POST['submit'])) {
                    $return_arr = array(
                        'action' => 'stulist',
                        // 'staff_rid' => $this->staff_rid,
                        // 'msg' => '一志愿邮箱未录入',
                        'rid' => $now_rid,
                        'now_stage' => $now_stage,
                        'select_status' => $select_status,
                        'condArr' => $cond_arr,
                    );
                    return $this->forward()->dispatch("Check/Controller/Check", $return_arr);
                }
            } else {//表单元素检验失败
                $this->formInvalidMessage($search_cond_form);
            }
        }
        return array(
            'form' => $search_cond_form,
            'rid' => $now_rid,
            'now_stage' => $now_stage,
            //    'select_status' => $select_status,
        );
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function processCond($filterd_values, &$cond_arr)
    {
        foreach ($filterd_values as $key => $value) {
            if ($key == "submit" || empty($value)) {
                continue;
            }
            if (is_array($value)) {
                $cond = $key . " in (" . implode(",", $value) . ")";
                $cond_arr[] = $cond;
            } else {
                $cond_arr[] = $key . "='" . $value . "'";
            }
        }
    }
    /*
        * author:lrn
        * function:查询表单的状态注入
        * attention:
        */
    public function getSearchStatus($now_rid, $now_stage)
    {
        $stu_status_arr = array();
        // 1 注册阶段   2  复试阶段  3 录取  -1  失败
        if ($now_stage == '2') {
            if ($now_rid == '10') {//研究生院
                $stu_status_arr = array(
                    //     '1' => '开启复试审核',
                    '2' => '所有注册学生',
                    '5' => '已通过复试资格审核学生',
                    '-3' => '未通过复试资格审核学生',
                );
            } elseif ($now_rid == '9' || $now_rid == '11') {//学院及秘书
                $stu_status_arr = array(
                    '4' => '学科已审通过学生，进行审核',
                    '5' => '审核通过名单',
                    '-3' => '未进复试名单'
                );
            } else {//方向 或学科
                $stu_status_arr = array(
                    '3' => '所有申请学生，进行审核',
                    '-1' => '未通过注册条件筛选',
                    '4' => '审核通过',
                    '-2' => '审核不通过',
                );
            }

        } elseif ($now_stage == '3') {//录取审核
            if ($now_rid == '10') {//研究生院
                $stu_status_arr = array(
                    '9' => '学院同意录取学生，进行拟录取',
                    '10' => '拟录取名单',
                    '-4' => '拟录取审核不通过，被拒学生',
                    ''
                );
            } elseif ($now_rid == '9' || $now_rid == '11') {//学院及秘书
                $stu_status_arr = array(
                    '8' => '学科已同意录取，进行学院拟录取',
                    '9' => '学院拟录取审核通过',
                    '-4' => '学院拟录取审核不通过（被拒学生）',
                    '10' => '查看已被拟录取名单'
                );
            } else {//方向 或学科
                $stu_status_arr = array(
                    '7' => '通过复试学生，进行拟录取',
                    '8' => '学科拟录取审核通过',
                    '-4' => '学科拟录取审核不通过（被拒学生）',
                    '10' => '查看已被拟录取名单'
                );
            }
        }elseif ($now_stage == '4') {//现场确认)
            $stu_status_arr = array(
            );
        }
        else {
            echo "<script>alert('当前阶段不支持审核！')</script>";
        }
        return $stu_status_arr;
    }
    /*
       * author:lrn
       * function:验证表单错误原因
       * attention:
       */
    public function formInvalidMessage($form)
    {//表单验证无效的原因报错
        $messages = $form->getMessages();
        echo "<br/>表单验证无效<br/>";
        foreach ($messages as $key => $value) {
            echo $key . ":</br>";
            foreach ($value as $key1 => $value1) {
                echo "&nbsp;" . $key1 . ":" . $value1 . "</br>";
            }
        }
    }

    /**
     * @param $filterdValues array post传入的查询条件
     * @param $condArr array() 根据post的查询条件，构建查询条件的数组
     */

    public function exportStuAction()
    {
        return new ViewModel(array(
            'albums' => "exportStuAction",
        ));
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    protected function getCollegeTable()
    {
        if (!$this->college_table) {
            $sm = $this->getServiceLocator();
            $this->college_table = $sm->get('Basicinfo\Model\CollegeTable');
        }
        return $this->college_table;
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function getCheckTable()
    {
        if (!$this->check_table) {
            $sm = $this->getServiceLocator();
            $this->check_table = $sm->get('Stu\Model\CheckTable');
        }
        return $this->check_table;
    }
    /*
     * author:lrn
     * function:
     * attention:
     */
    protected function getStuBaseTable()
    {
        if (!$this->stu_base_table) {
            $sm = $this->getServiceLocator();
            $this->stu_base_table = $sm->get('Stu\Model\StuBaseTable');
        }
        return $this->stu_base_table;
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function getSubjectTable()
    {
        if (!$this->subject_table) {
            $sm = $this->getServiceLocator();
            $this->subject_table = $sm->get('Basicinfo\Model\SubjectTable');
        }
        return $this->subject_table;
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function getProfessionTable()
    {
        if (!$this->profession_table) {
            $sm = $this->getServiceLocator();
            $this->profession_table = $sm->get('Basicinfo\Model\ProfessionTable');
        }
        return $this->profession_table;
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function getProfessionStaffTable()
    {
        if (!$this->profession_staff_table) {
            $sm = $this->getServiceLocator();
            $this->profession_staff_table = $sm->get('Basicinfo\Model\ProfessionstaffTable');
        }
        return $this->profession_staff_table;
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function getStaffTable()
    {
        if (!$this->staff_table) {
            $sm = $this->getServiceLocator();
            $this->staff_table = $sm->get('Basicinfo\Model\StaffTable');
        }
        return $this->staff_table;
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
            $this->stu_project_table = $sm->get('Stu\Model\ProjectTable');
        }
        return $this->stu_project_table;
    }
    /*
       * author:lrn
       * function:
       * attention:
       */
    public function getUniversityTable()
    {
        if (!$this->university_table) {
            $sm = $this->getServiceLocator();
            $this->university_table = $sm->get('Stu\Model\UniversityTable');
        }
        return $this->university_table;
    }
/*
           * author:lrn
           * function:
           * attention:
           */
protected function getConfigTable()
{
    if (!$this->config_table) {
        $sm = $this->getServiceLocator();
        $this->config_table = $sm->get('Setting\Model\ConfigTable');
    }
    return $this->config_table;
}
public function getStuScoreTable() {
    if (!$this->stu_score_table) {
        $sm = $this->getServiceLocator();
        $this->stu_score_table = $sm->get('Reexam\Model\StuScoreTable');
    }
    return $this->stu_score_table;
}
    public function getUniversityFreeTable()//UniversityTable
    {
        if (! $this->university_free_table) {
            $sm = $this->getServiceLocator ();
            $this->university_free_table = $sm->get ( 'Stu\Model\UniversityFreeTable' );
        }
        return $this->university_free_table;
    }
    public function getUnderSubjectTable()
    {
        if (!$this->under_subject_table) {
            $sm = $this->getServiceLocator();
            $this->under_subject_table = $sm->get('Stu\Model\UnderSubjectTable');
        }
        return $this->under_subject_table;
    }
    public function getStuHonorTable(){
        if (!$this->stu_honor_table) {
            $sm = $this->getServiceLocator();
            $this->stu_honor_table = $sm->get('Stu\Model\HonourTable');
        }
        return $this->stu_honor_table;
    }
    public function getEinfoTable() {
        if (!$this->einfoTable) {
            $um = $this->getServiceLocator();
            $this->einfoTable = $um->get('Stu\Model\EinfoTable');
        }
        return $this->einfoTable;
    }
    protected function getProjectTable()
    {
        if (!$this->projectTable) {
            $sm = $this->getServiceLocator();
            $this->projectTable = $sm->get('Stu\Model\ProjectTable');
        }
        return $this->projectTable;
    }
    public function getElectronicinfoTable() {
        if (!$this->electronicinfoTable) {
            $um = $this->getServiceLocator();
            $this->electronicinfoTable = $um->get('Stu\Model\ElectronicinfoTable');
        }
        return $this->electronicinfoTable;
    }
}

