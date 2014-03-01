<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserSocialNetworkIdentity,
    Rexmac\Zyndax\Entity\UserSocialNetworkIdentityTest;

class UserSocialNetworkIdentityServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserSocialNetworkIdentityTest::createTestIdentity();
    UserSocialNetworkIdentityService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserSocialNetworkIdentity e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserSocialNetworkIdentityTest::createTestIdentity();
    UserSocialNetworkIdentityService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserSocialNetworkIdentity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserSocialNetworkIdentityService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserSocialNetworkIdentityService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserSocialNetworkIdentityTest::createRandomTestIdentity());
    }
    self::$entityManager->flush();

    $entities = UserSocialNetworkIdentityService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserSocialNetworkIdentity);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserSocialNetworkIdentityTest::createTestIdentity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserSocialNetworkIdentityService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserSocialNetworkIdentityTest::createTestIdentity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserSocialNetworkIdentity e')->execute();
    $this->assertEquals(1, count($entities));

    UserSocialNetworkIdentityService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserSocialNetworkIdentity e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserSocialNetworkIdentityTest::createTestIdentity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserSocialNetworkIdentityService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserSocialNetworkIdentity e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
