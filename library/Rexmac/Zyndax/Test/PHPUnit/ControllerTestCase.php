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
 * @subpackage Test_PHPUnit
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Test\PHPUnit;

use Doctrine\ORM\Query\ResultSetMapping,
    Doctrine\ORM\Tools\SchemaTool,
    Rexmac\Zyndax\Controller\Request\HttpTestCase as HttpRequestTestCase,
    Rexmac\Zyndax\Controller\Response\HttpTestCase as HttpResponseTestCase,
    Rexmac\Zyndax\Doctrine\Service,
    Rexmac\Zyndax\Service\AclPermissionService,
    Rexmac\Zyndax\Service\AclResourceService,
    Rexmac\Zyndax\Service\AclRoleService,
    Rexmac\Zyndax\Service\TimeZoneService,
    Rexmac\Zyndax\Service\UserProfileService,
    Rexmac\Zyndax\Service\UserService;

/**
 * Functional testing scaffold for MVC applications
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Test_PHPUnit
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @codeCoverageIgnore
 */
class ControllerTestCase extends \Zend_Test_PHPUnit_ControllerTestCase {

  /**
   * Doctrine entity manager
   *
   * @var Doctrine\ORM\EntityManager
   */
  protected static $entityManager = null;

  /**
   * Entity class metadata
   *
   * @var array
   */
  protected static $metadata = array();

  /**
   * Application
   *
   * @var Zend_Application
   */
  protected static $app = null;

  /**
   * Load test DB iwth test data?
   *
   * @var bool
   */
  public static $withData = true;

  /**
   * Set up MVC app
   *
   * @return void
   */
  public static function setUpBeforeClass() {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    // Prepare test DB
    self::prepareDb();
    if(self::$withData) self::insertTestData();
    parent::setUpBeforeClass();
  }

  /**
   * Tear down MVC app
   *
   * @return void
   */
  public static function tearDownAfterClass() {
    parent::tearDownAfterClass();
  }

  /**
   * Setup method
   *
   * @return void
   */
  public function setUp() {
    #$this->bootstrap = self::$app;
    if(null === $this->bootstrap) {
      $this->bootstrap = new \Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
    }
    \Zend_Auth::getInstance()->clearIdentity();

    parent::setUp();
  }

  /**
   * Tear down
   *
   * @return void
   */
  public function tearDown() {
    $this->reset();
    \Zend_Auth::getInstance()->clearIdentity();
    \Zend_Registry::_unsetInstance();
    #\Zend_Controller_Action_HelperBroker::resetHelpers();
    \Rexmac\Zyndax\View\Helper\Jquery::clearScript();
    set_include_path(APPLICATION_PATH.':'.APPLICATION_PATH.'/../library:.:/usr/local/lib/php');
  }

  /**
   * Assert that response body contains a string.
   *
   * @param string $string String to search body for
   * @param string $message [Optional] Message to display for false assertion
   * @return bool TRUE if response body contains string
   */
  public function assertBodyContains($string = '', $message = null) {
    $body = $this->getResponse()->getBody();
    if($string === '') return $this->assertTrue($body === '', $message);
    return $this->assertTrue(strpos($body, $string) !== false, $message);
  }

  /**
   * Assert that response body matches regular expression.
   *
   * @param string $pattern Regular expression
   * @return bool TRUE if response body matches regular expression
   */
  public function assertBodyContainsRegex($pattern = '') {
    $body = $this->getResponse()->getBody();
    if($pattern === '') return $this->assertTrue($body === '');
    return $this->assertTrue(preg_match($pattern, $body));
  }

  /**
   * Return URL response was redirect to (if any).
   *
   * @return string URL that response was redirected to (if any)
   */
  public function getRedirectUrl() {
    $headers = $this->getResponse()->sendHeaders();
    return str_replace('Location: ', '', $headers['location']);
  }

  /**
   * Retrieve test case request object
   *
   * @return Zend_Controller_Request_Abstract
   */
  public function getRequest() {
    if(null === $this->_request) {
      $this->_request = new HttpRequestTestCase();
    }
    return $this->_request;
  }

