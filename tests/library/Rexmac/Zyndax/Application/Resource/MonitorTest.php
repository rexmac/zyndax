<?php

namespace Rexmac\Zyndax\Application\Resource;

use \Zend_Application,
    \Zend_Loader_Autoloader,
    \Zend_Registry,
    \Zend_Session;

class MonitorTest extends \PHPUnit_Framework_Testcase {

  public function setUp() {
    Zend_Session::$_unitTestEnabled = true;
    $this->application = new Zend_Application('testing');
    require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
    $this->bootstrap = new \ZfAppBootstrap($this->application);
  }

  public function testPassingMonitorConfigurationSetsObjectState() {
    $config = array(
      'logExceptions' => true,
      'logFatalErrors' => true,
      'logJavaScriptErrors' => true
    );
    $resource = new Monitor($config);
    $monitor = $resource->init();

    // Test monitor has been stored in registry
    $this->assertEquals($monitor, Zend_Registry::get('monitor'));

    // Test getter
    $this->assertEquals($monitor, $resource->getMonitor());
  }
}
