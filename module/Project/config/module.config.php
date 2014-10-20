<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Project\Controller\Project' => 'Project\Controller\ProjectController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'project' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/projeto[/][:action][/:id]',	// Indica que a chamada de ação será direto na raiz da URL
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Project\Controller\Project',
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
						'v1/projeto/ajax/contribuicao-login'			          => __DIR__ . '/../view/project/ajax/v1-contribuicao-login.phtml',
						'v1/projeto/ajax/contribuicao-cadastro-pf'			    => __DIR__ . '/../view/project/ajax/v1-contribuicao-cadastro-pf.phtml',
   					'v1/projeto/ajax/contribuicao-cadastro-pj'			    => __DIR__ . '/../view/project/ajax/v1-contribuicao-cadastro-pj.phtml',
   					'v1/projeto/ajax/contribuicao-validacao'            => __DIR__ . '/../view/project/ajax/v1-contribuicao-validacao-cadastro.phtml',
            'v1/projeto/ajax/contribuicao-escolhe-valor'        => __DIR__ . '/../view/project/ajax/v1-contribuicao-escolhe.phtml',
						'v1/projeto/ajax/contribuicao-atualiza-endereco'    => __DIR__ . '/../view/project/ajax/v1-contribuicao-atualiza-endereco.phtml',
						'v1/projeto/ajax/contribuicao-forma-pgto'           => __DIR__ . '/../view/project/ajax/v1-contribuicao-forma-pgto.phtml',
						'v1/projeto/ajax/contribuicao-resumo-final'         => __DIR__ . '/../view/project/ajax/v1-contribuicao-resumo-final.phtml',
						'v1/projeto/ajax/contribuicao-cancelada-por-usuario'=> __DIR__ . '/../view/project/ajax/v1-contribuicao-cancelada-por-usuario.phtml',
            'geral/ajax/contribuicao-cadastro-pf'               => __DIR__ . '/../view/project/ajax/contribuicao-cadastro-pf.phtml',
						'geral/ajax/contribuicao-cadastro-pj'               => __DIR__ . '/../view/project/ajax/contribuicao-cadastro-pj.phtml',
						'geral/ajax/contribuicao-validacao'                 => __DIR__ . '/../view/project/ajax/contribuicao-validacao-cadastro.phtml',
						'geral/ajax/contribuicao-login'			                => __DIR__ . '/../view/project/ajax/contribuicao-login.phtml',
						'geral/ajax/contribuicao-escolhe-valor'             => __DIR__ . '/../view/project/ajax/contribuicao-escolhe-valor.phtml',
						'geral/ajax/contribuicao-atualiza-endereco'         => __DIR__ . '/../view/project/ajax/contribuicao-atualiza-endereco.phtml',
						'geral/ajax/contribuicao-forma-pgto'                => __DIR__ . '/../view/project/ajax/contribuicao-forma-pgto.phtml',
						'geral/ajax/contribuicao-resumo-final'              => __DIR__ . '/../view/project/ajax/contribuicao-resumo-final.phtml',
						'geral/ajax/contribuicao-cancelada-por-usuario'     => __DIR__ . '/../view/project/ajax/contribuicao-cancelada-por-usuario.phtml',
            'projeto/assinantes'					                      => __DIR__ . '/../view/project/project/assinantes.phtml',
            'v1/projeto/assinantes'					                    => __DIR__ . '/../view/project/project/v1-assinantes.phtml',
            'v1/projeto/detalhes'					                      => __DIR__ . '/../view/project/project/v1-detalhes.phtml',
            'v1/projeto/contribuicao-cancelada-por-usuario'     => __DIR__ . '/../view/project/project/v1-contribuicao-cancelada-por-usuario.phtml',
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