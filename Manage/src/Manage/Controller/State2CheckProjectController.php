<?php

namespace Manage\Controller;

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
        $redirect_url = "/info";
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
        if(sizeof($team_list)!=0) {
            foreach ($team_list as &$team) {
                $team['checkbox'] = "<input type='checkbox' name='teamid[]' value='" . $team['team_id'] . "' class='team_id_check'/>";
                $team['college'] = $this->getCollegeTable()->getCollege($team['college_id'])->college_name;
                $team['leader'] = $this->getTeacherTable()->getUserById($team['leader_id'])->user_name;
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
        );
        return array(
            'college_list'=>$college_list,
            'team_list'=>$team_list,
            'column'=>$column,
        );
    }
    public function checkProjectAction(){

    }
}
