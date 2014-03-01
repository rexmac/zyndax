<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Entity\UserPasswordResetToken,
    Rexmac\Zyndax\Entity\UserPasswordResetTokenTest;

class UserPasswordResetTokenServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  public function testConstructFromArray() {
    $testEntity = UserPasswordResetTokenTest::createTestPasswordResetToken();
    UserPasswordResetTokenService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserPasswordResetTokenTest::createTestPasswordResetToken();
    UserPasswordResetTokenService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserPasswordResetTokenService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserPasswordResetTokenService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserPasswordResetTokenTest::createRandomTestPasswordResetToken());
    }
    self::$entityManager->flush();

    $entities = UserPasswordResetTokenService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof UserPasswordResetToken);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserPasswordResetTokenTest::createTestPasswordResetToken();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserPasswordResetTokenService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserPasswordResetTokenTest::createTestPasswordResetToken();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(1, count($entities));

    UserPasswordResetTokenService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserPasswordResetTokenTest::createTestPasswordResetToken();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserPasswordResetTokenService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }

  public function testGarbageCollection() {
    $testPasswordResetToken = UserPasswordResetTokenTest::createOldTestPasswordResetToken();
    $user = $testPasswordResetToken->getUser();
    self::$entityManager->persist($user->getRole());
    self::$entityManager->persist($user);
    self::$entityManager->flush();

    UserPasswordResetTokenService::create($testPasswordResetToken);
    $tokens = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(1, count($tokens));

    UserPasswordResetTokenService::collectGarbage();

    $tokens = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(0, count($tokens));
  }

  public function testClearTokensForUser() {
    $testPasswordResetToken = UserPasswordResetTokenTest::createRandomTestPasswordResetToken();
    UserPasswordResetTokenService::create($testPasswordResetToken);
    $user1 = $testPasswordResetToken->getUser();

    $testPasswordResetToken = UserPasswordResetTokenTest::createRandomTestPasswordResetToken();
    UserPasswordResetTokenService::create($testPasswordResetToken);
    $user2 = $testPasswordResetToken->getUser();

    $tokens = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(2, count($tokens));

    UserPasswordResetTokenService::clearTokensForUser($user2);

    $tokens = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\UserPasswordResetToken e')->execute();
    $this->assertEquals(1, count($tokens));
  }
}
