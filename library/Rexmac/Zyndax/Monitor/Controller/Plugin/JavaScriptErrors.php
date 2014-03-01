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
 * @subpackage Monitor_Controller_Plugin
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Monitor\Controller\Plugin;

use \Zend_Controller_Request_Abstract as AbstractRequest,
    \Zend_Controller_Request_Http as HttpRequest,
    \Zend_Log,
    \Zend_Registry;

/**
 * Zend controller plugin that intercepts XmlHttpRequests for logging
 * purposes.
 *
 * This class was inspired by and contains code from the monitorix project by
 * Markus Hausammann (?) (https://github.com/markushausammann/monitorix) and
 * released under the New BSD License.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Monitor_Controller_Plugin
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class JavaScriptErrors extends \Zend_Controller_Plugin_Abstract {

  /**
   * Called before Zend_Controller_Front begins evaluating the
   * request against its routes.
   *
   * @param AbstractRequest $request
   * @return void
   */
  public function routeStartup(AbstractRequest $request) {
    if(!($request instanceof HttpRequest)) return;

    if($request->getQuery('monitor') === 'x' && $request->isXmlHttpRequest()) {
      $message = "A javascript error was detected.\n"
        . "================================\n"
        . 'Message: ' . $request->getPost('message', '') . "\n"
        . 'URI: ' . $request->getPost('errorUrl', 'unknown') . "\n"
        . 'Line: ' . $request->getPost('errorLine', 'unknown') . "\n";
      Zend_Registry::get('monitor')->writeLog($message, Zend_Log::WARN, 'javascript-error');

      // Immediately return empty response
      $this->getResponse()->setBody('')->sendResponse();
      exit();
    }
  }
}
