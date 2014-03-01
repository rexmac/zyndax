<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserEditEvent,
    Rexmac\Zyndax\Entity\UserEditEventTest;

class UserEditEventServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserEditEventTest::createTestUserEditEvent();
    UserEditEventService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEditEvent e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserEditEventTest::createTestUserEditEvent();
    UserEditEventService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEditEvent e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserEditEventService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserEditEventService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserEditEventTest::createRandomTestUserEditEvent());
    }
    self::$entityManager->flush();

    $entities = UserEditEventService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserEditEvent);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserEditEventTest::createTestUserEditEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserEditEventService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserEditEventTest::createTestUserEditEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEditEvent e')->execute();
    $this->assertEquals(1, count($entities));

    UserEditEventService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEditEvent e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserEditEventTest::createTestUserEditEvent();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserEditEventService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEditEvent e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