  /**
   * Retrieve test case response object
   *
   * @return Zend_Controller_Response_Abstract
   */
  public function getResponse() {
    if(null === $this->_response) {
      $this->_response = new HttpResponseTestCase();
    }
    return $this->_response;
  }

  /**
   * Reset the request object
   *
   * Useful for test cases that need to test multiple trips to the server.
   *
   * @return ControllerTestCase
   */
  public function resetRequest() {
    if(false !== ($aclPlugin = \Zend_Controller_Front::getInstance()->getPlugin('Rexmac\Zyndax\Controller\Plugin\Acl'))) {
      $aclPlugin->setAcl(null);
    }
    if($this->_request instanceof HttpRequestTestCase) {
      $this->_request->clearQuery()->clearPost();
    }
    $this->_request = null;
    return $this;
  }

  /**
   * Reset request and response, then dispatch.
   *
   * @param  string|null $url
   * @param  bool $resetRequest Whether or not to reset request. Default is TRUE.
   * @param  bool $resetResponse Whether or not to reset response. Default is TRUE.
   * @return void
   */
  public function redispatch($url = null, $resetRequest = true, $resetResponse = true) {
    if($resetRequest) $this->resetRequest();
    if($resetResponse) $this->resetResponse();
    $this->dispatch($url);
  }

  /**
   * Return CSRF value from form
   *
   * @return string CSRF value
   */
  protected function _getFormCsrf() {
    if(preg_match('/name="csrf" value="([^"]+)" /', $this->getResponse()->getBody(), $matches)) {
      return $matches[1];
    }
    return null;
  }

  /**
   * Login as test admin
   *
   * @return void
   */
  protected function _loginAdmin() {
    $this->_loginUser('admin', 'admin');
  }

  /**
   * Login as test user
   *
   * @return void
   */
  protected function _loginTestUser() {
    $this->_loginUser('testuser', 'testuser');
  }

  /**
   * Login as test user
   *
   * @param  string $username User's username
   * @param  string $password User's password
   * @param  string $csrf     CSRF value
   * @param  bool   $ajax     True if AJAX request
   * @return void
   */
  protected function _loginUser($username='foobar', $password='foobar', $csrf='', $ajax = false) {
    /*
    $params = array(
      'module'     => 'default',
      'controller' => 'user',
      'action'     => 'login'
    );
    $url = $this->url($this->urlizeOptions($params));
    $this->dispatch($url);
    */
    $this->dispatch('/user/login');

    if($csrf == '') $csrf = $this->_getFormCsrf();
    $this->resetResponse();

    if(true === $ajax) {
      $this->_request->setHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
    }

    $postData = array(
      'username' => $username,
      'password' => $password,
      'login'    => 'Login',
      'csrf'     => $csrf
    );
    $this->_request->setPost($postData);
    $this->_request->setMethod('POST');

    // Login fails because the form's hash/csrf element expects the hash to be stored
    // in session var, but there are no sessions between unit tests.
    // So, perhaps we manually create the session var before dispatching the POST request?
    $sessionName = 'Zend_Form_Element_Hash_salt_csrf';
    $session = new \Zend_Session_Namespace($sessionName);
    $session->hash = $csrf;

    $this->dispatch('/user/login'.(true === $ajax ? '/format/json' : ''));
  }

  /**
   * Get class metadata
   *
   * @param string $path Path to class files
   * @param string $namespace Namespace of classes
   * @return array Class metadata
   */
  private static function getClassMetas($path, $namespace) {
    $metadata = array();
    if($handle = opendir($path)) {
      while(false !== ($file = readdir($handle))) {
        if(preg_match('/\.php$/', $file)) {
          list($class) = explode('.', $file);
          $metadata[] = self::$entityManager->getClassMetadata($namespace.$class);
        }
      }
    }
    self::$metadata = array_merge(self::$metadata, $metadata);
    return $metadata;
  }

