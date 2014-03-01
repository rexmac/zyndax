<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\TimeZone,
    Rexmac\Zyndax\Entity\TimeZoneTest;

class TimeZoneServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = TimeZoneTest::createTestTimeZone();
    TimeZoneService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\TimeZone e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = TimeZoneTest::createTestTimeZone();
    TimeZoneService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\TimeZone e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    TimeZoneService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    TimeZoneService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TimeZoneTest::createRandomTestTimeZone());
    }
    self::$entityManager->flush();

    $entities = TimeZoneService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof TimeZone);
    }
  }

  public function testFindWithParameter() {
    $testEntity = TimeZoneTest::createTestTimeZone();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = TimeZoneService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = TimeZoneTest::createTestTimeZone();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\TimeZone e')->execute();
    $this->assertEquals(1, count($entities));

    TimeZoneService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\TimeZone e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = TimeZoneTest::createTestTimeZone();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    TimeZoneService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\TimeZone e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
