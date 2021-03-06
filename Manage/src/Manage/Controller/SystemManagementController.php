<?php

namespace Manage\Controller;

use Manage\Form\CollegeEditForm;
use Manage\Form\UserSearchForm;
use Manage\Form\OthersForm;
use Manage\Form\PersonalForm;
use Manage\Form\TimeeditForm;
use Manage\Form\TimesetForm;
use Manage\Form\UnisearchForm;
use Manage\Form\UnisetForm;
use Manage\Form\CollegeAddForm;
use Manage\Model\Staff;
use Manage\Model\TBaseCollege;
use Manage\Model\TDbUniversity;
use Manage\Model\ManageTime;
use Manage\Model\UsrTeacher;
use Manage\Model\UsrRole;
use Manage\Model\RoleName;
use Manage\Model\StaffTable;
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
    protected $TDbDivisionTable;
    protected $TDbUniversityTable;
    protected $ManageTimeTable;
    protected $TBaseCollegeTable;
    protected $UsrTeacherTable;
    protected $UsrRoleTable;
    protected $RoleNameTable;
    protected $college_table;
    protected $StaffTable;

    public function getManageTimeTable()
    {
        if (!$this->ManageTimeTable) {
            $sm = $this->getServiceLocator();
            $this->ManageTimeTable = $sm->get('Manage\Model\ManageTimeTable');
        }
        return $this->ManageTimeTable;
    }
    public function getTDbDivisionTable()
    {
        if (!$this->TDbDivisionTable) {
            $sm = $this->getServiceLocator();
            $this->TDbDivisionTable = $sm->get('Manage\Model\TDbDivisionTable');
        }
        return $this->TDbDivisionTable;
    }
    public function TDbUniversityTable()
    {
        if (!$this->TDbUniversityTable) {
            $sm = $this->getServiceLocator();
            $this->TDbUniversityTable = $sm->get('Manage\Model\TDbUniversityTable');
        }
        return $this->TDbUniversityTable;
    }
    public function getStaffTable()
    {
        if (!$this->StaffTable) {
            $sm = $this->getServiceLocator();
            $this->StaffTable = $sm->get('Manage\Model\StaffTable');
        }
        return $this->StaffTable;
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
    public function getRoleNameTable()
    {
        if (!$this->RoleNameTable) {
            $sm = $this->getServiceLocator();
            $this->RoleNameTable = $sm->get('Manage\Model\RoleNameTable');
        }
        return $this->RoleNameTable;
    }//sm


//lrn sm
    public function getRolesArr($rid_arr){
        $roles = array();
        if(in_array('99',$rid_arr) || in_array('10',$rid_arr)){//角色select
            $roles = array(
                '99'=>'超级管理员',
                '10'=>'研究生院',
                '9'=>'学院负责人',
                '11'=>'院科研秘书',
                '14'=>'招生组长',
            );
        }elseif(in_array('9',$rid_arr) || in_array('11',$rid_arr)){
            $roles = array(
                '11'=>'院科研秘书',
                '14'=>'招生组长',
            );
        }else{
            echo "<script>alert('无权访问!');window.location.href='/info';</script>";
        }
        return $roles;
    }

    public function assemble($data_arr,$college,$mobile,$rid){
        $res_arr = array(
            'staff_id' => $data_arr['staff_id'],
            'real_name' => $data_arr['user_name'],
            'user_name' => $data_arr['email'],
            'college' => $college,
            'mobile' => $mobile,
            'create_time' => $data_arr['create_time'],
            'rid' => $rid,
        );
        return $res_arr;
    }//sm

    public function addUserAction(){
        $view = new ViewModel(array());
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        if (is_null($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
//            $login_id = 8004044;   //测试用
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (is_null($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
//            $rid_arr =array(9,10,11,14);  //测试
        }
        $college_id_container = new Container('college_id');
        $college_id = $college_id_container->item;
        if (is_null($college_id)) {
//            $college_id = array('004');  //信息学院，测试用，记得删
        }
        $roles_arr = $this->getRolesArr($rid_arr);
        if (!in_array(10,$rid_arr)) {//不是研究生院
            $college_info = $this->getTBaseCollegeTable()->getCollege($college_id);
            if (is_null($college_info)) {
                $college_info = $this->getTBaseCollegeTable()->getCollegebyStaffid($login_id);
            }
            $search_college_arr[$college_info->college_id] = $college_info->college_name;
        } else {
            $search_college_arr = $this->getTBaseCollegeTable()->getCollegesIDNameArr();
        }
        $form = new PersonalForm($roles_arr,$search_college_arr);

        $request = $this->getRequest();
        if($request->isPost()){
            $account = new UsrTeacher();
            //$form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $data = $form->getData();
                if($data['Password'] == $data['Password2']){
                    //生成salt
                    $psw = md5($data['Password']);
                    $salt = substr(md5(uniqid()), 1, 5);
//                    $password = md5($salt . strtoupper(trim($data['Password'])) . $salt);
                    $password = md5(strtoupper(trim($data['Password'])));
                    $LastUid = $this->getUsrTeacherTable()->getLastUID();
                    $insert_data = array(
                        'staff_id' => $LastUid+1,
                        'user_name'=> strip_tags(trim($data['Realname'])),
                        'email'=> strip_tags(trim($data['Email'])),
                        'salt'=> $salt,
                        'password'=> $password,
                        'create_time'=> date('Y-m-d H:i:s'),
                        'update_at'=> null,
                        'rid'=>$data['Rid'],
                    );
                    $account->exchangeArray($insert_data);
                    //用户已存在
                    if($this->getUsrTeacherTable()->registercheck($insert_data['email'])){
                        $view->setVariables(array('form'=>$form, 'msg'=>'用户已存在,插入失败'));
                        return $view;
                    }
                    //save
                    $res1 = $this->getUsrTeacherTable()->saveUser2($insert_data);
                    $base_staff_arr = new Staff();
                    $insert_staff_arr=array(
                        'staff_id' =>$LastUid+1,
                        'staff_name' => strip_tags(trim($data['Realname'])),
                        'college_id' => $data['YXSM'],
                        'title' => null,
                        'phone' => $data['Mobile'],
                        'cellphone' => $data['Mobile'],
                        'email' => $data['Email'],
                        'position' => null,
                    );
                    $base_staff_arr->exchangeArray($insert_staff_arr);
                    $res3 = $this->getStaffTable()->saveStaff($base_staff_arr);

                    //添加教师用户的教师权限
                    $insert_rid2 = new UsrRole();
                    $insert_rid2_arr=array(
                        'uid' => $insert_data['staff_id'],
                        'rid' => '2'
                    );
                    $insert_rid2->exchangeArray($insert_rid2_arr);
                    $res5 = $this->getUsrRole()->saveUserrole($insert_rid2);

                    //添加到base_staff表里！！！！！
                    $insert_rid = new UsrRole();
                    $insert_rid_arr=array(
                        'uid' => $insert_data['staff_id'],
                        'rid' => $insert_data['rid']
                    );
                    $insert_rid->exchangeArray($insert_rid_arr);
                    $res2 = $this->getUsrRole()->saveUserrole($insert_rid);


                    //添加base_college的manager id
                    if($insert_data['rid'] == '9'){
                        $update_basecol = $this->getTBaseCollegeTable()->find($data['YXSM']);
                        $update_basecol->manager_id = $insert_data['staff_id'];
                        $res4 = $this->getTBaseCollegeTable()->saveCollege($update_basecol);
                    }
                    //判断是否插入成功否则回滚
                    if($res1&&$res2&&$res3&&$res5){
                        echo "<script type=\"text/javascript\" >alert('新增用户成功!');</script>";
                    }else{
                        echo "<script type=\"text/javascript\" >alert('新增用户失败!');</script>";
                        if(!$res1) {//usr_teacher insert failed,删掉一行usr_user_role
                            $this->getUsrRole()->deleteLastInsert();
                        } elseif(!$res2){//usr_user_role插入失败则删掉一行usr_teacher
                            $last_insert = $this->getUsrTeacherTable()->getLastUID();
                            $this->getUsrTeacherTable()->deleteUser($last_insert);
                        }
                        elseif(!res3){
                            $last_insert = $this->getStaffTable()->getLastUID();
                            $this->getStaffTable()->deletestaff($last_insert);
                        }
                    }
                }
            }else
                echo "<script>alert('提交失败，请检查表单是否填写正确')</script>";
        }
        $view = new ViewModel(array(
            'form' => $form,
        ));
        return $view;
    }//sm

    public function othersAction(){
        //form
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        if (is_null($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
//            $login_id = 8004044;   //测试用
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (is_null($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
//            $rid_arr =array(9,10,11,14);  //测试
        }
        $college_id_container = new Container('college_id');
        $college_id = $college_id_container->item;
        if (is_null($college_id)) {
//            $college_id = array('004');  //信息学院，测试用，记得删
        }

        $roles_arr = $this->getRolesArr($rid_arr);
        if (!in_array(10,$rid_arr)) {//不是研究生院
            $college_info = $this->getTBaseCollegeTable()->getCollege($college_id);
            if (is_null($college_info)) {
                $college_info = $this->getTBaseCollegeTable()->getCollegebyStaffid($login_id);
            }
            $search_college_arr[$college_info->college_id] = $college_info->college_name;
        } else {
            $search_college_arr = $this->getTBaseCollegeTable()->getCollegesIDNameArr();
        }
        //获取可修改的角色数组(用于修改时的选择)
        $form = new PersonalForm($roles_arr,$search_college_arr);
        $form1 = new OthersForm($roles_arr,$search_college_arr);
        $current_page = $this->params()->fromRoute('param1');
        if (empty($current_page)) {
            $current_page = 1;
        }
        $per_page = 15;
        $offset = ($current_page - 1) * $per_page;

        //post
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $this->getRequest()->getPost()->toArray()
            );
            $form1->setData($postData);
            if ($form1->isValid()) {
                $data = $form1->getData();
                $staff_id = $data['Staffid'];
                $rid = $data['Rid'];
                //获取该用户所有权限记录的数组usr_arr
                $usr_rid = $this->getUsrRole()->getRidArr($staff_id);
                $usr_arr = array();
                foreach ($usr_rid as $k => $v){
                    $usr_arr[$k]['uid'] = $staff_id;
                    $usr_arr[$k]['rid'] = $v['rid'];
                }
                //删除所有权限
                if(!empty($usr_arr)){
                    $res1 = $this->getUsrRole()->deleteUsrRid($staff_id);
                }else
                    $res1 = true;
                //赋予新权限
                $insert_rid2 = new UsrRole();
                $insert_rid2->uid = $staff_id;
                $insert_rid2->rid = '2';
                $res3 = $this->getUsrRole()->saveUserrole($insert_rid2);

                $insert_rid = new UsrRole();
                $insert_rid->uid = $staff_id;
                $insert_rid->rid = $rid;
                $res2 = $this->getUsrRole()->saveUserrole($insert_rid);
                if($res1 && $res2 && $res3){
                    echo "<script>alert('修改成功')</script>";
                }elseif(!$res1 && !$res2){
                    echo "<script>alert('修改失败')</script>";
                }else{
                    echo "<script>alert('修改失败')</script>";
                    if(!$res1){//删除失败，添加成功
                        $this->getUsrRole()->deleteLastInsert();
                    }elseif(!$res2){//删除成功，添加失败
                        foreach ($usr_arr as $key => $value){
                            $this->getUsrRole()->saveUserrole($value);
                        }
                    }
                }
            }
            else
                echo "<script>alert('提交失败，请检查是否填写正确')</script>";
        }

        $usr_teacher = $this->getUsrTeacherTable()->findAll($per_page, $offset);
        $total_num = $this->getUsrTeacherTable()->getTotalnum();
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($usr_teacher));
        $paginator->setCurrentPageNumber($current_page);
        $total_page = ceil($total_num / $per_page);
        $pagesInRange = array();
        for ($i = 1; $i <= $total_page; $i++) {
            $pagesInRange[] = $i;
        }
        $column = array(
            'staff_id' =>'编号',
            'real_name'=>'姓名',
            'user_name'=>'用户名',
            'college'=>'学院',
            'mobile'=>'移动电话',
            'create_time'=>'创建时间',
            'rid'=>'角色',
            'oprat'=>' ',
        );
        $teacher = $this->getUsrTeacherTable()->findAll($per_page, $offset);
        $data_push = array();
        foreach ($teacher as $key => $value){
            $staff_id = $value['staff_id'];
            //获取教师角色（$rid）
            $usr_rid = $this->getUsrRole()->getRidArr($staff_id);
            $rid = "";
            foreach ($usr_rid as $k => $v){
                //将rid转换为name
                $ridname = $this->getRoleNameTable()->getRole($v['rid']);
                $Name = $this->getRoleNameTable()->changeRoleToArr($ridname);
                $rid = $rid." ".$Name[1]." ";
            }
            //获取院系名称（$college_name）
            $base_staff = $this->getStaffTable()->getStaff($staff_id);
            if(!$base_staff){
                $data_push[$key] = $this->assemble($value,null,null,$rid);
                continue;
            }

            //获取移动电话（$phone）
            $phone = $base_staff->phone;
            $college_id = $base_staff->college_id;
            $college = $this->getTBaseCollegeTable()->getCollege($college_id);
            if(!$college){
                $data_push[$key] = $this->assemble($value,null,$phone,$rid);
                continue;
            }
            $college_name = $college->college_name;
            $data_push[$key] = $this->assemble($value,$college_name,$phone,$rid);
        }
        $view = new ViewModel(array(
            'column' => $column,
            'teacher' => $data_push,
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
    }//sm


    public function addCollegeAction(){
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        if (!isset($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (!isset($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
        }
        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
        }
        $form = new CollegeAddForm();
        $form1 = new CollegeEditForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $this->getRequest()->getPost()->toArray()
            );
            $form->setData($postData);
            if ($form->isValid()) {
                $uni = new TBaseCollege();
                $formdata = array(
                    'college_id' => $_POST['college_id'],
                    'college_name' => $_POST['college_name'],
                    'phone' => $_POST['phone'],
                    'ip_address' => $_POST['ip_address'],
                    'address' => $_POST['address']
                );
                $uni->exchangeArray($formdata);
                if ($this->getTBaseCollegeTable()->saveCollege($uni))
                    echo "<script>alert('保存成功')</script>";
                else{
                    echo "<script>alert('操作失败')</script>";
                }

            }
            else {
                //echo "<script>alert('请仔细检查上述表单填写是否符合要求！符合要求后再次点击保存')</script>";
                $this->formInvalidMessage($form);
            }

        }

        $res= $this->getTBaseCollegeTable()->fetchAll();
        $college = iterator_to_array($res);

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
            'form' => $form,
            'form1'=>$form1,
        ));
        return $view;
    }
    public function formInvalidMessage($form)
    {//表单验证无效的原因报错
        $messages = $form->getMessages();
        $translate = $this->getCollegeColumns();
        $msgCn = $this->getMsgCn();
        echo "<script>alert('表单验证无效：";
        foreach ($messages as $key => $value) {
            echo $translate[$key] . " 有\"";
            foreach ($value as $key1 => $value1) {
                if (isset($msgCn[$key1])) {
                    echo $msgCn[$key1];
                } else {
                    echo $key1;
                }
                echo ":" . $value1 . "\" ";
            }
            echo "错误，";
        }
        echo "请检查更正后重新保存');</script>";
    }
    public function getCollegeColumns()
    {
        return array(
            'college_id' => '学院编号',
            'college_name' => '学院名称',
            'phone' => '学院电话',
            'ip_address' => '学院官网网址',
            'address' => '办公楼地址',
        );
    }
    public function getMsgCn()
    {
        return array(
            'stringLengthTooShort' => '长度不够',
            'stringLengthTooLong' => '长度过长',
            'notDigits' => '不是数字',
        );
    }
    public  function addTimeAction(){
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        if (!isset($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (!isset($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
        }
        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
        }
        $current_page = $this->params()->fromRoute('param1');
        if (empty($current_page)) {
            $current_page = 1;
        }
        $per_page = 2;
        $offset = ($current_page - 1) * $per_page;
        $form = new TimesetForm();
        $form1 = new TimeeditForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $this->getRequest()->getPost()->toArray()
            );
//                var_dump($_POST['start_time']);
//                var_dump($_POST['end_time']);
            $form->setData($postData);
            if ($form->isValid()) {
                $uni = new ManageTime();
                $formdata = array(
                    'name' => $_POST['name'],
                    'start_time' => $_POST['start_time']['year']."-".$_POST['start_time']['month']."-".$_POST['start_time']['day']." 00:00:00",
                    'end_time' => $_POST['end_time']['year']."-".$_POST['end_time']['month']."-".$_POST['end_time']['day']." 00:00:00",
                    'description' => $_POST['description'],
                    'status' => $_POST['status'],
                );
                $uni->exchangeArray($formdata);
                if ($this->getManageTimeTable()->saveTime($uni))
                    echo "<script>alert('设置成功')</script>";
                else{
                    echo "<script>alert('操作失败')</script>";
                }
            }
            else
                echo "<script>alert('设置失败，请检查是否填写正确')</script>";
        }
        $time = $this->getManageTimeTable()->findAll($per_page, $offset);
        $timesta = array();
        foreach ($time as $tt)
        {
            foreach ($tt as $key => $value)
            {
                if($key == 'id')
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
        $total_page = ceil($total_num / $per_page);

        $pagesInRange = array();
        for ($i = 1; $i <= $total_page; $i++) {
            $pagesInRange[] = $i;
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
//    public function  editCollegeAction(){
//        $res= $this->getTBaseCollegeTable()->fetchAll();
//        $college = iterator_to_array($res);
//        $request = $this->getRequest();
//
//        $form = new CollegeAddForm();
//        $form1 = new CollegeEditForm();
//
//        $post = $this->getTBaseCollegeTable()->getCollege($this->params('param1'));
//        var_dump($post);
//        $form->bind($post);
//        if ($request->isPost()) {
//            if (isset($_POST['submit'])) {//strcmp($arrayData['submit'], '保存')
//                $form->setData($post);
//                if ($form->isValid()) {
//                    try {
////                    $uni->exchangeArray($form->getData());
//                        if($this->getTBaseCollegeTable()->saveCollege($post))
//                            echo "<script>alert('修改成功')</script>";
//                        else{
//                            echo "<script>alert('修改失败')</script>";
//                        }
//                        //$this->setService->savePost($post);
//                        $flag = 1;
//
//                        return $this->redirect()->toRoute('manage/default', array('controller' => 'SystemManagement', 'action' => 'addCollege'));
//                    } catch (\Exception $e) {
//                        die($e->getMessage());
//                    }
//                }
//            }
//        }
//
//        $column = array(
//            'college_id' =>'学院编号',
//            'college_name'=>'学院名称',
//            'phone'=>'电话',
//            'ip_address'=>'网址',
//            'address'=>'办公楼地址',
//            'oprat'=>'操作'
//        );
//        return new ViewModel(array(
//            'column' => $column,
//            'college' => $college,
//            'form' => $form,
//            'form1'=>$form1,
//        ));
//    }
//    public function  editTimeAction(){
//        $current_page = $this->params()->fromRoute('param2');
//        $current_id =  $this->params()->fromRoute('param1');
//        //echo $current_page;
//        if(empty($current_page)){
//            $current_page = 1;
//        }
//
//        $per_page = 2;
//        $offset = ($current_page-1)*$per_page;
//        $time = $this->getManageTimeTable()->findAll($per_page, $offset);
//        $timesta = array();
//        foreach ($time as $tt)
//        {
//            foreach ($tt as $key => $value)
//            {
//                if($key == 'name')
//                    array_push($timesta, $this->getManageTimeTable()->getTimeSta($value));
//            }
//        }
//        $i=0;
//        foreach ($time as &$tt)
//        {
//            $a['sta']=$timesta[$i];
//            array_merge($tt,$a);
//            $tt['sta'] = $timesta[$i];
//            $i++;
//        }
//
//        $total_num = $this->getManageTimeTable()->getTotalnum();
//        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($time));
//        $paginator->setCurrentPageNumber($current_page);
//        $total_page = ceil($total_num/$per_page);
//        $pagesInRange = array();
//        for($i=1;$i<=$total_page;$i++){
//            $pagesInRange[] = $i;
//        }
//        $flag = 0;
//        $request = $this->getRequest();
//
//        $form = new TimesetForm();
//        $form1 = new TimesetForm();
//
//        $post = $this->getManageTimeTable()->findid($this->params('param1'));
//        $form->bind($post);
//        if ($request->isPost()) {
//            $uni = new Managetime();
//            $form->setData($request->getPost());
//
//            if ($form->isValid()) {
//                try {
//                    //$uni->exchangeArray($form->getData());
//                    $this->getManageTimeTable()->saveTime($post);
//
//                    //$this->setService->savePost($post);
//                    $flag = 1;
//                    //echo "<script>alert('修改成功')</script>";
//                    return $this->redirect()->toRoute('manage/default',array('controller'=>'SystemManagement','action'=>'addTime'));
//                } catch (\Exception $e) {
//                    die($e->getMessage());
//                }
//            }
//        }
//
//        $column = array(
//            'id' =>'编号',
//            'name'=>'名称',
//            'start_time'=>'开始',
//            'end_time'=>'结束',
//            'status'=>'开关',
//            'description'=>'备注',
//            'sta'=>'状态',
//            'oprat'=>' ',
//        );
//
//        return new ViewModel(array(
//            'column'=>$column,
//            'paginator'=>$paginator,
//            'pageCount' =>$total_page,
//            'pagesInRange' => $pagesInRange,
//            'previous'=>$current_page>1?$current_page-1:null,
//            'next'=>$current_page<$total_page?$current_page+1:null,
//            'total_num'=>$total_num,
//            'current'=>$current_page,
//            'form' => $form,
//            'time' => $time,
//            'flag' =>$flag,
//            'current_id' => $current_id,
//        ));
//    }
    public function  uniSearchAction(){
        $login_id_container = new Container('uid');
        $login_id = $login_id_container->item;
        if (!isset($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (!isset($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
        }
        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
        }
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
//                var_dump($pp);
                if(stripos($pp," is"))
                {
                    $new_param = $pp;
                }
                else
                {
                    $sub = explode('=', $pp);
                    $new_param = $sub[0].'="'.$sub[1].'"';
                }
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
        $res = $this->getTDbDivisionTable()->getUniCode();
        $res1 = $this->getTDbDivisionTable()->getUni();
        $UniCode = Array();
        $Uni = Array();
        foreach ($res as $x=>$value)
        {
            $result = '';
            $content = '';
            foreach ($value as $i=>$i_value)
            {
                $result .= " ".$value[$i];
                $content = $i_value;
            }
            $UniCode[$content] = $result;
        }
        foreach ($res1 as $x1=>$value1)
        {
            $result1 = '';
            foreach ($value1 as $i1=>$i_value1)
            {
                $result1 .= $value1[$i1];
            }
            $Uni[$result1] = $result1;
        }
        $form1 = new UnisetForm($UniCode,$Uni);
        $form = new UnisearchForm($UniCode,$Uni);

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
                if($_POST['is985'] == 0)
                {
                    $_POST['is985'] = '';
                }
                if($_POST['is211'] == 0)
                {
                    $_POST['is211'] = '';
                }
                if($_POST['freetest_qualified'] == 0)
                {
                    $_POST['freetest_qualified'] = '';
                }
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
//                    var_dump($form->getData());
                    foreach ($form->getData() as $key => $value)
                    {
                        $flag = 0;
//                        echo "key = ".$key." value=".$value;
                        if(($key == 'SSDM')&&($value != NULL))
                            $value = $value;
                        if(($key == 'SSDMC')&&($value != NULL))
                            $value = $value;
                        if(($key == 'is985')&&($value == "0"))
                        {
                            $flag = 1;
                            $value = '';
                        }
                        if(($key == 'is211')&&($value == "0"))
                        {
                            $flag = 1;
                            $value = '';
                        }
                        if(($key == 'freetest_qualified')&&($value == "0"))
                        {
                            $flag = 1;
                            $value = '';
                        }
                        if($value != null && $key != 'submit'){//echo $key."=".$value;
                            array_push($conArr,$key.'="'.$value.'"');
                        }
                        else if($value == null && ($key == 'is985'||$key == 'is211'||$key == 'freetest_qualified')&&$flag == 1){
                            array_push($conArr,$key.' is NULL');
                        }
                    }
//                    var_dump($conArr);
                    try {
                        //echo 'sear';
                        $uni_list = $this->TDbUniversityTable()->getUnibyCon($conArr,$per_page,$offset);

                        //echo "<br>unilist<br>";
//                         var_dump($uni_list);
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
        if (!isset($login_id)) {
            echo "<script> alert('您未登录，尚无权访问！');window.location.href='/info';</script>";
        }
        $rid_container = new Container('rid');
        $rid_arr = $rid_container->item;//login 用户的权限
        if (!isset($rid_arr)) {
            echo "<script> alert('系统中未查到您的权限，尚无权访问！');window.location.href='/info';</script>";
        }
        if (!in_array(10, $rid_arr)) {//url中取得用户角色不属于该用户的话
            echo "<script> alert('您无该项角色权限，无权访问！');window.location.href='/info';</script>";
        }
        $res = $this->getTDbDivisionTable()->getUniCode();
        $res1 = $this->getTDbDivisionTable()->getUni();
        $UniCode = Array();
        $Uni = Array();
        foreach ($res as $x=>$value)
        {
            $result = '';
            $content = '';
            foreach ($value as $i=>$i_value)
            {
                $result .= " ".$value[$i];
                $content = $i_value;
            }
            $UniCode[$content] = $result;
        }
        foreach ($res1 as $x1=>$value1)
        {
            $result1 = '';
            foreach ($value1 as $i1=>$i_value1)
            {
                $result1 .= $value1[$i1];
            }
            $Uni[$result1] = $result1;
        }
        $form = new UnisetForm($UniCode,$Uni);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $uni = new TDbUniversity();
            //$form->setInputFilter($uni->getInputFilter());
            if($_POST['is985'] == 0)
            {
                $_POST['is985'] = '';
            }
            if($_POST['is211'] == 0)
            {
                $_POST['is211'] = '';
            }
            if($_POST['freetest_qualified'] == 0)
            {
                $_POST['freetest_qualified'] = '';
            }
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
//            var_dump($uni->university_name);

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

    public function deleteUserAction(){
        //从url中读出staff id
        $staff_id = $this->params()->fromRoute('uid');
        //删权限
        $uid_rid = $this->getUsrRole()->getRidArr($staff_id);
        $usr_arr = array();
        foreach ($uid_rid as $k => $v){
            $usr_arr[$k]['uid'] = $staff_id;
            $usr_arr[$k]['rid'] = $v['rid'];
        }
        //删除所有权限
        if(!empty($usr_arr)){
            $res1 = $this->getUsrRole()->deleteUsrRid($staff_id);
        }else
            $res1 = false;

        //删除teacher表记录
        $res2 = $this->getUsrTeacherTable()->deleteUser($staff_id);
        if($res1 && $res2){
            echo "<script>alert('删除成功')</script>";
        }else{
            if(!$res1){//权限删除失败
                echo "<script>alert('权限删除失败')</script>";
            }elseif(!$res2){//teacher注册表删除失败
                echo "<script>alert('teacher表删除失败')</script>";
            }
        }

        //路由名 参数
        return $this->redirect()->toRoute('manage/default', array('controller' => 'SystemManagement', 'action' => 'others'));
    }//sm

    public function deleteCollegeAction()
    {
        $this->getServiceLocator()->get('Manage\Model\TBaseCollegeTable')
            ->deleteCollege(
                $this->params()->fromRoute('uid') //从url中读取id
            );
        //路由名 参数
        return $this->redirect()->toRoute('manage/default', array('controller' => 'SystemManagement', 'action' => 'addCollege'));
    }
}
