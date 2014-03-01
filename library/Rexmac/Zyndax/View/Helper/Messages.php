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
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\View\Helper;

use Rexmac\Zyndax\View\Helper\Jquery as JqueryViewHelper,
    \Zend_Controller_Action_HelperBroker as HelperBroker,
    \Zend_Controller_Front as FrontController;

/**
 * Helper class for displaying messages
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Messages extends \Zend_View_Helper_Abstract {

  /**
   * Associative array of error messages and their types
   *
   * @var array
   */
  private $_messages = null;

  /**
   * Constructor
   *
   * @return Rexmac\Zyndax\View\Helper\Messages
   */
  public function __construct() {
    $this->_messages = array();
    #$r = new \ReflectionClass($this);
    #$this->_types = array_map('strtolower', array_flip($r->getConstants()));
  }

  /**
   * Returns associative array of messages
   *
   * @return array Associate array of messages
   */
  public function getMessages() {
    return $this->_messages;
  }

  /**
   * Add message
   *
   * @param  string Message
   * @param  string Message type
   * @return void
   */
  public function addMessage($message, $type = 'info') {
    if(!isset($this->_messages[$type]) || !is_array($this->_messages[$type])) {
      $this->_messages[$type] = array();
    }
    $this->_messages[$type][] = $message;
  }

  /**
   * Add messages
   *
   * @param  array Associative array of messages
   * @return void
   */
  public function addMessages(array $messages) {
    foreach($messages as $type => $message) {
      $this->addMessage($message, $type);
    }
  }

  /**
   * Helper method to display messages
   *
   * @return Rexmac\Zyndax\View\Helper\Messages
   */
  public function messages() {
    return $this;
  }

  /**
   * Helper method to display messages
   *
   * @return Rexmac\Zyndax\View\Helper\Messages
   */
  public function direct() {
    return $this;
  }

  /**
   * Display messages
   *
   * @return void
   */
  public function render() {
    // Any flash messages?
    #if(!is_array($this->view->messages)) $this->view->messages = array();
    $messages = $this->_messages + HelperBroker::getStaticHelper('SessionMessenger')->getMessages();

    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.'::THIS_MESSAGES::'.var_export($this->_messages, true));
    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.'::SESS_MESSAGES::'.var_export(HelperBroker::getStaticHelper('SessionMessenger')->getMessages(), true));
    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.'::MRGD_MESSAGES::'.var_export($messages, true));
    $request = FrontController::getInstance()->getRequest();
    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.':: '.$request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName());
    $route = $request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName();

    if($request->isXmlHttpRequest()) {
      if(!empty($messages)) {
        $this->view->messages = $messages;
      }
      return;
    }
    foreach($messages as $type => $msgs) {
      $msgs = str_replace('\'', '\\\'', $msgs);
      #$msgs = array_map('htmlentities', $msgs, array_fill(0, count($msgs), ENT_QUOTES, 'UTF-8'
      #$msgs = array_map('myfunction', $msgs);

      // I'm sure there was a very good reason for the following, but I currently have no idea what that reason was.
      // Anyways, it is preventing us from including HTML formatting in messages.
      /*$msgs = array_map(
        function($str) {
          return htmlentities($str, ENT_QUOTES, 'UTF-8');
        },
        $msgs
      );*/

      $out = "['".implode("','", $msgs)."']";
      if($request->isMobileRequest()) {
        $script = "  $('div.ui-page-active > div.ui-content').showMessage({'thisMessage': $out, 'className': '$type'});";
      } else {
        if($route === 'default/user/login') { // Probably a better way to hanlde this special-case
          $script = "  $('#content').showMessage({'thisMessage': $out, 'className': '$type'});";
        } else {
          $script = "  Notifier.{$type}({$out}.join(' '));";
        }
      }
      JqueryViewHelper::appendScript($script);
    }
    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.'::DONE::');
  }
}
