<?php

namespace Rexmac\Zyndax\Auth\Adapter;

use Rexmac\Zyndax\Entity\AclRoleTest,
    Rexmac\Zyndax\Entity\UserTest,
    Rexmac\Zyndax\Service\AclRoleService,
    Rexmac\Zyndax\Service\UserService,
    Rexmac\Zyndax\Auth\AuthResult,
    \Zend_Registry;

class DoctrineAdapterTest extends \Rexmac\Zyndax\Test\PHPUnit\AclModelTestCase {

  private $_authUser = null;

  public function setUp() {
    parent::setUp();
    Zend_Registry::set('staticSalt', sha1(mt_rand()));
    $this->_authUser = UserTest::createRandomTestUser();
    $this->_authUser->setUsername('Admin');
    $this->_authUser->setPassword(UserService::encryptPassword('password', $this->_authUser->getSalt()));
    AclRoleService::create($this->_authUser->getRole());
    UserService::create($this->_authUser);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Auth\Adapter\DoctrineAdapter', new DoctrineAdapter());
  }

  public function testAuthenticateWithValidCredentials() {
    $adapter = new DoctrineAdapter();
    $result = $adapter->setIdentity('Admin')->setCredential('password')->authenticate();
    $this->assertTrue($result instanceof AuthResult);
    $this->assertEquals(AuthResult::SUCCESS, $result->getCode());
    $this->assertEquals($this->_authUser, $adapter->getUser());
  }

  public function testAuthenticateWithInvalidIdentity() {
    $adapter = new DoctrineAdapter();
    $result = $adapter->setIdentity('nobody')->setCredential('password')->authenticate();
    $this->assertTrue($result instanceof AuthResult);
    $this->assertEquals(AuthResult::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
  }

  public function testAuthenticateWithInvalidCredential() {
    $adapter = new DoctrineAdapter();
    $result = $adapter->setIdentity('Admin')->setCredential('p4ssw0rd')->authenticate();
    $this->assertTrue($result instanceof AuthResult);
    $this->assertEquals(AuthResult::FAILURE_CREDENTIAL_INVALID, $result->getCode());
  }

  public function testAuthenticateWithLockedAccount() {
    $adapter = new DoctrineAdapter();
    $this->_authUser->setLocked(1);
    $result = $adapter->setIdentity('Admin')->setCredential('password')->authenticate();
    $this->assertTrue($result instanceof AuthResult);
    $this->assertEquals(AuthResult::FAILURE_ACCOUNT_LOCKED, $result->getCode());
  }

  public function testAuthenticateWithUnverifiedAccount() {
    $adapter = new DoctrineAdapter();
    $this->_authUser->setActive(0);
    $result = $adapter->setIdentity('Admin')->setCredential('password')->authenticate();
    $this->assertTrue($result instanceof AuthResult);
    $this->assertEquals(AuthResult::FAILURE_REQUIRES_EMAIL_VERIFICATION, $result->getCode());
  }
}
