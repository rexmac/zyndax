<?php

namespace Rexmac\Zyndax\Acl;

use Rexmac\Zyndax\Entity\AclPermission,
    Rexmac\Zyndax\Entity\AclPermissionTest,
    Rexmac\Zyndax\Entity\AclResourceTest,
    Rexmac\Zyndax\Entity\AclRoleTest,
    Rexmac\Zyndax\Entity\UserTest,
    \Zend_Registry,
    \Zend_Session;

class AclTest extends \Rexmac\Zyndax\Test\PHPUnit\AclModelTestCase {

  public function setUp() {
    parent::setUp();

    $this->guestRole = AclRoleTest::createRandomTestAclRole()->setName('Guest');
    $this->modRole   = AclRoleTest::createRandomTestAclRole()->setName('Moderator');
    $this->adminRole = AclRoleTest::createRandomTestAclRole()->setName('Administrator');
    self::$entityManager->persist($this->guestRole);
    self::$entityManager->persist($this->modRole);
    self::$entityManager->persist($this->adminRole);
    $adminIndexResource = AclResourceTest::createRandomTestAclResource()->setIdentifier('mvc:admin:index');
    $adminFoobarResource = AclResourceTest::createRandomTestAclResource()->setIdentifier('mvc:admin:foobar');
    $adminGlobalResource = AclResourceTest::createRandomTestAclResource()->setIdentifier('mvc:admin:all');
    self::$entityManager->persist($adminIndexResource);
    self::$entityManager->persist($adminFoobarResource);
    self::$entityManager->persist($adminGlobalResource);
    self::$entityManager->persist(new AclPermission(array(
      'role'     => $this->modRole,
      'resource' => $adminIndexResource,
      'name'     => 'view'
    )));
    self::$entityManager->persist(new AclPermission(array(
      'role'     => $this->adminRole,
      'resource' => $adminGlobalResource,
      'name'     => 'view'
    )));

    self::$entityManager->flush();

    Zend_Session::$_unitTestEnabled = true;
  }

  public static function createTestAcl() {
    $aclTest = new AclTest();
    $aclTest::setUp();
    return new Acl();
  }

  public function testInitUserWithoutUser() {
    $this->acl = new Acl();
    $this->acl->initUser();
  }

  public function testInitUserWithUser() {
    $user = UserTest::createRandomTestUser();
    $user->setRole($this->guestRole);
    $this->acl = new Acl();
    $this->acl->initUser($user);
    $this->assertEquals($user->getUsername(), $this->acl->getUser()->getUsername());
  }

  public function testIsUserAllowedWithInvalidUser() {
    $this->acl = new Acl();
    $this->assertFalse($this->acl->isUserAllowed('mvc:admin:index', 'view'));
  }

  public function testIsUserAllowedWithValidUserAndAllowedResource() {
    $modUser = UserTest::createRandomTestUser();
    $modUser->setUsername('Mod');
    $modUser->setRole($this->modRole);
    self::$entityManager->persist($modUser);
    self::$entityManager->flush();

    $authIdentity = new \stdClass();
    $authIdentity->id   = $modUser->getId();
    $authIdentity->username = $modUser->getUsername();
    Zend_Registry::set('user', $modUser);

    $this->acl = new Acl();
    $this->assertTrue($this->acl->isUserAllowed('mvc:admin:index', 'view'), 'Mod user can view admin index resource');
  }

  public function testIsUserAllowedWithValidUserAndDisallowedResource() {
    $modUser = UserTest::createRandomTestUser();
    $modUser->setUsername('Mod');
    $modUser->setRole($this->modRole);
    self::$entityManager->persist($modUser);
    self::$entityManager->flush();

    $authIdentity = new \stdClass();
    $authIdentity->id   = $modUser->getId();
    $authIdentity->username = $modUser->getUsername();
    Zend_Registry::set('user', $modUser);

    $this->acl = new Acl();
    $this->assertFalse($this->acl->isUserAllowed('mvc:admin:foobar', 'view'), 'Mod user can view admin index resource');
  }

  public function testIsUserAllowedWithValidUserAndGlobalResource() {
    $adminUser = UserTest::createRandomTestUser();
    $adminUser->setUsername('Admin');
    $adminUser->setRole($this->adminRole);
    self::$entityManager->persist($adminUser);
    self::$entityManager->flush();

    $authIdentity = new \stdClass();
    $authIdentity->id   = $adminUser->getId();
    $authIdentity->username = $adminUser->getUsername();
    Zend_Registry::set('user', $adminUser);

    $this->acl = new Acl();
    $this->assertTrue($this->acl->isUserAllowed('mvc:admin:foobar', 'view'), 'Admin user can view all admin resources');
  }
}
