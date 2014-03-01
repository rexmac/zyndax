<?php

namespace Rexmac\Zyndax\Entity;

use Rexmac\Zyndax\Entity\AclPermission,
    Rexmac\Zyndax\Entity\AclResourceTest,
    Rexmac\Zyndax\Entity\AclRoleTest;

class AclPermissionTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'       => 1,
    'role'     => null,
    'resource' => null,
    'name'     => 'test_permission_name'
  );

  public function setUp() {
    self::$testData['resource'] = AclResourceTest::createTestAclResource();
    self::$testData['role']     = AclRoleTest::createTestAclRole();
  }

  public static function createTestAclPermission() {
    $testData = self::$testData;
    $testData['resource'] = AclResourceTest::createTestAclResource();
    $testData['role']     = AclRoleTest::createTestAclRole();
    return new AclPermission($testData);
  }

  public static function createRandomTestAclPermission() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['name']    .= substr(sha1(mt_rand()), 0, 4);
    $testData['token']    = sha1(mt_rand());
    $testData['resource'] = AclResourceTest::createRandomTestAclResource();
    $testData['role']     = AclRoleTest::createRandomTestAclRole();
    return new AclPermission($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\AclPermission', new AclPermission());
  }

  public function testConstructorInjectionOfProperties() {
    $permission = new AclPermission(self::$testData);
    $expected = self::$testData;
    $this->assertEquals($expected, $permission->toArray());
  }

  public function testGetters(AclPermission $permission = null) {
    if(null === $permission) $permission = new AclPermission(self::$testData);
    $this->assertEquals(self::$testData['id'],       $permission->getId());
    $this->assertEquals(self::$testData['role'],     $permission->getRole());
    $this->assertEquals(self::$testData['resource'], $permission->getResource());
    $this->assertEquals(self::$testData['name'],     $permission->getName());
  }

  public function testSetters() {
    $permission = new AclPermission();
    $permission->setId(self::$testData['id']);
    $permission->setRole(self::$testData['role']);
    $permission->setResource(self::$testData['resource']);
    $permission->setName(self::$testData['name']);

    $this->testGetters($permission);
  }
}
