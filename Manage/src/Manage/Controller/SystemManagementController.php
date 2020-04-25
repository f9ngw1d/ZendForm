<?php

namespace Manage\Controller;

use Manage\Form\CollegeEditForm;
use Manage\Form\UserSearchForm;
use Manage\Form\PersonalForm;
use Manage\Form\TimeeditForm;
use Manage\Form\TimesetForm;
use Manage\Form\UnisearchForm;
use Manage\Form\UnisetForm;
use Manage\Form\CollegeAddForm;
use Manage\Model\TBaseCollege;
use Manage\Model\TDbUniversity;
use Manage\Model\ManageTime;
use Manage\Model\UsrTeacher;
use Manage\Model\UsrRole;
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
    protected $UsrTeacherTable;
    protected $UsrRoleTable;
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
    public function getUsrTeacherTable()
    {
        if (!$this->UsrTeacherTable) {
            $sm = $this->getServiceLocator();
            $this->UsrTeacherTable = $sm->get('Manage\Model\UsrTeacherTable');
        }
        return $this->UsrTeacherTable;
    }//sm
    public function getUsrRole()
    {
        if (!$this->UsrRoleTable) {
            $sm = $this->getServiceLocator();
            $this->UsrRoleTable = $sm->get('Manage\Model\UsrRoleTable');
        }
        return $this->UsrRoleTable;
    }//sm

    public function addUserAction(){
        $view = new ViewModel(array());
        $form = new PersonalForm();

        /*从session中获取——————————————————————————————————————未完成*/
        $role = "99";//测试用例
        if($role == "99"){//角色select
            $roles = array(
                '0'=>'请选择角色',
                '9'=>'学院负责人',
                '10'=>'研究生院',
                '11'=>'院科研秘书',
                '14'=>'组长',
                '99'=>'超级管理员',
            );
        }elseif($role == "10"){
            $roles = array(
                '0'=>'请选择角色',
                '9'=>'学院负责人',
                '10'=>'研究生院',
                '11'=>'院科研秘书',
                '14'=>'组长',
            );
        }elseif($role == "14"){
            $roles = array(
                '14'=>'组长',
            );
        }else{
            $message = "您无权访问该页面";
            $view->setVariables(array('form'=>$form, 'msg'=>$message));
            return $view;
        }
        $message = '';

        //set rid option
        $form->get('Rid')->setValueOptions($roles);
        //set YXSM option
        /*
         */

        $request = $this->getRequest();
        if($request->isPost()){
            $account = new UsrTeacher();
            //$form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $data = $form->getData();
                if($data['Password'] == $data['Password2']){
                    $account->exchangeArray($data);
                    //生成salt
                    $psw = md5($data['Password']);
                    $salt = substr($psw,-1,5);
                    print_r($data['Rid']);
                    $LastUid = $this->getUsrTeacherTable()->getLastUID();
                    $insert_data = array(
                        'staff_id' => $LastUid+1,
                        'user_name'=> $data['Realname'],
                        'email'=> $data['Email'],
                        'salt'=> $salt,
                        'password'=> md5($salt.$data['Password'].$salt),
                        'create_time'=> date('Y-m-d H:i:s'),
                        'update_at'=> null,
                        'rid'=>$data['Rid'],
                    );
                    print_r($insert_data);
                    //用户已存在
                    if($this->getUsrTeacherTable()->registercheck($insert_data['email'])){
                        $view->setVariables(array('form'=>$form, 'msg'=>'用户已存在,插入失败'));
                        return $view;
                    }
                    //save
                    $res1 = $this->getUsrTeacherTable()->saveUser2($insert_data);
                    $insert_rid = new UsrRole();
                    $insert_rid->uid = $insert_data['staff_id'];
                    $insert_rid->rid = $insert_data['rid'];
                    $res2 = $this->getUsrRole()->saveUserrole($insert_rid);
                    //判断是否插入成功否则回滚
                    if($res1 && $res2){
                        echo "<script type=\"text/javascript\" >alert('新增用户成功!');</script>";
                    }else{
                        echo "<script type=\"text/javascript\" >alert('新增用户失败!');</script>";
                        if(!$res1) {//usr_teacher insert failed,删掉一行usr_user_role
                            $this->getUsrRole()->deleteLastInsert();
                        } elseif(!$res2){//usr_user_role插入失败则删掉一行usr_teacher
                            $last_insert = $this->getUsrTeacherTable()->getLastUID();
                            $this->getUsrTeacherTable()->deleteUser($last_insert);
                        }
                    }
                }
            }
        }
        $view = new ViewModel(array(
            'form' => $form,
        ));
        return $view;
    }//sm

    public function othersAction(){
        $view = new ViewModel(array());
        $form = new PersonalForm();
        $searchForm = new UserSearchForm();

        $request = $this->getRequest();
        if($request->isPost()){
            $postdata = $request->getPost();
            if($request->getPost('del','no') == 'del'){

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
        $form1 = new CollegeEditForm();
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
            'form1'=>$form1,
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
            $uni = new ManageTime();
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
            if($this->getManageTimeTable()->saveTime($uni))
                echo "<script>alert('设置成功')</script>";
        }
        $time = $this->getManageTimeTable()->findAll($per_page, $offset);
        //var_dump($time);
        $timesta = array();
        foreach ($time as $tt)
        {
            foreach ($tt as $key => $value)
            {
                if($key == 'id')
                    array_push($timesta, $this->getManageTimeTable()->getTimeSta($value));
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

        $total_num = $this->getManageTimeTable()->getTotalnum();
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
    public function  editCollegeAction(){
        $current_page = $this->params()->fromRoute('param2');
        $current_id =  $this->params()->fromRoute('param1');
        //echo $current_page;
        if(empty($current_page)){
            $current_page = 1;
        }

        $per_page = 2;
        $offset = ($current_page-1)*$per_page;
        $college= $this->getTBaseCollegeTable()->findAll($per_page, $offset);
        $total_num = $this->getTBaseCollegeTable()->getTotalnum();
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($college));
        $paginator->setCurrentPageNumber($current_page);
        $total_page = ceil($total_num/$per_page);
        $pagesInRange = array();
        for($i=1;$i<=$total_page;$i++){
            $pagesInRange[] = $i;
        }
        $flag = 0;
        $request = $this->getRequest();

        $form = new CollegeAddForm();
        $form1 = new CollegeEditForm();

        $post = $this->getTBaseCollegeTable()->getCollege($this->params('param1'));
        $form->bind($post);
        if ($request->isPost()) {
            $uni = new TBaseCollege();
            $form->setData($request->getPost());

            if ($form->isValid()) {
                try {
                    //$uni->exchangeArray($form->getData());
                    $this->getTBaseCollegeTable()->saveCollege($post);

                    //$this->setService->savePost($post);
                    $flag = 1;
                    //echo "<script>alert('修改成功')</script>";
                    return $this->redirect()->toRoute('manage/default',array('controller'=>'SystemManagement','action'=>'addCollege'));
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            }
        }

        $column = array(
            'college_id' =>'学院编号',
            'college_name'=>'学院名称',
            'phone'=>'电话',
            'ip_address'=>'网址',
            'address'=>'办公楼地址',
            'oprat'=>'操作'
        );
        return new ViewModel(array(
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
            'form1'=>$form1,
        ));
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
        $time = $this->getManageTimeTable()->findAll($per_page, $offset);
        $timesta = array();
        foreach ($time as $tt)
        {
            foreach ($tt as $key => $value)
            {
                if($key == 'name')
                    array_push($timesta, $this->getManageTimeTable()->getTimeSta($value));
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

        $total_num = $this->getManageTimeTable()->getTotalnum();
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

        $post = $this->getManageTimeTable()->findid($this->params('param1'));
        $form->bind($post);
        if ($request->isPost()) {
            $uni = new Managetime();
            $form->setData($request->getPost());

            if ($form->isValid()) {
                try {
                    //$uni->exchangeArray($form->getData());
                    $this->getManageTimeTable()->saveTime($post);

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

    public function deleteCollegeAction()
    {
        $this->getServiceLocator()->get('Manage\Model\TBaseCollegeTable')
            ->deleteCollege(
                $this->params()->fromRoute('uid') //从url中读取id
            );
        //路由名 参数
        return $this->redirect()->toRoute('manage/default');
    }
}
