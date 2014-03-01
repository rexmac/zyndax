<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * This source file is subject to the Modified BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL:
 * http://rexmac.com/license/bsd2c.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to
 * license@rexmac.com so that we can send you a copy.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Plugin
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Controller\Plugin;

use \DateTime,
    Rexmac\Zyndax\Log\Logger,
    Rexmac\Zyndax\Service\UserService,
    \Zend_Auth,
    \Zend_Controller_Action_Exception as ActionException,
    \Zend_Controller_Action_HelperBroker as HelperBroker,
    \Zend_Controller_Dispatcher_Exception as DispatcherException,
    \Zend_Controller_Front as FrontController,
    \Zend_Controller_Request_Abstract as AbstractRequest,
    \Zend_Registry,
    \Zend_Session,
    \Zend_Session_Namespace;

/**
 * Zend controller plugin that checks if current user is authenticated.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Plugin
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Auth extends \Zend_Controller_Plugin_Abstract {

  /**
   * Array of routes that are allowed to bypass authentication
   *
   * @var array
   */
  private $_whitelist = null;

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
  }

  /**
   * Called before Zend_Controller_Front begins evaluating the
   * request against its routes.
   *
   * @param AbstractRequest $request
   * @return void
   */
  public function routeStartup(AbstractRequest $request) {
/*
    $route = strtolower(sprintf('%s/%s/%s',
      $request->getModuleName(),
      $request->getControllerName(),
      $request->getActionName()
    ));
    Logger::debug(__METHOD__.':: route = '.$route);
    $auth = Zend_Auth::getInstance();
    if($auth->hasIdentity()) {
      Logger::debug(__METHOD__.":: Auth has identity...");
      $user = UserService::find($auth->getIdentity());
      $user->setLastConnect(new DateTime());
      UserService::update();
      Zend_Registry::set('user', $user);
      Logger::debug(__METHOD__.':: logged in as user: ' . $user->getId() . ' - ' . $user->getUsername());

      if(!Zend_Session::$_unitTestEnabled) { // @codeCoverageIgnoreStart
        // If accessing non-admin UI and currently using LoginAs feature, then overwrite 'user' in registry
        $authCookieName = Zend_Registry::get('config')->session->auth->name;
        $ssa = new Zend_Session_Namespace($authCookieName);
        if(isset($ssa->loginAsUser) && 'admin' !== strtolower($request->getModuleName())) {
          $user = UserService::find($ssa->loginAsUser);
          #Logger::debug(__METHOD__.':: admin using login-as user: ' . $user->getId() . ' - ' . $user->getUsername());
          Zend_Registry::set('loginAs', true);
          Zend_Registry::set('user', $user);
        }
      } // @codeCoverageIgnoreEnd
    }
*/
  }

  /**
   * Called before an action is dispatched by Zend_Controller_Dispatcher.
   * Does nothing if current request matches a whitelisted route, or if
   * request is authenticated. Otherwise, redirects to login page.
   *
   * @param  AbstractRequest $request
   * @throws Zend_Controller_Dispatcher_Exception
   * @throws Zend_Controller_Action_Exception
   * @return void
   */
  public function preDispatch(AbstractRequest $request) {
    $route = strtolower(sprintf('%s/%s/%s',
      $request->getModuleName(),
      $request->getControllerName(),
      $request->getActionName()
    ));
    Logger::debug(__METHOD__.':: route = '.$route);



    $auth = Zend_Auth::getInstance();
    if($auth->hasIdentity()) {
      Logger::debug(__METHOD__.":: Auth has identity...");
      $user = UserService::find($auth->getIdentity());
      $user->setLastConnect(new DateTime());
      UserService::update();
      Zend_Registry::set('user', $user);
      Logger::debug(__METHOD__.':: logged in as user: ' . $user->getId() . ' - ' . $user->getUsername());

      if(!Zend_Session::$_unitTestEnabled) { // @codeCoverageIgnoreStart
        // If accessing non-admin UI and currently using LoginAs feature, then overwrite 'user' in registry
        $authCookieName = Zend_Registry::get('config')->session->auth->name;
        $ssa = new Zend_Session_Namespace($authCookieName);
        if(isset($ssa->loginAsUser) && 'admin' !== strtolower($request->getModuleName())) {
          $user = UserService::find($ssa->loginAsUser);
          #Logger::debug(__METHOD__.':: admin using login-as user: ' . $user->getId() . ' - ' . $user->getUsername());
          Zend_Registry::set('loginAs', true);
          Zend_Registry::set('user', $user);
        }
      } // @codeCoverageIgnoreEnd
    }



    $this->_isDispatchable($request);

    if(null === $this->_whitelist) {
      $this->_whitelist = Zend_Registry::get('config')->auth->whitelist->toArray();
    }

    foreach($this->_whitelist as $whitelistedRoute) {
      if(preg_match('|^' . $whitelistedRoute . '$|', $route)) {
        return;
      }
    }

    $auth = Zend_Auth::getInstance();
    if($auth->hasIdentity()) {
      Logger::debug(__METHOD__.":: Auth has identity...");

      #if(isset($_SERVER["REMOTE_ADDR"])) { $ip = $_SERVER["REMOTE_ADDR"]; }
      #elseif(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
      #elseif(isset($_SERVER["HTTP_CLIENT_IP"])) { $ip = $_SERVER["HTTP_CLIENT_IP"]; }
      #else { $ip = null; }

      return;
    }

    #$request->setDispatched(false);  // Cancel the current action

    // Handle unauthorized request...
    Logger::debug(__METHOD__.":: Unauthorized request. Redirecting...");
    if(!Zend_Session::$_unitTestEnabled) { // @codeCoverageIgnoreStart
      $session = new Zend_Session_Namespace('referrer');
      $session->uri = $request->getRequestUri();
    } // @codeCoverageIgnoreEnd

    if($request->isXmlHttpRequest()) {
      return $this->getResponse()->setHttpResponseCode(500)->setBody(json_encode(array('redirect' => '/user/login')))->sendResponse();
    }
    $helper = HelperBroker::getStaticHelper('redirector');
    $helper->gotoUrl('/user/login');
  }

  /**
   * Return true if request is dispathcable
   *
   * Normally, the following non-dispatchable controller check logic is done
   * in the Zend_Controller_Dispatcher_Standard::dispatch method. We also
   * do it here before the request is dispatched to allow for errors to be
   * displayed for unauthenticated requests.
   *
   * @param  AbstractRequest $request
   * @throws Zend_Controller_Dispatcher_Exception
   * @throws Zend_Controller_Action_Exception
   * @return bool
   */
  private function _isDispatchable(AbstractRequest $request) {
    $dispatcher = FrontController::getInstance()->getDispatcher();
    if(!$dispatcher->isDispatchable($request)) {
      $controller = $request->getControllerName();
      if(!$dispatcher->getParam('useDefaultControllerAlways') && !empty($controller)) {
        throw new DispatcherException('Invalid controller specified (' . $request->getControllerName() . ')');
      }
    } else {
      $moduleName = $request->getModuleName();
      $controllerClassName = $dispatcher->getControllerClass($request);
      $dispatcher->loadClass($controllerClassName);
      $controllerMethods = get_class_methods(($moduleName !== 'default' ? ucfirst($moduleName) . '_' : '') . $controllerClassName);
      #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.':: controllerClassName = '.$controllerClassName);
      #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.':: controllerMethods = '.var_export($controllerMethods, true));
      if(!is_array($controllerMethods) || !in_array($dispatcher->getActionMethod($request), $controllerMethods)) {
        #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.':: controllerMethods = '.var_export($controllerMethods, true));
        throw new ActionException(sprintf('Action "%s" does not exist', $request->getActionName()), 404);
      }
    }
    return true;
  }
}
