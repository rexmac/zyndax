<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\Session,
    Rexmac\Zyndax\Entity\SessionTest;

class SessionServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = SessionTest::createTestSession();
    SessionService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\Session e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = SessionTest::createTestSession();
    SessionService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\Session e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    SessionService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    SessionService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(SessionTest::createRandomTestSession());
    }
    self::$entityManager->flush();

    $entities = SessionService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof Session);
    }
  }

  public function testFindWithParameter() {
    $testEntity = SessionTest::createTestSession();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = SessionService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = SessionTest::createTestSession();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\Session e')->execute();
    $this->assertEquals(1, count($entities));

    SessionService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\Session e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = SessionTest::createTestSession();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    SessionService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\Session e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }

  public function testGarbageCollection() {
    $testSession = SessionTest::createOldTestSession();
    SessionService::create($testSession);
    $sessions = self::$entityManager->createQuery('SELECT e FROM ' . SessionService::getEntityClass() . ' e')->execute();
    $this->assertEquals(1, count($sessions));

    SessionService::collectGarbage(0);

    $sessions = self::$entityManager->createQuery('SELECT e FROM ' . SessionService::getEntityClass() . ' e')->execute();
    $this->assertEquals(0, count($sessions));
  }
}
