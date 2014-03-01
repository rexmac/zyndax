<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserLoginAsEvent,
    Rexmac\Zyndax\Entity\UserLoginAsEventTest;

class UserLoginAsEventServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserLoginAsEventTest::createTestUserLoginAsEvent();
    UserLoginAsEventService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginAsEvent e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserLoginAsEventTest::createTestUserLoginAsEvent();
    UserLoginAsEventService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginAsEvent e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserLoginAsEventService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserLoginAsEventService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserLoginAsEventTest::createRandomTestUserLoginAsEvent());
    }
    self::$entityManager->flush();

    $entities = UserLoginAsEventService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserLoginAsEvent);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserLoginAsEventTest::createTestUserLoginAsEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserLoginAsEventService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserLoginAsEventTest::createTestUserLoginAsEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginAsEvent e')->execute();
    $this->assertEquals(1, count($entities));

    UserLoginAsEventService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginAsEvent e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserLoginAsEventTest::createTestUserLoginAsEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserLoginAsEventService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginAsEvent e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
