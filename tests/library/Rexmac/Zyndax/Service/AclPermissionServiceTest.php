<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\AclPermission,
    Rexmac\Zyndax\Entity\AclPermissionTest;

class AclPermissionServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\AclModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = AclPermissionTest::createTestAclPermission();
    AclPermissionService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclPermission e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = AclPermissionTest::createTestAclPermission();
    AclPermissionService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclPermission e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    AclPermissionService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    AclPermissionService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(AclPermissionTest::createRandomTestAclPermission());
    }
    self::$entityManager->flush();

    $entities = AclPermissionService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof AclPermission);
    }
  }

  public function testFindWithParameter() {
    $testEntity = AclPermissionTest::createTestAclPermission();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = AclPermissionService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = AclPermissionTest::createTestAclPermission();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclPermission e')->execute();
    $this->assertEquals(1, count($entities));

    AclPermissionService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclPermission e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = AclPermissionTest::createTestAclPermission();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    AclPermissionService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclPermission e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
