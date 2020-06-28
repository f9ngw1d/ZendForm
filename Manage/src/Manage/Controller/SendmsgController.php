<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manage\Controller;


use Manage\Model\Msgqueue;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Manage\Form\SendmsgForm;

class SendmsgController extends AbstractActionController
{

    protected $collegeTable;
    protected $msgqueue;


    /**
     * @author  lwb
     * @brief   为了其他系统发送 邮件/短信，获取信息
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getinfoAction()
    {
        $colleges = $this->params()->fromRoute("id");
        $role = $this->params()->fromRoute("rid");
        $page = $this->params()->fromRoute("page");
//        echo "<script>alert($colleges+$role+$page)</script>";
//        var_dump($colleges+$role+$page);
//        var_dump($colleges+'  '+$role+'   '+$page);
        if ($colleges == "-1") {  // 查找所有学院
            $all = $this->getCollegeTable()->fetchAll();
            $colleges = array();
            foreach ($all as $value)
                array_push($colleges, $value->college_id);
        }
        else
            $colleges = explode(',',$colleges);

//        echo $role." ".$audit_flag."<br>";
//        print_r($colleges);
        //exit;
//        var_dump($colleges);
//        echo "<br>";
        $adapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($adapter);
        $sl = new Select();
        $org_info = array();

        // 规定分页相关参数
        $onepage_num = 25;
        if (empty($page))   $page = 1;

        switch ($role) {
            case "college": // 院科研秘书
                $sl->from('base_college')
                    ->join('base_staff', 'base_college.manager_id=base_staff.staff_id')
                    ->where(new In('base_college.college_id',$colleges));
                break;
            case "team": // 组负责人
                $sl->from(array('su' => 'base_team'))
                    ->join(array('st' => 'base_staff'), 'su.leader_id = st.staff_id')
                    ->join(array('sc' => 'base_college'), 'st.college_id = sc.college_id')
                    ->where(new In('su.college_id',$colleges));
                break;
            case "teacher": // 教师
                $sl ->from(array('st' => 'base_staff'))
                    ->join(array('sc' => 'base_college'), 'st.college_id = sc.college_id')
                    ->where(new In('st.college_id',$colleges));
                break;
            case "yjsy":    // 研究生院  这个邮箱目前写我们的
                $yjsy_data = array(
                    array(1,10),
                    array("研究生院", "无", "XXX", "", "yzbmail@bjfu.edu.cn"),
                );
                echo json_encode(array('data'=>$yjsy_data));
                header('Access-Control-Allow-Origin:*');
                exit;
                break;
            default:
                exit;
                break;
        }

        // 记录总数统计
        $sl_num = clone $sl;
        $sl_num->columns(array("recordCount" => new \Zend\Db\Sql\Expression("COUNT(*)")));
        $statement = $sql->prepareStatementForSqlObject($sl_num);
        $resultset = $statement->execute();
        $item = $resultset->current();
        $record_num = $item['recordCount'];

        // 分页取数据
        $sl_limit = clone $sl;
        $sl_limit->limit($onepage_num)->offset(($page-1)*$onepage_num);
        $statement = $sql->prepareStatementForSqlObject($sl_limit);
        $resultset = $statement->execute();
        $item = $resultset->current();
//        var_dump($item);
        // 填写返回报文和数据
        $response = $this->getResponse();
        $data = array(array($record_num,$onepage_num));// 首记录告知浏览器：记录总数，每次显示记录条数
        while($item) {
            $tmp_data = array($item['college_name'],$item['staff_id'],$item['staff_name'],$item['cellphone'],$item['email']);
            //print_r($tmp_data); echo"<br>";
            array_push($data, $tmp_data);
            $item=$resultset->next();
        }
//        echo "data<br>";
//        var_dump($data);
//        die();
//        echo "<script>alert($data)</script>";
        $response->setContent(json_encode(array('data' => $data)));
        $response->getHeaders()->addHeaders(array(
            'Access-Control-Allow-Origin'=>'*'
        ));
        return $response;
    }

    public function getCollegeTable()
    {
        if (!$this->collegeTable) {
            $sm = $this->getServiceLocator();
            $this->collegeTable = $sm->get('Manage\Model\TBaseCollegeTable');
        }
        return $this->collegeTable;
    }

    public function getMsgqueue()
    {//获取发送邮件类
        if (!$this->msgqueue) {
            $um = $this->getServiceLocator();
            $this->msgqueue = $um->get('Manage\Model\MsgqueueTable');
        }
        return $this->msgqueue;
    }
}
