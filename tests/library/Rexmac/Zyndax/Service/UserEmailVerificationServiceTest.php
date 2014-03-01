<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserEmailVerification,
    Rexmac\Zyndax\Entity\UserEmailVerificationTest;

class UserEmailVerificationServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserEmailVerificationTest::createTestEmailVerification();
    UserEmailVerificationService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserEmailVerificationTest::createTestEmailVerification();
    UserEmailVerificationService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserEmailVerificationService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserEmailVerificationService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserEmailVerificationTest::createRandomTestEmailVerification());
    }
    self::$entityManager->flush();

    $entities = UserEmailVerificationService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserEmailVerification);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserEmailVerificationTest::createTestEmailVerification();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserEmailVerificationService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserEmailVerificationTest::createTestEmailVerification();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();
    $this->assertEquals(1, count($entities));

    UserEmailVerificationService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserEmailVerificationTest::createTestEmailVerification();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserEmailVerificationService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }

  public function testGarbageCollection() {
    $testEmailVerification = UserEmailVerificationTest::createOldTestEmailVerification();
    $user = $testEmailVerification->getUser();
    #self::$entityManager->persist($user->getRole());
    #self::$entityManager->persist($user);
    #self::$entityManager->flush();

    UserEmailVerificationService::create($testEmailVerification);
    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();
    $this->assertEquals(1, count($entities));

    UserEmailVerificationService::collectGarbage();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserEmailVerification e')->execute();
    $this->assertEquals(0, count($entities));
  }
}
