<?php
namespace Manage;

use Manage\Model\ConfigKey;
use Manage\Model\ConfigKeyTable;
use Manage\Model\ConfigValue;
use Manage\Model\ConfigValueTable;
use Manage\Model\ManageTime;
use Manage\Model\ManageTimeTable;
use Manage\Model\Msgqueue;
use Manage\Model\MsgqueueTable;
use Manage\Model\RoleName;
use Manage\Model\RoleNameTable;
use Manage\Model\Rolepermission;
use Manage\Model\RolepermissionTable;
use Manage\Model\TBaseCollege;
use Manage\Model\TBaseCollegeTable;
use Manage\Model\TDbDivision;
use Manage\Model\TDbDivisionTable;
use Manage\Model\TDbUniversity;
use Manage\Model\TDbUniversityTable;
use Manage\Model\EinfoTable;
use Manage\Model\Einfo;

use Manage\Model\TDbUnderSubject;
use Manage\Model\TDbUnderSubjectTable;

use Manage\Model\UsrPermission;
use Manage\Model\UsrPermissionTable;
use Manage\Model\UsrRole;
use Manage\Model\UsrRoleTable;
use Manage\Model\UsrTeacher;
use Manage\Model\UsrTeacherTable;
use Manage\Model\MTBaseTeam;
use Manage\Model\MTBaseTeamTable;
use Manage\Model\Permission;
use Manage\Model\PermissionTable;
use Manage\Model\Staff;
use Manage\Model\StaffTable;

use Manage\Model\Honour;
use Manage\Model\HonourTable;
use Manage\Model\Project;
use Manage\Model\ProjectTable;
use Manage\Model\Infovalidatemail;
use Manage\Model\InfovalidatemailTable;
use Manage\Model\UsrStu;
use Manage\Model\UsrStuTable;
use Manage\Model\Electronicinfo;
use Manage\Model\ElectronicinfoTable;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;

