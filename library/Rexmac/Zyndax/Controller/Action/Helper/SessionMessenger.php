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
 * @subpackage Controller_Action_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Controller\Action\Helper;

use \Zend_Session_Namespace;

/**
 * Zend controller action helper for managing messages stored in session
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Action_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class SessionMessenger extends \Zend_Controller_Action_Helper_Abstract implements \IteratorAggregate, \Countable {

  /**
   * Messages from previous request
   *
   * @var array
   */
  static protected $_messages = array();

  /**
   * Zend_Session storage object
   *
   * @var Zend_Session
   */
  static protected $_session = null;

  /**
   * Wether a message has been previously added
   *
   * @var boolean
   */
  static protected $_messageAdded = false;

  /**
   * Instance namespace, default is 'default'
   *
   * @var string
   */
  protected $_namespace = 'default';

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    if(!self::$_session instanceof Zend_Session_Namespace) {
      self::$_session = new Zend_Session_Namespace($this->getName());
      foreach(self::$_session as $namespace => $messages) {
        self::$_messages[$namespace] = $messages;
        unset(self::$_session->{$namespace});
      }
    }
  }

  /**
   * Set the session property
   *
   * @param Zend_Session_Namespace $session
   * @return void
   */
  public static function setSession(Zend_Session_Namespace $session) {
    self::$_session = $session;
  }

  /**
   * Resets the namespace in case we have forwarded to a different action
   * SessionMessenger will be 'clean' (default namespace)
   *
   * @return Rexmac\Zyndax\Controller\Action\Helper\SessionMessenger Provides a fluent interface
   */
  public function postDispatch() {
    $this->resetNamespace();
    return $this;
  }

  /**
   * Changes the namespace messages are added to, useful for per action
   * controller messaging between requests
   *
   * @param  string $namespace Namespace
   * @return Rexmac\Zyndax\Controller\Action\Helper\SessionMessenger Provides a fluent interface
   */
  public function setNamespace($namespace = 'default') {
    $this->_namespace = $namespace;
    return $this;
  }

  /**
   * Resets the namespace to the default
   *
   * @return Rexmac\Zyndax\Controller\Action\Helper\SessionMessenger Provides a fluent interface
   */
  public function resetNamespace() {
    $this->setNamespace();
    return $this;
  }

  /**
   * Adds a message
   *
   * @param  string $message Message
   * @param  int    $type Message type
   * @return \Rexmac\Controller\Action\Helper\SessionMessenger Provides a fluent interface
   */
  public function addMessage($message, $type = 'info') {
    if(self::$_messageAdded === false) {
      self::$_session->setExpirationHops(1, null, true);
    }

    if(!is_array(self::$_session->{$this->_namespace})) {
      self::$_session->{$this->_namespace} = array();
    }
    if(!isset(self::$_session->{$this->_namespace}[$type]) || !is_array(self::$_session->{$this->_namespace}[$type])) {
      self::$_session->{$this->_namespace}[$type] = array();
    }
    self::$_session->{$this->_namespace}[$type][] = $message;

    return $this;
  }

  /**
   * Adds multiple messages
   *
   * @param  array $messages Messages
   * @param  int   $type Message type
   * @return \Rexmac\Controller\Action\Helper\SessionMessenger Provides a fluent interface
   */
  public function addMessages(array $messages, $type = 'info') {
    foreach($messages as $message) {
      $this->addMessage($message, $type);
    }
  }

  /**
   * Wether a specific namespace has messages
   *
   * @return boolean
   */
  public function hasMessages() {
    return isset(self::$_messages[$this->_namespace]);
  }

  /**
   * Return array of messages from a specific namespace
   *
   * @return array
   */
  public function getMessages() {
    if($this->hasMessages()) {
      return self::$_messages[$this->_namespace];
    }

    return array();
  }

  /**
   * Clear all messages from the previous request & current namespace
   *
   * @return bool TRUE if messages were cleared, FALSE if none existed
   */
  public function clearMessages() {
    if($this->hasMessages()) {
      unset(self::$_messages[$this->_namespace]);
      return true;
    }

    return false;
  }

  /**
   * Complete the IteratorAggregate interface; for iterating
   *
   * @return ArrayObject
   */
  public function getIterator() {
    if($this->hasMessages()) {
      return new ArrayObject($this->getMessages());
    }
    return new ArrayObject();
  }

  /**
   * Complete the countable interface
   *
   * @return int
   */
  public function count() {
    if($this->hasMessages()) {
      return count($this->getMessages());
    }
    return 0;
  }

  /**
   * Strategy pattern: proxy to addMessage()
   *
   * @param  string $message Message
   * @param  int    $type    Type of message
   * @return \Rexmac\Controller\Action\Helper\SessionMessenger Provides a fluent interface
   */
  public function direct($message, $type = 'info') {
    return $this->addMessage($message, $type);
  }
}
