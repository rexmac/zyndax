<?php

namespace Rexmac\Zyndax\Controller\Plugin;

use Mockery,
    \ReflectionClass,
    Rexmac\Zyndax\Controller\Plugin\Layout as LayoutPlugin,
    \Zend_Controller_Front,
    \Zend_Controller_Request_HttpTestCase,
    \Zend_Controller_Response_HttpTestCase,
    \Zend_Layout;

class MockModuleBootstrap extends \Zend_Application_Module_Bootstrap {}

class LayoutTest extends \PHPUnit_Framework_TestCase {

  /**
   * Request object
   * @var Zend_Controller_Request_HttpTestCase
   */
  public $request;

  /**
   * Response object
   * @var Zend_Controller_Response_HttpTestCase
   */
  public $response;

  /**
   * Layout plugin
   * @var Rexmac\Zyndax\Controller\Plugin\Layout
   */
  public $plugin;

  /**
   * MVC Layout
   * @var Zend_Layout
   */
  public $layout;

  public function setUp() {
    $fc = Zend_Controller_Front::getInstance();
    $fc->resetInstance();
    $this->request  = new Zend_Controller_Request_HttpTestCase();
    $this->response = new Zend_Controller_Response_HttpTestCase();
    $fc->setRequest($this->request)
       ->setResponse($this->response);
    $this->plugin   = new LayoutPlugin();
    $this->plugin->setRequest($this->request);
    $this->plugin->setResponse($this->response);
    Zend_Layout::startMvc();
    $this->layout = Zend_Layout::getMvcInstance();
  }

  public function tearDown() {
    Mockery::close();
  }

  public function testDispatchLoopStartupDefaultModuleRequest() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('bar');

    $this->plugin->dispatchLoopStartup($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('bar', $this->request->getActionName());
    $this->assertEquals('', $this->layout->getLayoutPath());
  }

  public function testDispatchLoopStartupWithAdminModuleRequest() {
    $mockApp = Mockery::mock('Zend_Application');
    $mockApp->shouldReceive('hasOption')->twice()->andReturn(false);
    #$mockApp->shouldReceive('getOptions')->once()->andReturn(array());
    $mockModuleBootstrap = Mockery::mock(new MockModuleBootstrap($mockApp));
    $mockModuleBootstrap->shouldReceive('getOptions')->once()->andReturn(array(
      'resources' => array(
        'layout' => array(
          'layoutPath' => 'path_to_admin_layouts'
        )
      )
    ));
    $mockModulesResource = Mockery::mock('Zend_Application_Resource_Modules');
    $mockModulesResource->shouldReceive('offsetGet')->once()->andReturn($mockModuleBootstrap);

    $mockAppBootstrap = $this->_getCleanMock('Zend_Application_Bootstrap_Bootstrap');
    $mockAppBootstrap->expects($this->once())->method('getResource')->with('modules')->will($this->returnValue($mockModulesResource));
    #$mockAppBootstrap = Mockery::mock('Zend_Application_Bootstrap_Bootstrap');
    #$mockAppBootstrap->shouldReceive('getResource')->once()->andReturn($mockModulesResource);
    Zend_Controller_Front::getInstance()->setParam('bootstrap', $mockAppBootstrap);

    $this->request
      ->setModuleName('admin')
      ->setControllerName('foo')
      ->setActionName('bar');

    $this->plugin->dispatchLoopStartup($this->request);

    $this->assertEquals('admin', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('bar', $this->request->getActionName());
    $this->assertEquals('path_to_admin_layouts', $this->layout->getLayoutPath());
  }

  private function _getCleanMock($className) {
    $class = new ReflectionClass($className);
    $methods = $class->getMethods();
    $stubMethods = array();
    foreach($methods as $method) {
      if($method->isPublic() || ($method->isProtected() && $method->isAbstract())) {
        $stubMethods[] = $method->getName();
      }
    }

    $mockName = str_replace('Rexmac\Zyndax\Controller\Plugin\\', '', get_class($this));

    $mocked = $this->getMock(
      '\\'.$className,
      $stubMethods,
      array(),
      $mockName . 'Mock_' . uniqid(),
      false
    );
    return $mocked;
  }
}
