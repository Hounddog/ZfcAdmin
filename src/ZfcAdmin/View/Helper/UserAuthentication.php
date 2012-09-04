<?php
// ./module/Application/src/Application/View/Helper/AbsoluteUrl.php
namespace ZfcAdmin\View\Helper;
 
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class UserAuthentication extends AbstractHelper implements ServiceManagerAwareInterface
{

    /**
     * Service Manager
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __invoke()
    {
        $zfcUserAuthentication = $this->serviceManager
            ->getServiceLocator()
            ->get('ControllerPluginManager')
            ->get('zfcuserauthentication');
        if (!$zfcUserAuthentication->hasIdentity()) {
           return false;
        }
        return true;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}