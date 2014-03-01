<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserLoginEvent,
    Rexmac\Zyndax\Entity\UserLoginEventTest;

class UserLoginEventServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserLoginEventTest::createTestUserLoginEvent();
    UserLoginEventService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginEvent e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserLoginEventTest::createTestUserLoginEvent();
    UserLoginEventService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginEvent e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserLoginEventService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserLoginEventService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserLoginEventTest::createRandomTestUserLoginEvent());
    }
    self::$entityManager->flush();

    $entities = UserLoginEventService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserLoginEvent);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserLoginEventTest::createTestUserLoginEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserLoginEventService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserLoginEventTest::createTestUserLoginEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginEvent e')->execute();
    $this->assertEquals(1, count($entities));

    UserLoginEventService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginEvent e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserLoginEventTest::createTestUserLoginEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserLoginEventService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserLoginEvent e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
