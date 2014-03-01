<?php

namespace Rexmac\Zyndax\Controller\Plugin;

use \ReflectionClass,
    Rexmac\Zyndax\Controller\Plugin\Auth as AuthPlugin,
    \Zend_Auth,
    \Zend_Auth_Storage_NonPersistent,
    \Zend_Config,
    \Zend_Controller_Action_HelperBroker,
    \Zend_Controller_Front,
    \Zend_Controller_Request_HttpTestCase,
    \Zend_Controller_Response_HttpTestCase,
    \Zend_Registry,
    \Zend_Session;

class AuthTest extends \PHPUnit_Framework_TestCase {

  /**
   * Request object
   * @var Zend_Controller_Request_Http
   */
  public $request;

  /**
   * Response object
   * @var Zend_Controller_Response_Http
   */
  public $response;

  /**
   * Auth plugin
   * @var Rexmac\Zyndax\Controller\Plugin\Auth
   */
  public $plugin;

  public function setUp() {
    $fc = Zend_Controller_Front::getInstance();
    $fc->resetInstance();
    $this->request  = new Zend_Controller_Request_HttpTestCase();
    $this->response = new Zend_Controller_Response_HttpTestCase();
    $fc->setRequest($this->request)
       ->setResponse($this->response);
    $this->plugin   = new AuthPlugin();
    $this->plugin->setRequest($this->request);
    $this->plugin->setResponse($this->response);

    Zend_Registry::set('config', new Zend_Config(array(
      'auth' => array(
        'whitelist' => array(
          'default/foo/index',
          'foo/bar/baz'
        )
      )
    )));

    #$this->dispatcher = $this->_getCleanMock('Zend_Controller_Dispatcher_Standard');
    #$fc->setDispatcher($this->dispatcher);
    $fc->getDispatcher()->setControllerDirectory(array(
      'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
      'foo'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Foo'
    ));

    Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_NonPersistent());
    Zend_Session::$_unitTestEnabled = true;
    Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setExit(false);
  }

  public function testRouteStartup() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('home');
    #Zend_Auth::getInstance()->getStorage()->write(true);

    $this->plugin->routeStartup($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('home', $this->request->getActionName());
  }

  public function testPreDispatchNonExistentModule() {
    $this->request->setModuleName('baz');

    $this->plugin->preDispatch($this->request);

    $this->assertTrue($this->response->isRedirect());
    $responseHeaders = $this->response->getHeaders();
    $this->assertEquals('/user/login', $responseHeaders[0]['value']);
  }

/*
  public function testPreDispatchNonExistentModuleWithAuthenticatedSession() {
    $this->request->setModuleName('baz');

    Zend_Auth::getInstance()->getStorage()->write(true);
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('error', $this->request->getControllerName());
    $this->assertEquals('error', $this->request->getActionName());
  }
*/

  /**
   * @expectedException Zend_Controller_Dispatcher_Exception
   * @expectedExceptionMessage Invalid controller specified (foobar)
   */
  public function testPreDispatchNonExistentController() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foobar')
      ->setActionName('bar');

    $this->plugin->preDispatch($this->request);
  }

  /**
   * @expectedException Zend_Controller_Action_Exception
   * @expectedExceptionMessage Action "foo" does not exist
   */
  public function testPreDispatchNonExistentAction() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('foo');

    $this->plugin->preDispatch($this->request);
  }

  public function testPreDispatchWhitelistedAction() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('index');

    #$this->dispatcher
    #  ->expects($this->once())
    #  ->method('isDispatchable')
    #  ->with($this->request)
    #  ->will($this->returnValue(true));

    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('index', $this->request->getActionName());
  }

  public function testPreDispatchNonWhitelistedAction() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('home');

    $this->plugin->preDispatch($this->request);

    $this->assertTrue($this->response->isRedirect());
    $responseHeaders = $this->response->getHeaders();
    $this->assertEquals('/user/login', $responseHeaders[0]['value']);
  }

  public function testPreDispatchNonWhitelistedActionViaAjaxRequest() {
    $this->request
      ->setHeader('X_REQUESTED_WITH', 'XMLHttpRequest')
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('home');

    $this->plugin->preDispatch($this->request);

    $this->assertEquals(500, $this->response->getHttpResponseCode());
    $this->assertEquals(json_encode(array('redirect' => '/user/login')), $this->response->getBody());
  }

  public function testPreDispatchNonWhitelistedActionWithAuthenticatedSession() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('home');
    Zend_Auth::getInstance()->getStorage()->write(true);

    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('home', $this->request->getActionName());
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
