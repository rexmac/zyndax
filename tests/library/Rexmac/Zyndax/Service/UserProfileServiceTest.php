<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserProfile,
    Rexmac\Zyndax\Entity\UserProfileTest;

class UserProfileServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserProfileTest::createTestUserProfile();
    UserProfileService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserProfile e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserProfileTest::createTestUserProfile();
    UserProfileService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserProfile e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserProfileService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserProfileService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      #$user = UserTest::createRandomTestUser();
      #$user->setId($i+1);
      #self::$entityManager->persist($user); // Persisting user also persists profile
      #self::$entityManager->persist(UserProfileTest::createRandomTestProfile($user));
      self::$entityManager->persist(UserProfileTest::createRandomTestUserProfile());
    }
    self::$entityManager->flush();

    $entities = UserProfileService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserProfile);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserProfileTest::createTestUserProfile();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserProfileService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserProfileTest::createTestUserProfile();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserProfile e')->execute();
    $this->assertEquals(1, count($entities));

    UserProfileService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserProfile e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserProfileTest::createTestUserProfile();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserProfileService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserProfile e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }
}
