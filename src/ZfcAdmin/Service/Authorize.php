<?php

namespace ZfcAdmin\Service;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Authorize implements ServiceManagerAwareInterface
{
    public function onRoute(MvcEvent $e) 
    {
        $response   = $e->getResponse(); 
        $app        = $e->getTarget();
        $match      = $app->getMvcEvent()->getRouteMatch();
        $routeName  = $match->getMatchedRouteName();
        $sm = $this->getServiceManager();

        
        if (0 !== strpos($routeName, 'admin')) {
            return;
        }

        if ($routeName =='admin/login') {
            return;
        }

        $zfcUserAuthentication = $sm->get('ControllerPluginManager')->get('zfcuserauthentication');

        if (!$zfcUserAuthentication->hasIdentity()) {
            $response->getHeaders()->addHeaders(array(
                'Location' => '/admin/login',
            ));
            return $response;
        }
        
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
            return $this->_serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }
}