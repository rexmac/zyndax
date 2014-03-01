<?php

class IndexControllerTest extends \Rexmac\Zyndax\Test\PHPUnit\ControllerTestCase {

  public static function setUpBeforeClass() {
    parent::$withData = true;
    parent::setUpBeforeClass();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testIndexAction() {
    $this->dispatch('/');
    $this->assertModule('default');
    $this->assertController('index');
    $this->assertAction('index');
    $this->assertRedirectTo('/user/login', 'Failed to redirect to login');
  }

/*
  public function testIndexActionAsAdmin() {
    \Zend_Registry::set('siteDomain', 'example.com');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->_loginAdmin();
    $this->assertRedirectRegex('/^http:\/\/admin\.example\.com\/user\/login\?auth=[0-9a-f]{128}$/', 'Failed to redirect admin user upon login');
    $redirectUrl = str_replace('http://admin.example.com', '', $this->getRedirectUrl());
    $this->redispatch($redirectUrl);
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());

    $session = new Zend_Session_Namespace('VPMVMA');
    $session->loggedInAsUser = '2';

    #$this->redispatch('/', false);
    $this->redispatch('/');
error_log($this->getResponse()->getBody());
error_log($this->getRedirectUrl());
    $this->assertModule('default');
    $this->assertController('index');
    $this->assertAction('home');
  }
*/
}
