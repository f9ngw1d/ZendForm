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

class ShowDataInfoController extends AbstractActionController
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

    public function getTStuBaseTable()//获取数据库Article
    {
        if (!$this->TStuBaseTable) {
            $sm = $this->getServiceLocator();
            $this->TStuBaseTable = $sm->get('StuData\Model\TStuBaseTable');
        }
        return $this->TStuBaseTable;
    }

    public  function showDataInfoAction()
    {
        $gender = $this->getTStuBaseTable()->getGender();
        $isTwo = $this->getTStuBaseTable()->getUni();
        $allResult = $this->getTStuBaseTable()->getAll();
        $UniRank = $this->getTStuBaseTable()->getUniRank();
        $genderCollege = $this->getTStuBaseTable()->getCollegeGender('004');
        $isTwoCollege = $this->getTStuBaseTable()->getCollegeUni('004');
        $allResultCollege = $this->getTStuBaseTable()->getCollegeAll('004');
        $UniRankCollege = $this->getTStuBaseTable()->getCollegeUniRank('004');
        $genderTeam = $this->getTStuBaseTable()->getTeamGender('025200');
        $isTwoTeam = $this->getTStuBaseTable()->getTeamUni('025200');
        $allResultTeam = $this->getTStuBaseTable()->getTeamAll('025200');
        $UniRankTeam = $this->getTStuBaseTable()->getTeamUniRank('025200');
        $column = array(
            '1' => '学校名称',
            '2' => '人数',
        );
        return array(
            'gender' => $gender,
            'isTwo' => $isTwo,
            'allResult' => $allResult,
            'UniRank' => $UniRank,
            'column' => $column,
            'genderCollege'=>$genderCollege,
            'isTwoCollege'=>$isTwoCollege,
            'allResultCollege'=>$allResultCollege,
            'UniRankCollege'=>$UniRankCollege,
            'genderTeam'=>$genderTeam,
            'isTwoTeam'=>$isTwoTeam,
            'allResultTeam'=>$allResultTeam,
            'UniRankTeam'=>$UniRankTeam,
        );
    }
}