<?php

namespace Rexmac\Zyndax\Entity;

use Rexmac\Zyndax\Entity\AclResource;

class AclResourceTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id' => 1,
    'identifier' => 'test_resource_id',
    'name' => 'test resource',
  );

  public static function createTestAclResource() {
    return new AclResource(self::$testData);
  }

  public static function createRandomTestAclResource() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['identifier'] = sha1(mt_rand());
    $testData['name'] = sha1(mt_rand());
    return new AclResource($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\AclResource', new AclResource());
  }

  public function testConstructorInjectionOfProperties() {
    $resource = self::createTestAclResource();
    $expected = self::$testData;
    $this->assertEquals($expected, $resource->toArray());
  }

  public function testGetters(AclResource $resource = null) {
    if(null === $resource) $resource = self::createTestAclResource();
    $this->assertEquals(self::$testData['id'],         $resource->getId());
    $this->assertEquals(self::$testData['identifier'], $resource->getIdentifier());
    $this->assertEquals(self::$testData['name'],       $resource->getName());
  }

  public function testSetters() {
    $resource = new AclResource();
    $resource->setId(self::$testData['id']);
    $resource->setIdentifier(self::$testData['identifier']);
    $resource->setName(self::$testData['name']);

    $this->testGetters($resource);
  }
}
