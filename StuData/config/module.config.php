<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'StuData\Controller\ShowDataInfo' => 'StuData\Controller\ShowDataInfoController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'stuData' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/stuData',
                    'defaults' => array(
                        '__NAMESPACE__' => 'StuData\Controller',
                        'controller'    => 'ShowDataInfo',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action][/:param1][/:param2][/:param3][/:param4][/:param5]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'param1'	=> '[a-zA-Z0-9]*',
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
            'stuData' => __DIR__ . '/../view',
        ),
    ),
);
