<?php
/**
 * Copyright (c) 2012 Jurian Sluiman.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     ZfcAdmin
 * @author      Jurian Sluiman <jurian@soflomo.com>
 * @copyright   2012 Jurian Sluiman.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://zf-commons.github.com
 */

namespace ZfcAdmin;

use Zend\ModuleManager\Feature;
use Zend\Loader;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;

/**
 * Module class for ZfcAdmin
 *
 * @package ZfcAdmin
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\ServiceProviderInterface,
    Feature\BootstrapListenerInterface
{
    /**
     * @{inheritdoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            Loader\AutoloaderFactory::STANDARD_AUTOLOADER => array(
                Loader\StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @{inheritdoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @{inheritdoc}
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'admin_navigation' => 'ZfcAdmin\Navigation\Service\AdminNavigationFactory',
            ),
            'invokables' => array(
                'ZfcAdmin\Service\Authorize' => 'ZfcAdmin\Service\Authorize',
            )
        );
    }

    /**
     * @{inheritdoc}
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getParam('application');
        $sm  = $app->getServiceManager();
        $em  = $app->getEventManager()->getSharedManager();

        $config = $sm->get('config');
        if (false === $config['zfcadmin']['use_admin_layout']) {
            return;
        }

        $layout = $config['zfcadmin']['admin_layout_template'];
        $em->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, function($e) use ($layout) {
            $route = $e->getRouteMatch()->getMatchedRouteName();
            if (0 !== strpos($route, 'admin')) {
                return;
            }

            $controller = $e->getTarget();
            $controller->layout($layout);
        }, 100);

        $service = $sm->get('ZfcAdmin\Service\Authorize');
        $app->getEventManager()->attach('route', array($service, 'onRoute'), -1000);
    }
}