<?php

namespace Manage\Controller;

use Manage\Model\TBaseCollege;
use Mange\Form\SearchCondForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;



class State2CheckProjectController extends AbstractActionController
{
    public $team_table;
    public $college_table;
    public $teacher_table;
    public function __construct(){
        $rid_arr_container = new Container('rid');
        $rid_arr = $rid_arr_container->item;
        $redirect_url = "/manage/default";
    }
    public function getTeamTable()
    {
        if (!$this->team_table) {
            $sm = $this->getServiceLocator();
            $this->team_table = $sm->get('Leader\Model\TBaseTeamTable');
        }
        return $this->team_table;
    }
    public function getCollegeTable()
    {
        if (!$this->college_table) {
            $sm = $this->getServiceLocator();
            $this->college_table = $sm->get('Manage\Model\TBaseCollegeTable');
        }
        return $this->college_table;
    }
    public function getTeacherTable()
    {
        if (!$this->teacher_table) {
            $sm = $this->getServiceLocator();
            $this->teacher_table = $sm->get('Manage\Model\UsrTeacherTable');
        }
        return $this->teacher_table;
    }
    public  function showAllProjectAction(){
        $team_list = $this->getTeamTable()->getTeamAll();
        $college_list =$this->getTeamTable()->getCollegeAll();
        $i = -1;
        if(sizeof($team_list)!=0) {
            foreach ($team_list as &$team) {
                $i++;
                if($team['college_id'] != NULL && $team['leader_id'] != NULL)
                {
                    $team_status = "<input type='checkbox' name='teamid[]' value='" . $team['team_id'] . "' class='team_id_check'/>";
                    $team['checkbox'] = $team_status;
                    $team['college'] = $this->getCollegeTable()->getCollege($team['college_id'])->college_name;
                    $team['leader'] = $this->getTeacherTable()->getUserById($team['leader_id'])->user_name;
                    if($team['is_approved'] == 1)
                        $team['status'] = '已批准';
                    else if($team['is_approved'] == -1)
                        $team['status'] = '未通过';
                    else
                        $team['status'] = '未批准';
                }
                else
                {
                    unset($team_list[$i]);
                }
            }
        }
        $column = array(
            'checkbox' => '勾选框',
            'team_name'=>'名称',
            'start_time'=>'起始日期',
            'end_time'=>'结束日期',
            'stu_num'=>'计划人数',
            'leader'=>'组长',
            'introduction'=>'活动简介',
            'college_link'=>'链接',
            'college'=>'申报学院',
            'status'=>'当前状态',
        );
        return array(
            'college_list'=>$college_list,
            'team_list'=>$team_list,
            'column'=>$column,
        );
    }
    public function checkProjectAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post_data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray()
            );
            $post_team_ids = $post_data['teamid'];
            $submit = $_POST['submit'];
            if (isset($submit) && ($submit == '1')) {
                foreach ($post_team_ids as $per_team_id) {
                    if ($this->getTeamTable()->saveStatus($per_team_id))
                        echo "<script>alert('保存成功')</script>";
                }
            }
            else if(isset($submit) && ($submit == '12'))
            {
                foreach ($post_team_ids as $per_team_id) {
                    if ($this->getTeamTable()->saveStatus1($per_team_id))
                        echo "<script>alert('保存成功')</script>";
                }
            }
        }
        $redirect_url = '/manage/State2CheckProject/showAllProject';
        echo "<script>";
        echo "window.location.href='" . $redirect_url . "';";
        echo "</script>";
    }
}
