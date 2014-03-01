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
 * @subpackage Log
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Log;

require_once APPLICATION_PATH.'/../library/Zend/Log.php';
require_once APPLICATION_PATH.'/../library/Zend/Log/Filter/Interface.php';
require_once APPLICATION_PATH.'/../library/Zend/Log/Filter/Abstract.php';
require_once APPLICATION_PATH.'/../library/Zend/Log/Filter/Priority.php';
require_once APPLICATION_PATH.'/../library/Zend/Log/Writer/Abstract.php';
require_once APPLICATION_PATH.'/../library/Zend/Log/Writer/Stream.php';

use \Zend_Log,
    \Zend_Log_Writer_Stream,
    \Zend_Registry;

/**
 * Convenience class for easy file logging.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Log
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Logger {

  /**
   * Logger object
   *
   * @var Zend\Log\Logger
   */
  private $_log;

  /**
   * Singleton isntance
   *
   * @var Rexmac\Zyndax\Log\Logger
   */
  private static $_instance = null;

  /**
   * Optional PHP stream to log to
   *
   * @var null|stream
   */
  public static $logStream = null;

  /**
   * Returns static Rexmac\Zyndax\Log\Logger instance
   *
   * @return Rexmac\Zyndax\Log\Logger
   */
  public static function getInstance() {
    if(null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Logs a message at priorty EMERG
   *
   * @todo Look into replacing priority methods with __callstatic
   * @param string $message
   * @return void
   */
  public static function emerg($message) {
    self::getInstance()->getLog()->emerg($message);
  }

  /**
   * Logs a message at priorty ALERT
   *
   * @param  string $message
   * @return void
   */
  public static function alert($message) {
    self::getInstance()->getLog()->alert($message);
  }

  /**
   * Logs a message at priorty CRIT
   *
   * @param  string $message
   * @return void
   */
  public static function crit($message) {
    self::getInstance()->getLog()->crit($message);
  }

  /**
   * Logs a message at priorty ERR
   *
   * @param  string $message
   * @return void
   */
  public static function err($message) {
    self::getInstance()->getLog()->err($message);
  }

  /**
   * Logs a message at priorty WARN
   *
   * @param  string $message
   * @return void
   */
  public static function warn($message) {
    self::getInstance()->getLog()->warn($message);
  }

  /**
   * Logs a message at priorty NOTICE
   *
   * @param  string $message
   * @return void
   */
  public static function notice($message) {
    self::getInstance()->getLog()->notice($message);
  }

  /**
   * Logs a message at priorty INFO
   *
   * @param  string $message
   * @return void
   */
  public static function info($message) {
    self::getInstance()->getLog()->info($message);
  }

  /**
   * Logs a message at priorty DEBUG
   *
   * @param  string $message
   * @return void
   */
  public static function debug($message) {
    self::getInstance()->getLog()->debug($message);
  }

  /**
   * Constructor
   *
   * @return Rexmac\Zyndax\Log\Logger
   */
  private function __construct() {
    if(null !== self::$logStream) {
      $this->_log = new Zend_Log(new Zend_Log_Writer_Stream(self::$logStream));
    } elseif(Zend_Registry::isRegistered('log')) {
      // @codeCoverageIgnoreStart
      $this->_log = Zend_Registry::get('log');
    } else { // @codeCoverageIgnoreEnd
      $this->_log = new Zend_Log(new Zend_Log_Writer_Stream('/tmp/log'), 'a');
    }

    $this->_log->setEventItem('pid', getmypid());
  }

  /**
   * Return Zend\Log\Logger object
   *
   * @return Zend\Log\Logger
   */
  public function getLog() {
    return $this->_log;
  }
}
