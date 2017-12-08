<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\View\ContatoForm;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;


class Module
{
    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager();
        // Registering a listener at default priority, 1, which will trigger
        // after the ConfigListener merges config.
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'onMergeConfig']);

    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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

       /**
     * Recupera código do usuário da sessão
     * Modify the configuration; here, we'll remove a specific key
     * Pass the changed configuration back to the listener
     *
     * @param ModuleEvent $e
     */
    public function onMergeConfig(ModuleEvent $e)
    {
        $auth = new AuthenticationService();
        $coUsuario = null;
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $coUsuario = $identity->coUsuario;
        }
        $remote = new RemoteAddress();
        $ip = $remote->getIpAddress();

        $configListener = $e->getConfigListener();
        $config = $configListener->getMergedConfig(false);
        $config['user_identify']['co_usuario'] = $coUsuario;
        $config['user_identify']['ip_usuario'] = $ip;
        $configListener->setMergedConfig($config);
    }
    public function getFormElementConfig()
    {
        return [
            'factories' => [
                'ContatoForm' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $em = $locator->get('doctrine.entitymanager.orm_default');
                    return new ContatoForm($em);
                }
            ]
        ];
    }


}
