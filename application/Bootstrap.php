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
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */

/**
 * Application bootstrap
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap {

  /**
   * Initialize request and response objects; extend ZF defaults
   *
   * @return void
   */
  protected function _initRequest() {
    $this->bootstrap('frontcontroller')->getResource('frontcontroller')->setRequest('Rexmac\Zyndax\Controller\Request\HttpRequest');
    $this->bootstrap('frontcontroller')->getResource('frontcontroller')->setResponse('Rexmac\Zyndax\Controller\Response\HttpResponse');
  }

  /**
   * Initialize registry
   *
   * @return bool
   */
  protected function _initRegistry() {
    Zend_Registry::set('config', new Zend_Config($this->getOptions(), true));
    Zend_Registry::set('version', Rexmac\Zyndax\Version\Version::VERSION);
    Zend_Registry::set('build', Rexmac\Zyndax\Version\Version::BUILD);
    Zend_Registry::set('siteName', $this->getOption('siteName'));
    Zend_Registry::set('staticSalt', $this->getOption('staticSalt'));
    $view = new Zend_View_Helper_ServerUrl();
    Zend_Registry::set('siteDomain', preg_replace('/^https?:\/\//', '', $view->serverUrl()));
    return true;
  }

  /**
   * Initialize logging
   *
   * @return bool
   */
  protected function _initLog() {
    if($this->hasPluginResource('log')) {
      $dirName = dirname(Zend_Registry::get('config')->resources->log->stream->writerParams->stream);
      if(!file_exists($dirName)) {
        @mkdir($dirName, 0775, true);
      }
      Zend_Registry::set('log', $this->getPluginResource('log')->getLog());
    }
    return true;
  }

  /**
   * Initialize session
   *
   * @return bool
   */
  protected function _initModifiedSession() {
    session_set_cookie_params(0, '/', '.'.Zend_Registry::get('siteDomain'));
    $this->bootstrap('session');
    #Zend_Session::start(true);
    return true;
  }

  /**
   * Initialize routes and navigation
   *
   * @return bool
   */
  protected function _initRoutes() {
    $this->bootstrap('frontcontroller');
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', APPLICATION_ENV);
    $router = $this->getResource('frontcontroller')->getRouter();
    $router->addConfig($config, 'routes');
    return true;
  }

  /**
   * Initialize navigation
   *
   * @return bool
   */
  protected function _initNavigation() {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/navigation.ini', APPLICATION_ENV);
    $container = new Zend_Navigation($config->navigation->toArray());
    $this->bootstrap('view');
    $view = $this->getResource('view');
    $view->navigation($container);
    return true;
  }

  /**
   * Initialize helpers
   *
   * @return bool
   */
  protected function _initHelpers() {
    Zend_Controller_Action_HelperBroker::addPath(
      APPLICATION_PATH.'/../library/Rexmac/Zyndax/Controller/Action/Helper',
      'Rexmac\Zyndax\Controller\Action\Helper\\'
    );
    return true;
  }
}
