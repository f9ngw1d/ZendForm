<?php
namespace Stu;

//use Basicinfo\Model\Professionstaff;
//use Basicinfo\Model\ProfessionstaffTable;
use Stu\Model\StuBase;
use Stu\Model\StuBaseTable;
use Stu\Model\Check;
use Stu\Model\CheckTable;
use Stu\Model\Project;
use Stu\Model\ProjectTable;
use Stu\Model\Division;
use Stu\Model\DivisionTable;
use Stu\Model\StuReexamResult;
use Stu\Model\StuReexamResultTable;
use Stu\Model\UnderSubject;
use Stu\Model\UnderSubjectTable;
use Stu\Model\University;
use Stu\Model\UniversityTable;
use Stu\Model\UsrStu;
use Stu\Model\UsrStuTable;
use Stu\Model\Sendmsg;
use Stu\Model\Infovalidatemail;
use Stu\Model\InfovalidatemailTable;
use Stu\Model\Honour;
use Stu\Model\HonourTable;
use Stu\Model\UniversityFree;
use Stu\Model\UniversityFreeTable;
use Stu\Model\Electronicinfo;
use Stu\Model\ElectronicinfoTable;
use Stu\Model\Einfo;
use Stu\Model\EinfoTable;
use Stu\Model\Nationality;
use Stu\Model\NationalityTable;
use Stu\Model\PoliticalStatus;
use Stu\Model\PoliticalStatusTable;
//use Basicinfo\Model\Staff;
//use Basicinfo\Model\StaffTable;
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
                'Stu\Model\Sendmsg' => function(){
                    $sendmsg = new Sendmsg();
                    return $sendmsg;
                },
                'Stu\Model\NationalityTable' => function($sm){
                    $tableGateway = $sm->get('NationalityTableGateway');
                    $table = new NationalityTable($tableGateway);
                    return $table;
                },
                'NationalityTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Nationality());
                    return new TableGateway('stu_nationality',$dbAdapter,null,$resultSetPrototype);
                },
                'Stu\Model\PoliticalStatusTable' => function($sm){
                    $tableGateway = $sm->get('PoliticalStatusTableGateway');
                    $table = new PoliticalStatusTable($tableGateway);
                    return $table;
                },
                'PoliticalStatusTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PoliticalStatus());
                    return new TableGateway('stu_political_status',$dbAdapter,null,$resultSetPrototype);
                },
                'Stu\Model\UsrStuTable' => function($sm){
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
                'Stu\Model\EinfoTable' => function($sm){
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
                'Stu\Model\ElectronicinfoTable' => function($sm){
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
                'Stu\Model\UniversityFreeTable' => function($sm){
                    $tableGateway = $sm->get('UniversityFreeTableGateway');
                    $table = new UniversityFreeTable($tableGateway);
                    return $table;
                },
                'UniversityFreeTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UniversityFree());
                    return new TableGateway('db_university_free',$dbAdapter,null,$resultSetPrototype);
                },
                'Stu\Model\HonourTable' => function($sm){
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
                'Stu\Model\InfovalidatemailTable' => function($sm){
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
                'Stu\Model\CheckTable' =>  function($sm) {
                    $tableGateway = $sm->get('CheckTableGateway');
                    $table = new CheckTable($tableGateway);
                    return $table;
                },
                'CheckTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Check());
                    return new TableGateway('stu_check', $dbAdapter, null, $resultSetPrototype);
                },
                'Stu\Model\DivisionTable' =>  function($sm) {
                    $tableGateway = $sm->get('DivisionTableGateway');
                    $table = new DivisionTable($tableGateway);
                    return $table;
                },
                'DivisionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Division());
                    return new TableGateway('db_administrative_division', $dbAdapter, null, $resultSetPrototype);
                },
                'Stu\Model\ProjectTable' =>  function($sm) {
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
                'Stu\Model\StuBaseTable' => function ($sm) {
                    $tableGateway = $sm->get('StuBaseTableGateway');
                    $table = new StuBaseTable($tableGateway);
                    return $table;
                },
                'StuBaseTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new StuBase());
                    return new TableGateway('stu_base', $dbAdapter, null, $resultSetPrototype);
                },
                'Stu\Model\StuReexamResultTable' =>  function($sm) {
                    $tableGateway = $sm->get('StuReexamResultTableGateway');
                    $table = new StuReexamResultTable($tableGateway);
                    return $table;
                },
                'StuReexamResultTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new StuReexamResult());
                    return new TableGateway('stu_reexam_result', $dbAdapter, null, $resultSetPrototype);
                },
                'Stu\Model\UnderSubjectTable' =>  function($sm) {
                    $tableGateway = $sm->get('UnderSubjectTableGateway');
                    $table = new UnderSubjectTable($tableGateway);
                    return $table;
                },
                'UnderSubjectTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UnderSubject());
                    return new TableGateway('db_under_subject', $dbAdapter, null, $resultSetPrototype);
                },
                'Stu\Model\UniversityTable' =>  function($sm) {
                    $tableGateway = $sm->get('UniversityTableGateway');
                    $table = new UniversityTable($tableGateway);
                    return $table;
                },
                'UniversityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new University());
                    return new TableGateway('db_university', $dbAdapter, null, $resultSetPrototype);
                },
                'Basicinfo\Model\ProfessionstaffTable' => function($sm){
                    $tableGateway = $sm->get('ProfessionstaffTableGateway');
                    $table = new ProfessionstaffTable($tableGateway);
                    return $table;
                },
                'ProfessionstaffTableGateway' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Professionstaff());
                    return new TableGateway('base_profession_staff',$dbAdapter,null,$resultSetPrototype);
                },
                'Basicinfo\Model\StaffTable' => function($sm){
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
            )
        );
    }
}