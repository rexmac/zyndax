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

use Rexmac\Zyndax\Log\Logger,
    \Zend_Controller_Action_HelperBroker as HelperBroker,
    \Zend_Controller_Request_Abstract as AbstractRequest,
    \Zend_Registry,
    \Zend_View_Helper_Navigation_HelperAbstract;

/**
 * Zend controller plugin that checks if current use has ACL rights to the
 * currently requested resource.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Plugin
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Acl extends \Zend_Controller_Plugin_Abstract {

  /**
   * Zend_Acl object
   *
   * @var Zend_Acl
   */
  private $_acl = null;

  /**
   * Pre-dipatch method that ensures that current user has ACL rights to
   * access the requested resource.
   *
   * @param AbstractRequest $request
   * @return void
   */
  public function preDispatch(AbstractRequest $request) {
    if(null === $this->_acl) {
      $this->_acl = new \Rexmac\Zyndax\Acl\Acl();
    }
    Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->_acl);
    Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($this->_acl->getUser()->getRole()->getName());
    Zend_Registry::set('acl', $this->_acl);
    #Logger::debug(__METHOD__.':: acl user = '.$this->_acl->getUser()->getUsername());
    if('testing' === APPLICATION_ENV) {
      // If we're testing and being redirected,we don't care about ACL
      // Why does this only happen during testing? May have something to do with redirect helper not exiting
      if($response = \Zend_Controller_Front::getInstance()->getResponse()->getHeader('Location')) {
        return;
      }
    }

    if(!$this->_isUserAllowed($request)) {
      #Logger::debug(__METHOD__.':: ACL told us to get lost. Redirecting...');
      // Redirect non-admins away from admin sub-domain
      $siteDomain = Zend_Registry::get('siteDomain');
      if(0 === strpos($siteDomain, 'admin') && !$this->_acl->isUserAllowed('mvc:admin', 'view')) {
        return HelperBroker::getStaticHelper('redirector')->gotoUrl($request->getScheme() . '://' . str_replace('admin.', '', $siteDomain) . $request->getRequestUri());
      }

      $request->setModuleName('default')->setControllerName('error')->setActionName('forbidden');
    }
  }

  /**
   * Return true if current user is allowed to access the given MVC request
   *
   * @param AbstractRequest $request
   * @return bool
   */
  private function _isUserAllowed(AbstractRequest $request) {
    $module     = $request->getModuleName();
    $controller = $request->getControllerName();
    $action     = $request->getActionName();

    if('error' === $controller) return true;

    $resource = 'mvc:'.$module.':'.$controller.':'.$action;
    #Logger::debug(__METHOD__.':: Testing resource: '.$resource);
    #if($this->_acl->has($resource)) Logger::debug(__METHOD__.':: ACL has resource');
    if($this->_acl->has($resource) && $this->_acl->isUserAllowed($resource, 'view')) return true;

    $resource = 'mvc:'.$module.':'.$controller;
    #Logger::debug(__METHOD__.':: Testing resource: '.$resource);
    #if($this->_acl->has($resource)) Logger::debug(__METHOD__.':: ACL has resource');
    #if($this->_acl->has($resource) && $this->_acl->isUserAllowed($resource, 'view')) return true;
    if($this->_acl->has($resource)) {
      return $this->_acl->isUserAllowed($resource, 'view');
    } else {
      // Does user have global access?
      return $this->_acl->isUserAllowed('mvc:' . $module . ':all', 'view');
    }

    return false;
  }

  /**
   * Sets ACL object.
   *
   * @param Zend_Acl $acl ACL object
   * @return Rexmac\Zyndax\Controller\Plugin\Acl Provides a fluent interface
   */
  public function setAcl($acl) {
    $this->_acl = $acl;
    return $this;
  }

  /**
   * Returns ACL object.
   *
   * @return Zend_Acl ACL object
   */
  public function getAcl() {
    return $this->_acl;
  }
}
