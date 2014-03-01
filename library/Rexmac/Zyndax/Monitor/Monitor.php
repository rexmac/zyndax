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
 * @subpackage Monitor
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Monitor;

use \Exception,
    Rexmac\Zyndax\Monitor\Exception as MonitorException,
    Rexmac\Zyndax\Monitor\Log\Writer as DoctrineLogWriter,
    Rexmac\Zyndax\View\Helper\Jquery as JqueryViewHelper,
    \Zend_Controller_Action_HelperBroker as HelperBroker,
    \Zend_Controller_Front as FrontController,
    \Zend_Controller_Response_Http as HttpResponse,
    \Zend_Db_Adapter_Abstract as AbstractDbAdapter,
    \Zend_Exception,
    \Zend_Registry,
    \Zend_Session;

/**
 * Class for logging of errors fromvarious sources.
 *
 * This class was inspired by and contains code from the monitorix project by
 * Markus Hausammann (?) (https://github.com/markushausammann/monitorix) and
 * released under the New BSD License.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Monitor
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Monitor extends \Zend_Log {
  /**
   * Has the shutdown function been registered?
   *
   * @var bool
   */
  private static $_shutdownRegistered = false;

  /**
   * Should exceptions be logged?
   *
   * @var bool
   */
  private $_logExceptions = false;

  /**
   * Should fatal errors be logged?
   *
   * @var bool
   */
  private $_logFatalErrors = false;

  /**
   * Should JavaScript errors be logged?
   *
   * @var bool
   */
  private $_logJavaScriptErrors = false;

  /**
   * Should slow SQL queries be logged?
   *
   * @var bool
   */
  private $_logSlowQueries = false;

  /**
   * Number of milliseconds after which a SQL query is considered a slow query.
   *
   * @var int
   */
  private $_slowQueryLimit = 1000;

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    parent::__construct(new DoctrineLogWriter());
  }

  /**
   * Return slow SQL query liit
   *
   * @return int
   */
  public function getSlowQueryLimit() {
    return $this->_slowQueryLimit;
  }

  /**
   * Toggle logging of exceptions
   *
   * @param bool $toggle Should exceptions be logged? Default is TRUE.
   * @return Monitor
   */
  public function logExceptions($toggle = true) {
    $this->_logExceptions = $toggle;
    $this->_registerControllerPlugin('\Rexmac\Zyndax\Monitor\Controller\Plugin\Exceptions', $toggle);
  }

  /**
   * Toggle logging of fatal errors
   *
   * @param bool $toggle Should fatal errors be logged? Default is TRUE.
   * @return Monitor
   */
  public function logFatalErrors($toggle = true) {
    $this->_logFatalErrors = $toggle;
    if(!self::$_shutdownRegistered) {
      if(!Zend_Session::$_unitTestEnabled) register_shutdown_function(array($this, 'shutdownFunction'));
      self::$_shutdownRegistered = true;
    }
    return $this;
  }

  /**
   * Toggle logging of JavaScript errors
   *
   * @todo Toggle implies ability to undo, but there is currently no way of unappending script to Jquery view helper
   * @param bool $toggle Should JavaScript errors be logged? Default is TRUE.
   * @return Monitor
   */
  public function logJavaScriptErrors($toggle = true) {
    $this->_logJavaScriptErrors = $toggle;

    if($toggle) {
      $viewRenderer = HelperBroker::getStaticHelper('viewRenderer');
      if(null === $viewRenderer->view) {
        try { $viewRenderer->init(); }
        catch(Zend_Exception $e) {
          throw new MonitorException('Could not init() viewRenderer.');
        }
      }

      $view = $viewRenderer->view;
      if(false === $view->getPluginLoader('helper')->getPaths('Rexmac\Zyndax\View\Helper')) {
        try {
          $view->addHelperPath('Rexmac/Zyndax/View/Helper', 'Rexmac\Zyndax\View\Helper');
        } catch(Zend_Exception $e) {
          throw new MonitorException('Failed to add Rexmac\Zyndax\View\Helper path to view', null, $e);
        }
      }

      JqueryViewHelper::appendScript(
        'window.onerror=function(message,errorUrl,errorLine){'
        . '$.ajax({type:\'post\',url:\'?monitor=x\',dataType:\'html\','
        . 'data:{\'message\':message,\'errorUrl\':errorUrl,\'errorLine\':errorLine}})}'
      );
      /*JqueryViewHelper::appendScript('$("<a/>").appendTo($("body")).text("CLICK ME").click(function(e){
        e.preventDefault();
        alert("HELLO!" + window.parseNog());
      });');*/
    }
    $this->_registerControllerPlugin('\Rexmac\Zyndax\Monitor\Controller\Plugin\JavaScriptErrors', $toggle);

    return $this;
  }

  /**
   * Toggle logging of slow DB queries.
   *
   * @param array $adapters Array of Zend_Db adapters
   * @param int $limit (ms) Anything slower is considered a slow query.
   * @param bool $toggle Whether or not to log slow DB queries. Default is true.
   * @return Monitor
   */
  public function logSlowQueries(array $adapters, $limit = null, $toggle = true) {
    $this->_logSlowQueries = $toggle;
    if(null !== $limit) $this->_slowQueryLimit = (int) $limit;
    $profilers = array();
    foreach($adapters as $adapter) {
      $profiler = $adapter->getProfiler()->setEnabled($toggle);
      if($toggle) {
        $profilers[] = $profiler;
      }
    }

    if(count($profilers) > 0) {
      Zend_Registry::set('monitorProfilers', $profilers);
    }

    $this->_registerControllerPlugin('\Rexmac\Zyndax\Monitor\Controller\Plugin\SlowQueries', $toggle);

    return $this;
  }

  /**
   * Write log entry to DB
   *
   * @param string|Zend_Controller_Response_Http $input
   * @param int $priority
   * @param string $logType
   */
  public function writeLog($input, $priority = self::DEBUG, $logType = null) {
    if($input instanceof HttpResponse) {
      $exceptions = $input->getException();
      foreach($exceptions as $exception) {
        $message = $exception->getMessage();
        $extraFields = $this->_getExtraFieldsArray($exception);
        parent::log($message, self::CRIT, $extraFields);
      }
    } else {
      if($input === null) throw new Exception('unknown');
      parent::log($input, $priority, $this->_getExtraFieldsArray($logType));
    }
  }

  /**
   * Callback function to log fatal errors.
   *
   * Not intended to be caled directly. For use with
   * register_shutdown_function(), which is done by the logFatalErrors method.
   * Would be private if possible.
   *
   * @return void
   */
  public function shutdownFunction() {
    if(!$this->_logFatalErrors) return;
    $error = error_get_last();
    #$this->handleError($error['type'], $error['message'], $error['file'], $error['line'], 'Last error before shutdown. Fatal or syntax.');
    #if(!(error_reporting() & $error['type'])) return;
    if(null === $error) return;
    parent::log(
      $error['message'],
      self::CRIT,
      array(
        'logType'     => 'php_error',
        #'applicationName' => $this->_applicationName,
        #'environment' => APPLICATION_ENV,
        'errno'       => $error['type'],
        'file'        => $error['file'],
        'line'        => $error['line'],
        'context'     => json_encode('Last error before shutdown. Fatal or syntax'),
        'stackTrace'  => json_encode(debug_backtrace(false))
      )
    );
  }

  /**
   * Maps given information to array of extra fields.
   *
   * @param string|Exception $input
   * @return array
   */
  private function _getExtraFieldsArray($input = null) {
    if(null === $input) return array();

    $extraFields = array(
      'logType'         => is_string($input) ? $input : 'log',
      #'applicationName' => $this->_applicationName,
      #'environment'     => APPLICATION_ENV
    );

    if($input instanceof Exception) {
      $extraFields['logType'] = 'exception';
      $extraFields['errno'] = $input->getCode();
      $extraFields['file']  = $input->getFile();
      $extraFields['line']  = $input->getLine();
      #$extraFields['context']  = 'unknown';
      $extraFields['stackTrace'] = json_encode($input->getTrace());
    }

    return $extraFields;
  }

  /**
   * Retrieve the lowest free index of the front controller plugin stack that
   * is above the minimum given index.
   *
   * @param int $minIndex Lowest stack index to use
   */
  private function _getLowestFreeStackIndex($minIndex) {
    $plugins = array_keys(FrontController::getInstance()->getPlugins());
    sort($plugins);
    $highestIndex = array_pop($plugins);
    if($highestIndex < $minIndex) return $minIndex;
    return $highestIndex + 1;
  }

  /**
   * Register front controller plugin
   *
   * @param string $name Name of plugin
   * @param bool $toggle
   * @return void
   */
  private function _registerControllerPlugin($name, $toggle) {
    $fc = FrontController::getInstance();
    if($toggle) {
      $fc->registerPlugin(new $name, $this->_getLowestFreeStackIndex(101));
    } else {
      $fc->unregisterPlugin($name);
    }
  }
}
