<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


return array(
		// The following section is new and should be added to your file
		'router' => array(
				'routes' => array(
						'home' => array(
						    'type' => 'Module\Router\Content',
						//                'type' => 'Zend\Mvc\Router\Http\Literal',
								'type' => 'segment',
								'options' => array(
										'route'    => '/[:action][/:id]',
										'defaults' => array(
												'controller' => 'Application\Controller\Index',
												'action'     => 'index',
										),
								),
						),
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'application' => array(
								'type'    => 'Literal',
								'options' => array(
										'route'    => '/application',
										'defaults' => array(
												'__NAMESPACE__' => 'Application\Controller',
												'controller'    => 'Index',
												'action'        => 'index',
										),
								),
								'may_terminate' => true,
								'child_routes' => array(
										'default' => array(
												'type'    => 'Segment',
												'options' => array(
														'route'    => '/[:controller[/:action]]',
														'constraints' => array(
																'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
														),
														'defaults' => array(
														),
												),
										),
								),
						),
				),
		),
		'service_manager' => array(
				'factories' => array(
						'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
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
		'controllers' => array(
				'invokables' => array(
						'Application\Controller\Index' => 'Application\Controller\IndexController'
				),
		),
		'view_manager' => array(
				'display_not_found_reason' => true,
				'display_exceptions'       => true,
				'doctype'                  => 'HTML5',
				'not_found_template'       => 'error/404',
				'exception_template'       => 'error/index',
				'template_map' => array(
						'layout/layout'           					      => __DIR__ . '/../view/layout/layout.phtml',
						'layout/moldura'           					      => __DIR__ . '/../view/layout/moldura.phtml',
						'v1/layout/moldura'           					  => __DIR__ . '/../view/layout/v1-moldura.phtml',
						'layout/autenticacao'								      => __DIR__ . '/../view/layout/autenticacao.phtml',
            'layout/webadmin'									        => __DIR__ . '/../view/layout/webadmin.phtml',
						'application/index/index' 					      => __DIR__ . '/../view/application/index/index.phtml',
						'error/404'               					      => __DIR__ . '/../view/error/404.phtml',
						'error/index'             					      => __DIR__ . '/../view/error/index.phtml',
						'geral/projetos'					                 => __DIR__ . '/../view/application/index/projetos.phtml',
						'geral/ajax/lista-estados'					      => __DIR__ . '/../view/application/ajax/lista-estados.phtml',
						'geral/ajax/lista-cidades'					      => __DIR__ . '/../view/application/ajax/lista-cidades.phtml',
						'geral/ajax/header-logged'                => __DIR__ . '/../view/application/ajax/header-logged.phtml',
						'geral/ajax/header-login'                 => __DIR__ . '/../view/application/ajax/header-login.phtml',
						'geral/ajax/message'                      => __DIR__ . '/../view/application/ajax/message.phtml',
						/* templates exclusivos do novo layout em desenvolviment */
						'v1/home'                                 => __DIR__ . '/../view/application/index/v1-index.phtml',
						'v1/info'                                 => __DIR__ . '/../view/application/index/v1-info.phtml',
				    'v1/login'                                => __DIR__ . '/../view/application/index/v1-login.phtml',
						'v1/cadastro'                             => __DIR__ . '/../view/application/index/v1-cadastro.phtml',
						'v1/cadastro-pf'                          => __DIR__ . '/../view/application/index/v1-cadastro-pf.phtml',
						'v1/cadastro-pj'                          => __DIR__ . '/../view/application/index/v1-cadastro-pj.phtml',
						'v1/projetos'                             => __DIR__ . '/../view/application/index/v1-lista-projetos.phtml',
						'v1/como-funciona'                        => __DIR__ . '/../view/application/index/v1-como-funciona.phtml',
						'v1/cadastra-projeto'                     => __DIR__ . '/../view/application/index/v1-cadastra-projeto.phtml',
						'v1/faq'                                  => __DIR__ . '/../view/application/index/v1-faq.phtml',
						'v1/contato'                                  => __DIR__ . '/../view/application/index/v1-contato.phtml',
						'v1/termos-uso'                           => __DIR__ . '/../view/application/index/v1-termos-uso.phtml',
						'v1/crowdfunding-x-recorrente'                           => __DIR__ . '/../view/application/index/v1-crowdfunding-x-recorrente.phtml',
				    'v1/ajax/header-logged'                   => __DIR__ . '/../view/application/ajax/v1-header-logged.phtml',
						'v1/ajax/header-login'                    => __DIR__ . '/../view/application/ajax/v1-header-login.phtml',
		),
				'template_path_stack' => array(
						__DIR__ . '/../view',
				),
		),
		// Placeholder for console routes
		'console' => array(
				'router' => array(
						'routes' => array(
						    'controller' => 'Application\Controller\Index',
						    'action'     => 'consoledefault'
						),
				),
		),
);

