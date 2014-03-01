<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * Zyndax is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zyndax is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zyndax.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Bootstrap
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */
use \Zend_Config_Ini,
    \Zend_Controller_Router_Route_Hostname,
    \Zend_Registry;

/**
 * Admin module bootstrap class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Bootstrap
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */
class Admin_Bootstrap extends \Zend_Application_Module_Bootstrap {

  /**
   * Initialize config
   *
   * @return void
   */
  protected function _initConfig() {
    $iniOptions = new Zend_Config_Ini(dirname(__FILE__) . '/configs/module.ini', APPLICATION_ENV);
    $this->setOptions($iniOptions->toArray());

    #$app = $this->getApplication();
    #$app->setOptions($app->mergeOptions($iniOptions->toArray()));
  }

  /**
   * Initialize routes
   *
   * @return void
   */
  protected function _initRoutes() {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/modules/admin/configs/routes.ini', APPLICATION_ENV);
    #$this->bootstrap('frontcontroller');
    $router = $this->getApplication()->getResource('frontcontroller')->getRouter();
    $router->addConfig($config, 'routes');
    $siteDomain = str_replace('admin.', '', Zend_Registry::get('siteDomain'));
    $hostnameRoute = new Zend_Controller_Router_Route_Hostname(
      'admin.' . $siteDomain, array(
        'module'     => 'admin',
        'controller' => 'index',
        'action'     => 'index'
      )
    );
    // Chain all admin routes to the hostname route
    foreach($router->getRoutes() as $key => $route) {
      if(strpos($key, 'admin') === 0) {
        $router->addRoute($key, $hostnameRoute->chain($route));
      }
    }
  }

 /**
   * Initialize navigation
   *
   * @return void
   */
  protected function _initNavigation() {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/modules/admin/configs/navigation.ini', APPLICATION_ENV);
    $container = $this->getApplication()->getResource('view')->navigation()->getContainer();
    $container->addPages($config->navigation->toArray());
  }
}
