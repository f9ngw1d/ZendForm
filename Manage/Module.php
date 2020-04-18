<?php
namespace Manage;

use Manage\Model\ConfigKey;
use Manage\Model\ConfigKeyTable;
use Manage\Model\ConfigValue;
use Manage\Model\ConfigValueTable;
use Manage\Model\ManageTime;
use Manage\Model\ManageTimeTable;
use Manage\Model\Rolepermission;
use Manage\Model\RolepermissionTable;
use Manage\Model\TBaseCollege;
use Manage\Model\TBaseCollegeTable;
use Manage\Model\TDbDivision;
use Manage\Model\TDbDivisionTable;
use Manage\Model\TDbUniversity;
use Manage\Model\TDbUniversityTable;
use Manage\Model\TDbUnderSubject;
use Manage\Model\TDBUnderSubjectTable;
use Manage\Model\UsrPermission;
use Manage\Model\UsrPermissionTable;
use Manage\Model\UsrRole;
use Manage\Model\UsrRoleTable;
use Manage\Model\UsrTeacher;
use Manage\Model\UsrTeacherTable;
use Manage\Model\MTBaseTeam;
use Manage\Model\MTBaseTeamTable;
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
                'Manage\Model\RolePermissionTable' => function($sm){
                    $tableGateway = $sm->get('RolePermissionTableGateway');
                    $table = new RolePermissionTable($tableGateway);
                    return $table;
                },
                'RolePermissionTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new RolePermission());
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
                'TDbUnderSubjectTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TDbUnderSubject());
                    return new TableGateway('manage_subject_map',$dbAdapter,null,$resultSetPrototype);
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
                'Manage\Model\MTBaseTeamTable' => function($sm){
                    $tableGateway = $sm->get('MTBaseTeamTableGateway');
                    $table = new MTBaseTeamTable($tableGateway);
                    return $table;
                },
                'MTBaseTeamTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new MTBaseTeam());
                    return new TableGateway('base_team',$dbAdapter,null,$resultSetPrototype);
                },
            )
        );
    }
}