<?php

namespace Rexmac\Zyndax\Controller\Action\Helper;

use \Zend_Controller_Front as FrontController,
    \Zend_Controller_Response_Cli as CliResponse,
    \Zend_Controller_Request_Http as HttpRequest,
    \Zend_Session;

class SessionMessengerTest extends \PHPUnit_Framework_TestCase {

  private $controller;

  private $front;

  private $helper;

  private $request;

  private $response;

  public function setUp() {
    $this->front = FrontController::getInstance();
    $this->front->resetInstance();
    #$this->front->setControllerDirectory(dirname(__FILE__), 'default');
    $this->front->setControllerDirectory(dirname(__FILE__));
    #$this->front->setControllerDirectory(APPLICATION_PATH.'/controllers', 'default');
    $this->front->returnResponse(true);

    Zend_Session::$_unitTestEnabled = true;

    $this->request    = new HttpRequest();
    #$this->request->setControllerName('session-messenger-test');
    #$this->request->setControllerName('sessionMessengerTest');
    #$this->request->setModuleName('default');
    $this->request->setControllerName('sessionmessengertest');
    #$this->request->setActionName('index');
    $this->response   = new CliResponse();
    $this->controller = new SessionMessengerTestController($this->request, $this->response, array());
    $this->helper     = new SessionMessenger($this->controller);
  }

  public function testLoadSessionMessenger() {
    $this->markTestSkipped();
    $response = $this->front->dispatch($this->request);
    #$response = $this->front->dispatch($this->request, $this->response);
    $this->assertEquals('Rexmac\Zyndax\Controller\Action\Helper\SessionMessenger123456', $response->getBody());
  }
}
