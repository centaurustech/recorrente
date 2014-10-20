<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Application\Model\Project;


class Module
{
//     public function onBootstrap(MvcEvent $e)
//     {
//         $eventManager        = $e->getApplication()->getEventManager();
//         $moduleRouteListener = new ModuleRouteListener();
//         $moduleRouteListener->attach($eventManager);
//     }
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager  = $e->getApplication()->getEventManager();
        $sharedManager = $eventManager->getSharedManager();
        //controller can't dispatch request action that passed to the url
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController',
            'dispatch',
            array($this, 'handleControllerCannotDispatchRequest' ), 101);
    }
    
    public function handleControllerCannotDispatchRequest(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('action');
        $controller = get_class($e->getTarget());
        $sm = $e->getApplication()->getServiceManager();
        
        // error-controller-cannot-dispatch
        if (! method_exists($e->getTarget(), $action.'Action')) {
            $logText = 'The requested controller '.$controller.' was unable to dispatch the request : '.$action.' Action';
            // Verifica se existe um projeto com o código para montagem da url dinâmica
            $oProject = new Project($sm);
            $iProjectId = $oProject->loadByShortURL($action);
            if (is_numeric($iProjectId)) {
                // Redirect
                $controller = $e->getTarget();
                //return \Zend\Mvc\Controller\AbstractActionController::forward()->dispatch('ProjectController', array('action' => 'detalhes', 'id' => $iProjectId));
                return $controller->plugin('redirect')->toURL('/projeto/detalhes/'.$iProjectId);
            }
        }
    }

    
    
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config_misterio'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                        if (isset($session['validators'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validators'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));

                            }
                        }
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },


            ),
        );
    }

    
    
    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
