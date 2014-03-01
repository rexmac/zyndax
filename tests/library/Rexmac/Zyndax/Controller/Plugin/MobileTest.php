<?php

namespace Rexmac\Zyndax\Controller\Plugin;

use Mockery,
    \ReflectionClass,
    Rexmac\Zyndax\Controller\Plugin\Mobile as MobilePlugin,
    Rexmac\Zyndax\Controller\Request\HttpRequest,
    \Zend_Controller_Front,
    \Zend_Controller_Response_HttpTestCase,
    \Zend_Layout;

class MobileTest extends \PHPUnit_Framework_TestCase {

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
    $this->request  = new HttpRequest();
    $this->response = new Zend_Controller_Response_HttpTestCase();
    $fc->setRequest($this->request)
       ->setResponse($this->response);
    $this->plugin   = new MobilePlugin();
    $this->plugin->setRequest($this->request);
    $this->plugin->setResponse($this->response);
    Zend_Layout::startMvc();
    $this->layout = Zend_Layout::getMvcInstance();
  }

  public function testDispatchLoopStartupWithNonMobilRequest() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('bar');

    $_SERVER['SERVER_NAME'] = 'www.domain.tld';

    $this->plugin->dispatchLoopStartup($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('bar', $this->request->getActionName());
    $this->assertEquals('layout', $this->layout->getLayout());
  }

  public function testDispatchLoopStartupWithMobileRequest() {
    $this->request
      ->setModuleName('default')
      ->setControllerName('foo')
      ->setActionName('bar');

    $_SERVER['SERVER_NAME'] = 'mobile.domain.tld';

    $this->plugin->dispatchLoopStartup($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('foo', $this->request->getControllerName());
    $this->assertEquals('bar', $this->request->getActionName());
    $this->assertEquals('mobile', $this->layout->getLayout());
  }
}
