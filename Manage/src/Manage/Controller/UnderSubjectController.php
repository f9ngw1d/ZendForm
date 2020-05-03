<?php
namespace Manage\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Manage\Model\TDbUnderSubject;
use Manage\Model\TDbUnderSubjectTable;
use Manage\Form\UnderSubjectSearchForm;
use Manage\Form\UnderSubjectForm;


class UnderSubjectController extends AbstractActionController {
    protected $UnderSubjectTable;

    public function indexAction() {
        $rid_arr_container = new Container('rid');
        $rid_arr = $rid_arr_container->item;
        if(in_array('10',$rid_arr)) {
            $form1 = new UnderSubjectSearchForm();
            $form2 = new UnderSubjectForm();
            $column = $this->params()->fromQuery('column', NULL);
            $parame = $this->params()->fromQuery('parame', NULL);
            // grab the paginator from the AlbumTable
            $paginator = $this->getUnderSubjectTable()->fetchAll(true, $column, $parame);
            $data = $this->getSubjectName();

            // set the current page to what has been passed in query string, or to 1 if none set
            $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
            // set the number of items per page to 10
            $paginator->setItemCountPerPage(10);

            $request = $this->getRequest();
            if ($request->isPost()) {
                $arrayData = $request->getPost()->toArray();

                $form2->setData($arrayData);
                if ($form2->isValid()) {
                    $new_under_subject = $form2->getData();
                    if ($new_under_subject['relation1'] == NULL) {
                        $new_under_subject['relation1'] = "";
                    }
                    if ($new_under_subject['relation2'] == NULL) {
                        $new_under_subject['relation2'] = "";
                    }
                    $result = array(
                        'id' => $new_under_subject['id'],
                        'name' => $new_under_subject['name'],
                        'relation1' => $new_under_subject['relation1'],
                        'relation2' => $new_under_subject['relation2'],
                    );
                    $res = $this->getUnderSubjectTable()->saveUnderSubject($result);
                    echo "<script>alert('新专业已新增至数据库');</script>";
                } else {//表单元素检验失败
                    echo "<script type=\"text/javascript\">alert('填写信息有误，请重新输入!');</script>";

                }
            }

            return array(
                'form1' => $form1,
                'form2' => $form2,
                'data' => $data,
                'paginator' => $paginator,
                'column' => $column,
                'parame' => $parame,
            );
        }else{
            echo "<script>alert('您没有查看修改本科专业目录的权限');window.location.href='/info';</script>";
        }
    }

    public function deleteAction(){
        $rid_arr_container = new Container('rid');
        $rid_arr = $rid_arr_container->item;
        if(in_array('10',$rid_arr)) {
            $id = (int)$this->params()->fromRoute('param', NULL);

            if (!$id) {
                return $this->redirect()->toRoute('manage/default', array('controller' => 'UnderSubject', 'action' => 'index'));
            }
            if ($this->getUnderSubjectTable()->deleteUnderSubject($id))
                echo "<script type=\"text/javascript\" >alert('删除成功');</script>";
            else
                echo "<script type=\"text/javascript\" >alert('删除失败');</script>";

            return $this->redirect()->toRoute('manage/default', array('controller' => 'UnderSubject', 'action' => 'index'));
        }else{
            echo "<script>alert('您没有删除本科专业目录的权限');window.location.href='/info';</script>";
        }
    }


    public function getSubjectName(){
        $subject = $this->getUnderSubjectTable();
        $subject_info = $subject->fetchAll(false)->toArray();
        $data=array();
        foreach ($subject_info as $value){
            $data[$value['id']]=array(
                'name'=>$value['name'],
                'relation1'=>$value['relation1'],
                'relation2'=>$value['relation2'],
            );
        }
        return $data;
    }

    public function getUnderSubjectTable()
    {
        if (!$this->UnderSubjectTable) {
            $sm = $this->getServiceLocator();
            $this->UnderSubjectTable = $sm->get('Manage\Model\TDbUnderSubjectTable');
        }
        return $this->UnderSubjectTable;
    }


}