<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\AclRole,
    Rexmac\Zyndax\Entity\AclRoleTest;

class AclRoleServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\AclModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = AclRoleTest::createTestAclRole();
    AclRoleService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclRole e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = AclRoleTest::createTestAclRole();
    AclRoleService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclRole e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    AclRoleService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    AclRoleService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(AclRoleTest::createRandomTestAclRole());
    }
    self::$entityManager->flush();

    $entities = AclRoleService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof AclRole);
    }
  }

  public function testFindWithParameter() {
    $testEntity = AclRoleTest::createTestAclRole();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = AclRoleService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = AclRoleTest::createTestAclRole();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclRole e')->execute();
    $this->assertEquals(1, count($entities));

    AclRoleService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclRole e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = AclRoleTest::createTestAclRole();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    AclRoleService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\AclRole e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
