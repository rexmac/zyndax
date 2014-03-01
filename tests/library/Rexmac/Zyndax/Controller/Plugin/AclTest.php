<?php

namespace Rexmac\Zyndax\Controller\Plugin;

use \ReflectionClass,
    Rexmac\Zyndax\Acl\Acl,
    Rexmac\Zyndax\Controller\Plugin\Acl as AclPlugin,
    Rexmac\Zyndax\Controller\Response\HttpTestCase,
    \Zend_Acl_Resource,
    \Zend_Acl_Role,
    \Zend_Controller_Front,
    \Zend_Controller_Request_Http,
    \Zend_Registry;

class AclTest extends \PHPUnit_Framework_TestCase {

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
   * ACL plugin
   * @var Rexmac\Zyndax\Controller\Plugin\Acl
   */
  public $plugin;

  public function setUp() {
    $fc = Zend_Controller_Front::getInstance();
    $fc->resetInstance();
    $this->request  = new Zend_Controller_Request_Http();
    $this->response = new HttpTestCase();
    $fc->setRequest($this->request)
       ->setResponse($this->response);
    $this->plugin   = new AclPlugin();
    $this->plugin->setRequest($this->request);
    $this->plugin->setResponse($this->response);

    Zend_Registry::set('siteDomain', 'example.com');

    $this->acl  = $this->_getCleanMock('Rexmac\Zyndax\Acl\Acl');
    $this->user = $this->_getCleanMock('Rexmac\Zyndax\Entity\User');
    $this->role = $this->_getCleanMock('Rexmac\Zyndax\Entity\AclRole');
  }

  public function testAclGetterAndSetter() {
    $this->plugin->setAcl($this->acl);
    $this->assertEquals($this->acl, $this->plugin->getAcl());
  }

  public function testPreDispatchForNonExistentResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(false));
    $this->acl->expects($this->at(2))
              ->method('has')
              ->with('mvc:foo:bar')
              ->will($this->returnValue(false));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('error', $this->request->getControllerName());
    $this->assertEquals('forbidden', $this->request->getActionName());
  }

  public function testPreDispatchForAllowedModuleControllerActionResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(2))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar:baz', 'view')
              ->will($this->returnValue(true));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('foo', $this->request->getModuleName());
    $this->assertEquals('bar', $this->request->getControllerName());
    $this->assertEquals('baz', $this->request->getActionName());
  }

  public function testPreDispatchForDisallowedModuleControllerActionResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(2))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar:baz', 'view')
              ->will($this->returnValue(false));
    $this->acl->expects($this->at(3))
              ->method('has')
              ->with('mvc:foo:bar')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(4))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar', 'view')
              ->will($this->returnValue(false));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('error', $this->request->getControllerName());
    $this->assertEquals('forbidden', $this->request->getActionName());
  }

  public function testPreDispatchForAllowedModuleControllerResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(2))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar:baz', 'view')
              ->will($this->returnValue(false));
    $this->acl->expects($this->at(3))
              ->method('has')
              ->with('mvc:foo:bar')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(4))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar', 'view')
              ->will($this->returnValue(true));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('foo', $this->request->getModuleName());
    $this->assertEquals('bar', $this->request->getControllerName());
    $this->assertEquals('baz', $this->request->getActionName());
  }

  public function testPreDispatchForDisallowedModuleControllerResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(2))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar:baz', 'view')
              ->will($this->returnValue(false));
    $this->acl->expects($this->at(3))
              ->method('has')
              ->with('mvc:foo:bar')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(4))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar', 'view')
              ->will($this->returnValue(false));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('error', $this->request->getControllerName());
    $this->assertEquals('forbidden', $this->request->getActionName());
  }

  public function testPreDispatchForAllowedModuleResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(2))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar:baz', 'view')
              ->will($this->returnValue(false));
    $this->acl->expects($this->at(3))
              ->method('has')
              ->with('mvc:foo:bar')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(4))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar', 'view')
              ->will($this->returnValue(true));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('foo', $this->request->getModuleName());
    $this->assertEquals('bar', $this->request->getControllerName());
    $this->assertEquals('baz', $this->request->getActionName());
  }

  public function testPreDispatchForDisallowedModuleResource() {
    $this->role->expects($this->once())
               ->method('__call')
               ->with('getName')
               ->will($this->returnValue('Guest'));
    $this->user->expects($this->once())
               ->method('__call')
               ->with('getRole')
               ->will($this->returnValue($this->role));
    $this->acl->expects($this->at(0))
              ->method('getUser')
              ->will($this->returnValue($this->user));
    $this->acl->expects($this->at(1))
              ->method('has')
              ->with('mvc:foo:bar:baz')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(2))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar:baz', 'view')
              ->will($this->returnValue(false));
    $this->acl->expects($this->at(3))
              ->method('has')
              ->with('mvc:foo:bar')
              ->will($this->returnValue(true));
    $this->acl->expects($this->at(4))
              ->method('isUserAllowed')
              ->with('mvc:foo:bar', 'view')
              ->will($this->returnValue(false));
    $this->plugin->setAcl($this->acl);

    $this->request->setModuleName('foo')
                  ->setControllerName('bar')
                  ->setActionName('baz');
    $this->plugin->preDispatch($this->request);

    $this->assertEquals('default', $this->request->getModuleName());
    $this->assertEquals('error', $this->request->getControllerName());
    $this->assertEquals('forbidden', $this->request->getActionName());
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
