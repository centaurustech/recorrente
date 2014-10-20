<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Usuario\Controller\Usuario' => 'Usuario\Controller\UsuarioController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'usuario' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/usuario[/][:action][/:id]',	// Indica que a chamada de ação será direto na raiz da URL
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Usuario\Controller\Usuario',
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
						'v1/usuario/perfil'               => __DIR__ . '/../view/usuario/usuario/v1-perfil.phtml',
						'v1/usuario/pagamentos'           => __DIR__ . '/../view/usuario/usuario/v1-pagamentos.phtml',
						'v1/usuario/projetos'             => __DIR__ . '/../view/usuario/usuario/v1-projetos.phtml',
						'v1/usuario/projetos-valores-recebidos'   => __DIR__ . '/../view/usuario/usuario/v1-projetos-valores-recebidos.phtml',
						'v1/usuario/ler-mensagem'               => __DIR__ . '/../view/usuario/usuario/ler-mensagem.phtml',
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