  /**
   * Prepare DB for tests by loading in predefined test data
   *
   * @return void
   */
  private static function prepareDb() {
    $app = new \Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
    $app->getBootstrap()->bootstrap('doctrine');
    $app->getBootstrap()->bootstrap('registry');
    self::$entityManager = $app->getBootstrap()->getResource('doctrine');
    self::$metadata = array();

    // Drop existing schema
    $params = self::$entityManager->getConnection()->getParams();
    if(file_exists($params['path'])) unlink($params['path']);

    // Add BLOB data type mapping
    self::$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('BLOB', 'gzblob');
    // Add IP data type mapping
    self::$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('IP', 'ip');

    // Create schema
    $tool = new SchemaTool(self::$entityManager);
    $metas = array_merge(
      self::getClassMetas(APPLICATION_PATH.'/../library/Rexmac/Zyndax/Entity', 'Rexmac\Zyndax\Entity\\'),
      self::getClassMetas(APPLICATION_PATH.'/../library/Rexmac/Zyndax/Monitor/Entity', 'Rexmac\Zyndax\Monitor\Entity\\')
    );
    $tool->createSchema($metas);
  }

  /**
   * Insert test data into test DB.
   *
   * @return void
   */
  private static function insertTestData() {
    // Insert test data
    $roles = array(
      'admin' => AclRoleService::create(array('name' => 'Administrator', 'description' => 'Site Administrator')),
      'user'  => AclRoleService::create(array('name' => 'User', 'description' => 'Regular user')),
      'guest' => AclRoleService::create(array('name' => 'Guest', 'description' => 'Anonymous guest')),
    );

    $resources = array(
      'default'    => AclResourceService::create(array('identifier' => 'mvc:default:all', 'name' => 'Global non-admin access')),
      'userLogin'  => AclResourceService::create(array('identifier' => 'mvc:default:user:login', 'name' => 'User login')),
      'admin'      => AclResourceService::create(array('identifier' => 'mvc:admin', 'name' => 'Admin interface')),
      #'adminIndex' => AclResourceService::create(array('identifier' => 'mvc:admin:all')),
    );
    AclPermissionService::create(array('role' => $roles['guest'], 'resource' => $resources['default'], 'name' => 'view'));
    AclPermissionService::create(array('role' => $roles['guest'], 'resource' => $resources['userLogin'], 'name' => 'view'));
    AclPermissionService::create(array('role' => $roles['admin'], 'resource' => $resources['admin'], 'name' => 'view'));
    #AclPermissionService::create(array('role' => $roles['admin'], 'resource' => $resources['adminIndex'], 'name' => 'view'));
    $userData = array(array(
      'username'  => 'admin',
      'firstName' => 'admin',
      'lastName'  => 'istrator',
      'role'      => $roles['admin'],
    ), array(
      'username'  => 'testuser',
      'firstName' => 'test',
      'lastName'  => 'er',
      'role'      => $roles['user'],
    ));
    $timeZone = TimeZoneService::create(array(
      'name' => 'America/Los_Angeles'
    ));
    $users = array();
    foreach($userData as $u) {
      $user = UserService::create(array(
        'role'        => $u['role'],
        #'profile'     => $profile,
        'username'    => $u['username'],
        'password'    => $u['username'],
        'email'       => $u['username'].'@example.com',
        'dateCreated' => new \DateTime(),
        'lastConnect' => new \DateTime(),
        'active'      => 1,
        'locked'      => 0
      ));
      $user->setPassword(UserService::encryptPassword($user->getPassword()));

      $profile = UserProfileService::create(array(
        'user'      => $user,
        'firstName' => $u['firstName'],
        'lastName'  => $u['lastName'],
        'phone'     => '408-555-5555',
        'website'   => '',
        'timeZone'  => $timeZone
      ));
      $user->setProfile($profile);

      #UserService::update();
      #UserProfileService::update();
      $users[$u['username']] = $user;
    }
  }
}
