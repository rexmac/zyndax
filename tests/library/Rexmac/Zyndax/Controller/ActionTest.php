<?php

namespace Rexmac\Zyndax\Controller;

use \ReflectionClass,
    Rexmac\Zyndax\Entity\UserTest,
    Rexmac\Zyndax\Entity\UserProfileTest,
    \Zend_Controller_Request_Http as HttpRequest,
    \Zend_Controller_Response_Cli as CliResponse,
    \Zend_Controller_Front as FrontController,
    \Zend_Registry;

class ActionTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $fc = FrontController::getInstance();
    $fc->resetInstance();
    $fc->setControllerDirectory(dirname(__FILE__), 'default');

    #\Zend_Registry::set('acl', AclTest::createTestAcl());
    $user = UserTest::createTestUser();
    $userProfile = UserProfileTest::createTestUserProfile();
    $user->setProfile($userProfile);
    $acl = $this->_getCleanMock('Rexmac\Zyndax\Acl\Acl');
    $acl->expects($this->once())
        ->method('getUser')
        ->will($this->returnValue($user));
    Zend_Registry::set('acl', $acl);
  }

  public function tearDown() {
    unset($this->controller);
  }

  public function testPostDispatch() {
    $this->controller = new TestController(
      new HttpRequest(),
      new CliResponse()
    );

    $view = $this->controller->initView();

    $this->controller->postDispatch();

    $this->assertTrue(isset($view->now));
    $this->assertTrue($view->now instanceof \DateTime);
  }

  public function testPostDispatchXmlHttpRequest() {
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

    $this->controller = new TestController(
      new HttpRequest(),
      new CliResponse(),
      array('noViewRenderer' => true)
    );

    $view = $this->controller->initView();
    $view->addHelperPath(APPLICATION_PATH . '/../library/Rexmac/Zyndax/View/Helper', 'Rexmac\Zyndax\View\Helper\\');

    $view->messages()->addMessage('foo', 'info');
    $this->controller->postDispatch();

    // Get view and check $messages array?
    $this->assertTrue(isset($view->messages));
    $this->assertTrue(isset($view->messages['info']));
    $this->assertTrue(is_array($view->messages['info']));
    $this->assertEquals('foo', $view->messages['info'][0]);
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

    $mockName = str_replace('Rexmac\Zyndax\Controller\\', '', get_class($this));

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

class TestController extends Action {
}