<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Stu\Controller\AddInfo' => 'Stu\Controller\AddInfoController',
            'Stu\Controller\Register' => 'Stu\Controller\RegisterController',
            'Stu\Controller\Stu' => 'Stu\Controller\StuController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'stu' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/stu',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Stu\Controller',
                        'controller'    => 'Stu',
                        'action'        => 'changeVolunteer',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action][/id/:id][/select/:select][/mail/:mail][/active/:active][/rid/:rid][/now_stage/:now_stage][/uid/:uid][/param2/:param2][/param3/:param3][/param4/:param4]]',
//                            'route'    => '/[:controller[/:action][/:uid][/:now_stage][/:page][/success/:success][/fail/:fail]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'rid'		 => '[a-zA-Z0-9]*',
                                'uid' => '[a-zA-Z0-9]*',
                                'now_stage'    => '[a-zA-Z0-9]*',
                                'param3'    => '[a-zA-Z0-9]*',
                                'param4'    => '[a-zA-Z0-9]*',
                                'active'    => '[a-zA-Z0-9]*',
                                'select'    => '[a-zA-Z0-9]*',
                                'mail'    => '[a-zA-Z0-9@.]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),


    'view_manager' => array(
        'template_path_stack' => array(
            'stu' => __DIR__ . '/../view',
        ),
    ),
);
