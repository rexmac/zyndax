<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\SocialNetwork,
    Rexmac\Zyndax\Entity\SocialNetworkTest;

class SocialNetworkServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = SocialNetworkTest::createTestSocialNetwork();
    SocialNetworkService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\SocialNetwork e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = SocialNetworkTest::createTestSocialNetwork();
    SocialNetworkService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\SocialNetwork e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    SocialNetworkService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    SocialNetworkService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(SocialNetworkTest::createRandomTestSocialNetwork());
    }
    self::$entityManager->flush();

    $entities = SocialNetworkService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof SocialNetwork);
    }
  }

  public function testFindWithParameter() {
    $testEntity = SocialNetworkTest::createTestSocialNetwork();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = SocialNetworkService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = SocialNetworkTest::createTestSocialNetwork();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\SocialNetwork e')->execute();
    $this->assertEquals(1, count($entities));

    SocialNetworkService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\SocialNetwork e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = SocialNetworkTest::createTestSocialNetwork();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    SocialNetworkService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\SocialNetwork e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
