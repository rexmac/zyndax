<?php

namespace Rexmac\Zyndax\Entity;

use Rexmac\Zyndax\Entity\AclRole;

class AclRoleTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'          => 1,
    'name'        => 'test_role_name',
    'description' => 'test_role_description',
  );

  public static function createTestAclRole($name = 'test_role_name') {
    $role = new AclRole(self::$testData);
    $role->setName($name);
    return $role;
  }

  public static function createRandomTestAclRole() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['name'] = sha1(mt_rand());
    return new AclRole($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\AclRole', new AclRole());
  }

  public function testConstructorInjectionOfProperties() {
    $role = self::createTestAclRole();
    $expected = self::$testData;
    $this->assertEquals($expected, $role->toArray());
  }

  public function testGetters(AclRole $role = null) {
    if(null === $role) $role = self::createTestAclRole();
    $this->assertEquals(self::$testData['id'], $role->getId());
    $this->assertEquals(self::$testData['name'], $role->getName());
    $this->assertEquals(self::$testData['description'], $role->getDescription());
  }

  public function testSetters() {
    $role = new AclRole();
    $role->setId(self::$testData['id']);
    $role->setName(self::$testData['name']);
    $role->setDescription(self::$testData['description']);

    $this->testGetters($role);
  }
}
