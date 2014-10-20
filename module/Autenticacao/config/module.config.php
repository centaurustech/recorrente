<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Autenticacao\Controller\Autenticacao' => 'Autenticacao\Controller\AutenticacaoController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'autenticacao' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/seguro[/][:action][/:id]',	// Indica que a chamada de ação será direto na raiz da URL
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Autenticacao\Controller\Autenticacao',
                        'action'     => 'login',
                    ),
                ),
            ),
        ),
    ),

/*
    'view_manager' => array(
        'template_path_stack' => array(
            'professor' => __DIR__ . '/../view',
        ),
    ),
*/

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'autenticacao/login'					=> __DIR__ . '/../view/autenticacao/autenticacao/login.phtml',
            'autenticacao/esqueci-senha'					=> __DIR__ . '/../view/autenticacao/autenticacao/esqueci-senha.phtml',
            'autenticacao/aviso-recuperar-senha'	=> __DIR__ . '/../view/autenticacao/autenticacao/aviso-recuperar-senha.phtml',
            'autenticacao/altera-senha'	=> __DIR__ . '/../view/autenticacao/autenticacao/altera-senha.phtml',
            'v1/autenticacao/esqueci-senha'					=> __DIR__ . '/../view/autenticacao/autenticacao/v1-esqueci-senha.phtml',
            'v1/autenticacao/aviso-recuperar-senha'	=> __DIR__ . '/../view/autenticacao/autenticacao/v1-aviso-recuperar-senha.phtml',
            'v1/autenticacao/altera-senha'	=> __DIR__ . '/../view/autenticacao/autenticacao/v1-altera-senha.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
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