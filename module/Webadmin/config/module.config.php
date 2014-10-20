<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Webadmin\Controller\Webadmin' => 'Webadmin\Controller\WebadminController',
            'Webadmin\Controller\Report' => 'Webadmin\Controller\ReportController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
						'report' => array(
						    'type' => 'Module\Router\Content',
						    'priority' => 100,
								'type' => 'segment',
								'options' => array(
										'route'    => '/wbadmin/relatorio/[:action][/:id]',
										'defaults' => array(
												'controller' => 'Webadmin\Controller\Report',
												'action'     => 'index',
										),
								),
						),
            'wbadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/wbadmin[/][:action][/:id]',	// Indica que a chamada de ação será direto na raiz da URL
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Webadmin\Controller\Webadmin',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'webadmin/pre-projeto'          => __DIR__ . '/../view/webadmin/webadmin/projetos-pre.phtml',
            'webadmin/mensagem-padrao'      => __DIR__ . '/../view/webadmin/webadmin/mensagem-padrao.phtml',
        ),
    ),


    'translator' => array(
        'locale' => 'pt_BR',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
);