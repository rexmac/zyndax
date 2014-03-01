<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\AclResource,
    Rexmac\Zyndax\Entity\AclResourceTest;

class AclResourceServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\AclModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = AclResourceTest::createTestAclResource();
    AclResourceService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclResource e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = AclResourceTest::createTestAclResource();
    AclResourceService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclResource e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    AclResourceService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    AclResourceService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(AclResourceTest::createRandomTestAclResource());
    }
    self::$entityManager->flush();

    $entities = AclResourceService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof AclResource);
    }
  }

  public function testFindWithParameter() {
    $testEntity = AclResourceTest::createTestAclResource();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = AclResourceService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = AclResourceTest::createTestAclResource();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclResource e')->execute();
    $this->assertEquals(1, count($entities));

    AclResourceService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclResource e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = AclResourceTest::createTestAclResource();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    AclResourceService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclResource e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
