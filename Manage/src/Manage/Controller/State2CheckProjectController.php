<?php

namespace Manage\Controller;

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
    public  function showAllProjectAction(){
        $column = array(
            'team_id'=>'编号',
            'team_name'=>'名称',
            'college_id'=>'申报学院编号',
            'leader_id'=>'组长',
            'start_time'=>'起始日期',
            'end_time'=>'结束日期',
            'stu_num'=>'计划人数',
            'introduction'=>'活动简介',
            'college_link'=>'链接',
        );
        $team_data = $this->getTeamTable()->getTeamKey(array('team_name','start_time','end_time','stu_num','leader_id','introduction','college_link','college_id'),false,true);
        return array(
            'setting'=>$team_data,
            'column'=>$column,
        );
    }
    public function checkProjectAction(){

    }

}
