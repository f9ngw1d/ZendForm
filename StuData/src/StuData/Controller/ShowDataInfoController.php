<?php
namespace StuData\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Select;
use Manage\model\TBaseCollegeTable;

class ShowDataInfoController extends AbstractActionController
{
    protected $TStuBaseTable;
    protected $TBaseCollegeTable;
    protected $TBaseTeamTable;
    public function __construct(){

    }

    public function getTStuBaseTable()//获取数据库Article
    {
        if (!$this->TStuBaseTable) {
            $sm = $this->getServiceLocator();
            $this->TStuBaseTable = $sm->get('StuData\Model\TStuBaseTable');
        }
        return $this->TStuBaseTable;
    }
    public function getCollegeTable()
    {
        if (!$this->TBaseCollegeTable) {
            $sm = $this->getServiceLocator();
            $this->TBaseCollegeTable = $sm->get('Manage\Model\TBaseCollegeTable');
        }
        return $this->TBaseCollegeTable;
    }
    public function getTBaseTeamTable()
    {
        if(!$this->TBaseTeamTable)
        {
            $sm = $this->getServiceLocator();
            $this->TBaseTeamTable = $sm->get('Leader\Model\TBaseTeamTable');
        }
        return $this->TBaseTeamTable;
    }

    public  function showDataInfoAction()
    {
        $rid_arr_container = new Container('rid');
        $rid_arr = $rid_arr_container->item;
        $redirect_url = "/info";
        if(!$rid_arr && !in_array(10,$rid_arr)&&!in_array(9,$rid_arr)&&!in_array(14,$rid_arr)){
            echo "<script language='javascript'>alert('没有访问权限');window.location.href='".$redirect_url."';</script>";
            exit();
        }

        if(in_array(10,$rid_arr))
        {
            $flag = 1;
            $range = "研究生院";
            $gender = $this->getTStuBaseTable()->getGender();
            $isTwo = $this->getTStuBaseTable()->getUni();
            $allResult = $this->getTStuBaseTable()->getAll();
            $UniRank = $this->getTStuBaseTable()->getUniRank();
            $column = array(
                '1' => '学校名称',
                '2' => '人数',
            );
            return array(
                'flag' => $flag,
                'range' => $range,
                'gender' => $gender,
                'isTwo' => $isTwo,
                'allResult' => $allResult,
                'UniRank' => $UniRank,
                'column' => $column,
            );
        }

        $college_id_container = new Container('college_id');
        $college_id = $college_id_container->item;

        if (!is_null($college_id) && in_array(9,$rid_arr)) {
            echo $college_id;
            $flag = 1;
            $res = $this->getCollegeTable()->getCollege($college_id);
            $range = $res['college_name'];
            $gender = $this->getTStuBaseTable()->getCollegeGender($college_id);
            $isTwo = $this->getTStuBaseTable()->getCollegeUni($college_id);
            $allResult = $this->getTStuBaseTable()->getCollegeAll($college_id);
            $UniRank = $this->getTStuBaseTable()->getCollegeUniRank($college_id);
            $column = array(
                '1' => '学校名称',
                '2' => '人数',
            );
            return array(
                'flag' => $flag,
                'range' => $range,
                'gender' => $gender,
                'isTwo' => $isTwo,
                'allResult' => $allResult,
                'UniRank' => $UniRank,
                'column' => $column,
            );
        }

        $team_id_container = new Container('team_id');
        $team_id = $team_id_container->item;
        if(!is_null($team_id)&&in_array(14,$rid_arr)){
            $flag = 2;
            $num = count($team_id);
            $gender = array();
            $isTwo = array();
            $allResult = array();
            $UniRank = array();
            $range = array();
            for($x = 0 ; $x < $num ; $x++)
            {
                $range[$x] = $this->getTBaseTeamTable()->getTeam($team_id[$x]);
                $gender[$x] = $this->getTStuBaseTable()->getTeamGender($team_id[$x]);
                $isTwo[$x] = $this->getTStuBaseTable()->getTeamUni($team_id[$x]);
                $allResult[$x] = $this->getTStuBaseTable()->getTeamAll($team_id[$x]);
                $UniRank[$x] = $this->getTStuBaseTable()->getTeamUniRank($team_id[$x]);
            }
            $column = array(
                '1' => '学校名称',
                '2' => '人数',
            );
            return array(
                'flag'=>$flag,
                'num' => $num,
                'range' => $range,
                'gender' => $gender,
                'isTwo' => $isTwo,
                'allResult' => $allResult,
                'UniRank' => $UniRank,
                'column' => $column,
            );
        }

//        $gender = $this->getTStuBaseTable()->getGender();
//        $isTwo = $this->getTStuBaseTable()->getUni();
//        $allResult = $this->getTStuBaseTable()->getAll();
//        $UniRank = $this->getTStuBaseTable()->getUniRank();
//        $genderCollege = $this->getTStuBaseTable()->getCollegeGender('004');
//        $isTwoCollege = $this->getTStuBaseTable()->getCollegeUni('004');
//        $allResultCollege = $this->getTStuBaseTable()->getCollegeAll('004');
//        $UniRankCollege = $this->getTStuBaseTable()->getCollegeUniRank('004');
//        $genderTeam = $this->getTStuBaseTable()->getTeamGender(25200);
//        $isTwoTeam = $this->getTStuBaseTable()->getTeamUni(25200);
//        $allResultTeam = $this->getTStuBaseTable()->getTeamAll(25200);
//        $UniRankTeam = $this->getTStuBaseTable()->getTeamUniRank(25200);
//        $column = array(
//            '1' => '学校名称',
//            '2' => '人数',
//        );
//        return array(
//            'gender' => $gender,
//            'isTwo' => $isTwo,
//            'allResult' => $allResult,
//            'UniRank' => $UniRank,
//            'column' => $column,
//            'genderCollege'=>$genderCollege,
//            'isTwoCollege'=>$isTwoCollege,
//            'allResultCollege'=>$allResultCollege,
//            'UniRankCollege'=>$UniRankCollege,
//            'genderTeam'=>$genderTeam,
//            'isTwoTeam'=>$isTwoTeam,
//            'allResultTeam'=>$allResultTeam,
//            'UniRankTeam'=>$UniRankTeam,
//        );
    }
}