class Module{
    public function onBootstrap(MvcEvent $e){
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig(){
        return array(
            'factories' => array(
                'Manage\Model\ConfigKeyTable' => function($sm){
                    $tableGateway = $sm->get('ConfigKeyTableGateway');
                    $table = new ConfigKeyTable($tableGateway);
                    return $table;
                },
                'ConfigKeyTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ConfigKey());
                    return new TableGateway('config_key',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\ConfigValue' => function($sm){
                    $tableGateway = $sm->get('ConfigValueTableGateway');
                    $table = new ConfigValueTable($tableGateway);
                    return $table;
                },
                'ConfigValueTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ConfigValue());
                    return new TableGateway('config_value',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\ManageTimeTable' => function($sm){
                    $tableGateway = $sm->get('ManageTimeTableGateway');
                    $table = new ManageTimeTable($tableGateway);
                    return $table;
                },
                'ManageTimeTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ManageTime());
                    return new TableGateway('manage_time',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\RolepermissionTable' => function($sm){
                    $tableGateway = $sm->get('RolepermissionTableGateway');
                    $table = new RolepermissionTable($tableGateway);
                    return $table;
                },
                'RolepermissionTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Rolepermission());
                    return new TableGateway('usr_role_permission',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\TBaseCollegeTable' => function($sm){
                    $tableGateway = $sm->get('TBaseCollegeTableGateway');
                    $table = new TBaseCollegeTable($tableGateway);
                    return $table;
                },
                'TBaseCollegeTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TBaseCollege());
                    return new TableGateway('base_college',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\TDbDivisionTable' => function($sm){
                    $tableGateway = $sm->get('TDbDivisionTableGateway');
                    $table = new TDbDivisionTable($tableGateway);
                    return $table;
                },
                'TDbDivisionTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TDbDivision());
                    return new TableGateway('db_administrative_division',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\TDbUnderSubjectTable' => function($sm){
                    $tableGateway = $sm->get('TDbUnderSubjectGateway');
                    $table = new TDbUnderSubjectTable($tableGateway);
                    return $table;
                },
                'TDbUnderSubjectGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TDbUnderSubject());
                    return new TableGateway('db_under_subject',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\TDbUniversityTable' => function($sm){
                    $tableGateway = $sm->get('TDbUniversityTableGateway');
                    $table = new TDbUniversityTable($tableGateway);
                    return $table;
                },
                'TDbUniversityTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TDbUniversity());
                    return new TableGateway('db_university_free',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\UsrPermissionTable' => function($sm){
                    $tableGateway = $sm->get('UsrPermissionTableGateway');
                    $table = new UsrPermissionTable($tableGateway);
                    return $table;
                },
                'UsrPermissionTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UsrPermission());
                    return new TableGateway('usr_permission',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\RoleNameTable' => function($sm){
                    $tableGateway = $sm->get('RoleNameTableGateway');
                    $table = new RoleNameTable($tableGateway);
                    return $table;
                },
                'RoleNameTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RoleName());
                    return new TableGateway('usr_role',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\UsrRoleTable' => function($sm){
                    $tableGateway = $sm->get('UsrRoleTableGateway');
                    $table = new UsrRoleTable($tableGateway);
                    return $table;
                },
                'UsrRoleTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UsrRole());
                    return new TableGateway('usr_user_role',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\UsrTeacherTable' => function($sm){
                    $tableGateway = $sm->get('UsrTeacherTableGateway');
                    $table = new UsrTeacherTable($tableGateway);
                    return $table;
                },
                'UsrTeacherTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UsrTeacher());
                    return new TableGateway('usr_teacher',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\PermissionTable' => function($sm){
                    $tableGateway = $sm->get('PermissionTableGateway');
                    $table = new PermissionTable($tableGateway);
                    return $table;
                },
                'PermissionTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Permission());
                    return new TableGateway('usr_permission',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\StaffTable' => function($sm){
                    $tableGateway = $sm->get('StaffTableGateway');
                    $table = new StaffTable($tableGateway);
                    return $table;
                },
                'StaffTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Staff());
                    return new TableGateway('base_staff',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\EinfoTable' => function($sm){
                    $tableGateway = $sm->get('EinfoTableGateway');
                    $table = new EinfoTable($tableGateway);
                    return $table;
                },
                'EinfoTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Einfo());
                    return new TableGateway('stu_einfo_map',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\HonourTable' => function($sm){
                    $tableGateway = $sm->get('HonourTableGateway');
                    $table = new HonourTable($tableGateway);
                    return $table;
                },
                'HonourTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Honour());
                    return new TableGateway('stu_honour',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\ProjectTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProjectTableGateway');
                    $table = new ProjectTable($tableGateway);
                    return $table;
                },
                'ProjectTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Project());
                    return new TableGateway('stu_project', $dbAdapter, null, $resultSetPrototype);
                },
                'Manage\Model\InfovalidatemailTable' => function($sm){
                    $tableGateway = $sm->get('InfovalidatemailTableGateway');
                    $table = new InfovalidatemailTable($tableGateway);
                    return $table;
                },
                'InfovalidatemailTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Infovalidatemail());
                    return new TableGateway('info_validatemail',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\UsrStuTable' => function($sm){
                    $tableGateway = $sm->get('UsrStuTableGateway');
                    $table = new UsrStuTable($tableGateway);
                    return $table;
                },
                'UsrStuTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UsrStu());
                    return new TableGateway('usr_stu',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\ElectronicinfoTable' => function($sm){
                    $tableGateway = $sm->get('ElectronicinfoTableGateway');
                    $table = new ElectronicinfoTable($tableGateway);
                    return $table;
                },
                'ElectronicinfoTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Electronicinfo());
                    return new TableGateway('stu_electronic_info',$dbAdapter,null,$resultSetPrototype);
                },
                'Manage\Model\MsgqueueTable' => function($sm){
                    $tableGateway = $sm->get('MsgqueueTableGateway');
                    $table = new MsgqueueTable($tableGateway);
                    return $table;
                },
                'MsgqueueTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Msgqueue());
                    return new TableGateway('msg_queue',$dbAdapter,null,$resultSetPrototype);
                },
            )
        );
    }
}