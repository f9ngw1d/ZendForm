<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Manage\Controller\Setting' => 'Manage\Controller\SettingController',
            'Manage\Controller\SystemManagement' => 'Manage\Controller\SystemManagementController',
            'Manage\Controller\State2CheckProject' => 'Manage\Controller\State2CheckProjectController',
            'Manage\Controller\Account' => 'Manage\Controller\AccountController',
            'Manage\Controller\SuperCharge' => 'Manage\Controller\SuperChargeController',
            'Manage\Controller\UnderSubject' => 'Manage\Controller\UnderSubjectController',
            'Manage\Controller\TStuInfo' => 'Manage\Controller\TStuInfoController',
            'Manage\Controller\Sendmsg' => 'Manage\Controller\SendmsgController',

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
                            'route'    => '/[:controller[/:action][/uid/:uid][/param/:param][/param1/:param1][/param3/:param3][/id/:id][/rid/:rid][/page/:page]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'uid'    => '[a-zA-Z0-9]*',
                                'param'    => '[a-zA-Z0-9=]*',
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
