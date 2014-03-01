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

use Zend_Controller_Front as FrontController,
    Zend_Controller_Request_Abstract as AbstractRequest;

/**
 * Zend controller plugin that allows for camelCased names of controller
 * actionm methods to be properly routed.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Plugin
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class CamelCaseAction extends \Zend_Controller_Plugin_Abstract {

  /**
   * Called before Zend_Controller_Front enters its dispatch loop.
   *
   * During the dispatch loop, Zend_Controller_Front keeps a
   * Zend_Controller_Request_Abstract object, and uses
   * Zend_Controller_Dispatcher to dispatch the
   * Zend_Controller_Request_Abstract object to controllers/actions.
   *
   * @param  AbstractRequest $request
   * @return void
   */
  public function dispatchLoopStartup(AbstractRequest $request) {
    $newActionName     = self::_camelize($request->getActionName());
    #$newControllerName = self::_camelize($request->getControllerName());
    #$newModuleName     = self::_camelize($request->getModuleName());
    $request->setActionName($newActionName);
    #$request->setControllerName($newControllerName);
    #$request->setModuleName($newModuleName);
    $request->setDispatched(false);
    FrontController::getInstance()->setRequest($request);
  }

  /**
   * Convert camelCased string to hyphenated string expected by Zend_Controller_Router.
   *
   * For example, "myAction" becomes "my-action"
   *
   * @param string $str
   * @return string camelCased string in hyphenated form
   */
  private static function _camelize($str) {
    $str[0] = strtolower($str[0]);
    return preg_replace_callback('/([A-Z])/', function($c){return '-'.strtolower($c[1]);}, $str);
  }
}
