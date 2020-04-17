<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Manage\Controller\Setting' => 'Manage\Controller\SettingController',
            'Manage\Controller\SystemManagement' => 'Manage\Controller\SystemManagementController',
            'Manage\Controller\State2CheckProject' => 'Manage\Controller\State2CheckProjectController',
            'Manage\Controller\Account' => 'Manage\Controller\AccountController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'manage' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/manage',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Manage\Controller',
                        'controller'    => 'Setting',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action][/param2/:param2][/param3/:param3][/param4/:param4]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'param1'     => '[a-zA-Z0-9]*',
                                'param2'    => '[a-zA-Z0-9]*',
                                'param3'    => '[a-zA-Z0-9]*',
                                'param4'    => '[a-zA-Z0-9]*',
                                'param5'    => '[a-zA-Z0-9]*',
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
            'manage' => __DIR__ . '/../view',
        ),
    ),
);
