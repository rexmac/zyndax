<?php

namespace Rexmac\Zyndax\Controller\Plugin;

use Rexmac\Zyndax\Controller\Plugin\CamelCaseAction as CamelCaseActionPlugin,
    \Zend_Controller_Front,
    \Zend_Controller_Request_HttpTestCase,
    \Zend_Controller_Response_HttpTestCase;

class CamelCaseTest extends \PHPUnit_Framework_TestCase {

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
   * CamelCaseAction plugin
   * @var Rexmac\Zyndax\Controller\Plugin\CamelCaseAction
   */
  public $plugin;

  public function setUp() {
    $fc = Zend_Controller_Front::getInstance();
    $fc->resetInstance();
    $this->request  = new Zend_Controller_Request_HttpTestCase();
    $this->response = new Zend_Controller_Response_HttpTestCase();
    $fc->setRequest($this->request)
       ->setResponse($this->response);
    $this->plugin   = new CamelCaseActionPlugin();
    $this->plugin->setRequest($this->request);
    $this->plugin->setResponse($this->response);
  }

  public function testDispatchLoopStartupWithNonCamelCasedRequest() {
    $this->request
      ->setModuleName('foo')
      ->setControllerName('bar')
      ->setActionName('baz');

    $this->plugin->dispatchLoopStartup($this->request);

    $this->assertEquals('foo', $this->request->getModuleName());
    $this->assertEquals('bar', $this->request->getControllerName());
    $this->assertEquals('baz', $this->request->getActionName());
  }

  public function testDispatchLoopStartupWithCamelCasedRequest() {
    $this->request
      ->setModuleName('foo')
      ->setControllerName('bar')
      ->setActionName('bazLig');

    $this->plugin->dispatchLoopStartup($this->request);

    $this->assertEquals('foo', $this->request->getModuleName());
    $this->assertEquals('bar', $this->request->getControllerName());
    $this->assertEquals('baz-lig', $this->request->getActionName());
  }
}
