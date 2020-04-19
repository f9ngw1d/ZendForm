<?php

namespace Manage\Controller;

use Manage\Form\PersonalForm;
use Manage\Form\TimeeditForm;
use Manage\Form\TimesetForm;
use Manage\Form\UnisearchForm;
use Manage\Form\UnisetForm;
use Manage\Form\CollegeAddForm;
use Manage\Model\TBaseCollege;
use Manage\Model\TDbUniversity;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

//use Check\Form\SearchCondForm;

class SystemManagementController extends AbstractActionController
{
    protected $TDbUniversityTable;
    protected $ManageTimeTable;
    protected $TBaseCollegeTable;
    protected $UserTeacherTable;
    public function getManageTimeTable()
    {
        if (!$this->ManageTimeTable) {
            $sm = $this->getServiceLocator();
            $this->ManageTimeTable = $sm->get('Manage\Model\ManageTimeTable');
        }
        return $this->ManageTimeTable;
    }
    public function TDbUniversityTable()
    {
        if (!$this->TDbUniversityTable) {
            $sm = $this->getServiceLocator();
            $this->TDbUniversityTable = $sm->get('Manage\Model\TDbUniversityTable');
        }
        return $this->TDbUniversityTable;
    }
    public function getTBaseCollegeTable()
    {
        if (!$this->TBaseCollegeTable) {
            $sm = $this->getServiceLocator();
            $this->TBaseCollegeTable = $sm->get('Manage\Model\TBaseCollegeTable');
        }
        return $this->TBaseCollegeTable;
    }
    public function getUerTeacherTable()
    {
        if (!$this->UserTeacherTable) {
            $sm = $this->getServiceLocator();
            $this->TBaseCollegeTable = $sm->get('Manage\Model\UserTeacherTable');
        }
        return $this->UserTeacherTable;
    }//sm
    public function addUserAction(){
        $view = new ViewModel(array());
        $form = new PersonalForm();

        //角色选择
//        $roles = array(
//            '' => ''
//        );
        $request = $this->getRequest();
        if($request->isPost()){
            $account = new UsrTeacher();
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $data = $form->getData();
                if($data['Password'] == $data['Password2']){
                    $account->exchangeArray($data);
                    $insert_data = array(
                        'staff_id' => $data['Uid'],
                        'user_name'=> $data['Uname'],
                        'email'=> $data['Email'],
                        //'salt'=> 'csafarfkj',
                        'password'=> md5($data['Password']),
                        'create_at'=> date('Y-m-d H:i:s'),
                        'update_at'=> date('Y-m-d H:i:s'),
                    );
                    //用户已存在
                    if($this->getUerTeacherTable()->getUserById($insert_data['staff_id'])){
                        $view->setVariables(array('form'=>$form, 'msg'=>'用户已存在'));
                        return $view;
                    }
                    //save data
                    $res = $this->getUerTeacherTable()->saveUser2($insert_data);




                }
            }
        }
        $view = new ViewModel(array(
            'form' => $form,
        ));
        return $view;
    }//sm
    public function addCollegeAction(){
//        $login_id_container = new Container('uid');
//        $login_id = $login_id_container->item;
//        if (!isset($login_id)) {
//            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
//        }
//        $rid_container = new Container('rid');
//        $rid_arr = $rid_container->item;//login 用户的权限
//        if (!isset($rid_arr)) {
//            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
//        }
//        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
//            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
//        }
        $current_page = $this->params()->fromRoute('param1');
        if (empty($current_page)) {
            $current_page = 1;
        }
        $per_page = 2;
        $offset = ($current_page - 1) * $per_page;
        $form = new CollegeAddForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $uni = new TBaseCollege();
            $formdata  = array(
                'college_id' => $_POST['college_id'],
                'college_name' => $_POST['college_name'],
                'phone' => $_POST['phone'],
                'ip_address' => $_POST['ip_address'],
                'address' => $_POST['address']
            );
            $uni->exchangeArray($formdata);
            if($this->getTBaseCollegeTable()->saveCollege($uni))
                echo "<script>alert('保存成功')</script>";
        }
        $college= $this->getTBaseCollegeTable()->findAll($per_page, $offset);
        $total_num = $this->getTBaseCollegeTable()->getTotalnum();
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($college));
        $paginator->setCurrentPageNumber($current_page);
        $total_page = ceil($total_num / $per_page);

        $pagesInRange = array();
        for ($i = 1; $i <= $total_page; $i++) {
            $pagesInRange[] = $i;
        }
        $column = array(
            'college_id' =>'学院编号',
            'college_name'=>'学院名称',
            'phone'=>'电话',
            'ip_address'=>'网址',
            'address'=>'办公楼地址',
            'oprat'=>'操作'
        );
        $view = new ViewModel(array(
            'column' => $column,
            'college' => $college,
            'paginator' => $paginator,
            'pageCount' => $total_page,
            'pagesInRange' => $pagesInRange,
            'previous' => $current_page > 1 ? $current_page - 1 : null,
            'next' => $current_page < $total_page ? $current_page + 1 : null,
            'total_num' => $total_num,
            'current' => $current_page,
            'form' => $form,
        ));
        return $view;
    }
    public  function addTimeAction(){
//        $login_id_container = new Container('uid');
//        $login_id = $login_id_container->item;
//        if (!isset($login_id)) {
//            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
//        }
//        $rid_container = new Container('rid');
//        $rid_arr = $rid_container->item;//login 用户的权限
//        if (!isset($rid_arr)) {
//            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
//        }
//        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
//            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
//        }
        $current_page = $this->params()->fromRoute('param1');
        if (empty($current_page)) {
            $current_page = 1;
        }
        //var_dump($current_page);

        //var_dump($uni_list);
        $per_page = 2;
        $offset = ($current_page - 1) * $per_page;
        //echo $per_page."-----------------".$offset;
        $form = new TimesetForm();
        $form1 = new TimeeditForm();

        //$form->get('submit')->setValue('Add');
        // echo "request<br><br>";
        $request = $this->getRequest();
        if ($request->isPost()) {
            //echo "post<br><br>";
            //$postData = $this->getRequest()->getPost()->toArray();
            //if (strcmp($postData['submit'], '确定') == 0) {

            /// }
            $uni = new Managetime();
            $formdata  = array(
                'name' => $_POST['name'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
            );
            //var_dump($form->getData());
            $uni->exchangeArray($formdata);
            //var_dump($uni);
            if($this->getManagetimeTable()->saveTime($uni))
                echo "<script>alert('设置成功')</script>";
        }
        $time = $this->getManagetimeTable()->findAll($per_page, $offset);
        //var_dump($time);
        $timesta = array();
        foreach ($time as $tt)
        {
            foreach ($tt as $key => $value)
            {
                if($key == 'id')
                    array_push($timesta, $this->getManagetimeTable()->getTimeSta($value));
            }
        }
        //var_dump($timesta);
        $i=0;
        foreach ($time as &$tt)
        {
            $a['sta']=$timesta[$i];
            array_merge($tt,$a);
            $tt['sta'] = $timesta[$i];
            $i++;
            //echo $tt['sta'];
            //var_dump($tt);
        }
        //echo "times:";
        //var_dump($time);

        $total_num = $this->getManagetimeTable()->getTotalnum();
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($time));
        $paginator->setCurrentPageNumber($current_page);
        $total_page = ceil($total_num / $per_page);

        $pagesInRange = array();
        for ($i = 1; $i <= $total_page; $i++) {
            $pagesInRange[] = $i;
        }


        //return array('form' => $form);


        $column = array(
            'id' =>'编号',
            'name'=>'名称',
            'start_time'=>'开始',
            'end_time'=>'结束',
            'status'=>'开关',
            'description'=>'备注',
            'sta'=>'状态',
            'oprat'=>' ',
        );

        $view = new ViewModel(array(
            'column' => $column,
            'time' => $time,
            //'timsta' => $timesta,
            'paginator' => $paginator,
            'pageCount' => $total_page,
            'pagesInRange' => $pagesInRange,
            'previous' => $current_page > 1 ? $current_page - 1 : null,
            'next' => $current_page < $total_page ? $current_page + 1 : null,
            'total_num' => $total_num,
            'current' => $current_page,
            'form' => $form,
            'form1'=>$form1,

        ));
        return $view;
    }
    public function  editTimeAction(){
        $current_page = $this->params()->fromRoute('param2');
        $current_id =  $this->params()->fromRoute('param1');
        //echo $current_page;
        if(empty($current_page)){
            $current_page = 1;
        }

        $per_page = 2;
        $offset = ($current_page-1)*$per_page;
        $time = $this->getManagetimeTable()->findAll($per_page, $offset);
        $timesta = array();
        foreach ($time as $tt)
        {
            foreach ($tt as $key => $value)
            {
                if($key == 'name')
                    array_push($timesta, $this->getManagetimeTable()->getTimeSta($value));
            }
        }
        $i=0;
        foreach ($time as &$tt)
        {
            $a['sta']=$timesta[$i];
            array_merge($tt,$a);
            $tt['sta'] = $timesta[$i];
            $i++;
        }

        $total_num = $this->getManagetimeTable()->getTotalnum();
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($time));
        $paginator->setCurrentPageNumber($current_page);
        $total_page = ceil($total_num/$per_page);
        $pagesInRange = array();
        for($i=1;$i<=$total_page;$i++){
            $pagesInRange[] = $i;
        }
        $flag = 0;
        $request = $this->getRequest();

        $form = new TimesetForm();
        $form1 = new TimesetForm();

        $post = $this->getManagetimeTable()->findid($this->params('param1'));
        $form->bind($post);
        if ($request->isPost()) {
            $uni = new Managetime();
            $form->setData($request->getPost());

            if ($form->isValid()) {
                try {
                    //$uni->exchangeArray($form->getData());
                    $this->getManagetimeTable()->saveTime($post);

                    //$this->setService->savePost($post);
                    $flag = 1;
                    //echo "<script>alert('修改成功')</script>";
                    return $this->redirect()->toRoute('manage/default',array('controller'=>'SystemManagement','action'=>'addTime'));
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }

        $column = array(
            'id' =>'编号',
            'name'=>'名称',
            'start_time'=>'开始',
            'end_time'=>'结束',
            'status'=>'开关',
            'description'=>'备注',
            'sta'=>'状态',
            'oprat'=>' ',
        );

        return new ViewModel(array(
            'column'=>$column,
            'paginator'=>$paginator,
            'pageCount' =>$total_page,
            'pagesInRange' => $pagesInRange,
            'previous'=>$current_page>1?$current_page-1:null,
            'next'=>$current_page<$total_page?$current_page+1:null,
            'total_num'=>$total_num,
            'current'=>$current_page,
            'form' => $form,
            'time' => $time,
            'flag' =>$flag,
            'current_id' => $current_id,
        ));
    }
    public function  uniSearchAction(){
        //echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>";
        $current_page = $this->params()->fromRoute('param');

        if(empty($current_page)){
            $current_page = 1;
        }
        $per_page = 10;
        $offset = ($current_page-1)*$per_page;
        //echo    'current_page---------------------------->'.$current_page;
        //var_dump($current_page);
        $conArr = array();
        $uni_list = array();
        $new_param = '';
        $new_params = array();
        $condArr = $this->params()->fromRoute('param1');

        // echo    'condarr---------------------------->'.$condArr;

        if(isset($condArr))
        {
            $pieces = explode('&', $condArr);
            foreach ($pieces as &$pp)
            {
                $sub = explode('=', $pp);
                $new_param = $sub[0].'="'.$sub[1].'"';
                array_push($new_params,$new_param);
            }
            //var_dump($new_params);
            $conArr = $new_params;
            $uni_list = $this->TDbUniversityTable()->getUnibyCon($conArr,$per_page,$offset);
            //var_dump($uni_list);
        }

        $searchcolumn = array(
            'university_id'=>'学校代码',
            'university_name'=>'学校名称',
            'SSDM'=>'省代码',
            'SSDMC'=>'所在省',
            'is985'=>'是否985',
            'is211'=>'是否211',
            'freetest_qualified'=>'是否具有推免资格'
        );
        //for($i=1;$i<=8;$i++){
        //    if(isset($url_arr['param'.$i])){
        //$get_param .= "/param".$i."/".$url_arr['param'.$i];//$url_arr['param'.$i];
        //        foreach ($searchcolumn as $key => $value)
        //        array_push($conArr,$key.'="'.$value.'"');
        //  }
        //}

        //var_dump($uni_list);

        $form = new UnisearchForm();
        $form1 = new UnisetForm();

        //$form->get('submit')->setValue('Add');
        $column = array(
            'university_id'=>'学校代码',
            'university_name'=>'学校名称',
            'SSDM'=>'省代码',
            'SSDMC'=>'所在省',
            'is985'=>'是否985',
            'is211'=>'是否211',
            'freetest_qualified'=>'是否具有推免资格',
            'opra' => ' '
        );


        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();
            //var_dump($postData);
            //echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>".$postData['submit'];
            if (!isset($postData['submit'])) {
                // echo "->>>>>>>>>>>>>>>>>>>>>>>>>>>>>edit";
                $formdata  = array(
                    'university_id' => $_POST['university_id'],
                    'university_name' => $_POST['university_name'],
                    'SSDM' => $_POST['SSDM'],
                    'SSDMC' => $_POST['SSDMC'],
                    'is985' => $_POST['is985'],
                    'is211' => $_POST['is211'],
                    'freetest_qualified' => $_POST['freetest_qualified'],
                );
                //$form1->setData($request->getPost());
                $uni = new TDbUniversity();

                $uni->exchangeArray($formdata);
                //var_dump($uni);
                if($this->TDbUniversityTable()->updateUni($uni))
                    echo "<script>alert('修改成功')</script>";
                else
                    echo "<script>alert('修改失败')</script>";

            }
            else if (isset($postData['submit'])) {
                $form->setData($request->getPost());


                //var_dump($form->getData());
                //echo "post";

                if ($form->isValid()) {
                    //echo "valid";
                    //var_dump($form->getData());
                    foreach ($form->getData() as $key => $value)
                    {
                        if($value != null && $key != 'submit'){//echo $key."=".$value;
                            array_push($conArr,$key.'="'.$value.'"');}
                    }
                    //var_dump($conArr);
                    try {
                        //echo 'sear';
                        $uni_list = $this->TDbUniversityTable()->getUnibyCon($conArr,$per_page,$offset);

                        //echo "<br>unilist<br>";
                        // var_dump($uni_list);
                    } catch (\Exception $e) {
                        // 某些数据库错误发生了，记录并且让用户知道
                    }
                }
            }
        }

        //$uni_list  = $this->getUniversityTable()->getUni($per_page,$offset);
        //var_dump($uni_list);

        $total_num  = $this->TDbUniversityTable()->getConnum($conArr);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($uni_list));
        $paginator->setCurrentPageNumber($current_page);
        $total_page = ceil($total_num/$per_page);

        $pagesInRange = array();
        for($i=1;$i<=$total_page;$i++){
            $pagesInRange[] = $i;
        }


        $view = new ViewModel(array(
            'column'=>$column,
            'uni_list'=>$uni_list,
            'paginator'=>$paginator,
            'pageCount' =>$total_page,
            'pagesInRange' => $pagesInRange,
            'previous'=>$current_page>1?$current_page-1:null,
            'next'=>$current_page<$total_page?$current_page+1:null,
            'total_num'=>$total_num,
            'current'=>$current_page,
            'form'=>$form,
            'form1' =>$form1,
            'condArr' => $conArr
        ));
        return $view;
    }
    public function  uniSetAction(){
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
//        if (!isset($login_id)) {
//            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
//        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
//        if (!isset($rid_arr)) {
//            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
//        }
//        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
//            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
//        }
        $form = new UnisetForm();

        //$form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $uni = new TDbUniversity();
            //$form->setInputFilter($uni->getInputFilter());
            $formdata  = array(
                'university_id' => $_POST['university_id'],
                'university_name' => $_POST['university_name'],
                'SSDM' => $_POST['SSDM'],
                'SSDMC' => $_POST['SSDMC'],
                'is985' => $_POST['is985'],
                'is211' => $_POST['is211'],
                'freetest_qualified' => $_POST['freetest_qualified'],
            );

            $uni->exchangeArray($formdata);
            var_dump($uni->university_name);

            if($this->TDbUniversityTable()->getUnibyname($uni->university_name))
                echo "<script>alert('请勿重复添加！')</script>";
            else{
                if($this->TDbUniversityTable()->saveUniversity($uni))
                    echo "<script>alert('添加成功')</script>";
                else
                    echo "<script>alert('添加失败')</script>";
            }

            // Redirect to list of albums
            //return $this->redirect()->toRoute('uniset');

        }
        //return array('form' => $form);


        $column = array(
            'university_id'=>'学校代码',
            'university_name'=>'学校名称',
            'SSDM'=>'省代码',
            'SSDMC'=>'所在省',
            'is985'=>'是否985',
            'is211'=>'是否211',
            'freetest_qualified'=>'是否具有推免资格',
            'opra' => ' '
        );
//        var_dump($form);
//        exit();




        $view = new ViewModel(array(
            'form'=>$form,
        ));
        //不要layout
//        $view->setTerminal(true);
        //$view->addChild($tutor_cond_view,'tutor_cond_view');
        return $view;
    }
}
