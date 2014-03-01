<?php

namespace Rexmac\Zyndax\Entity;

use \DateInterval,
    \DateTime,
    Rexmac\Zyndax\Entity\UserEmailVerification,
    Rexmac\Zyndax\Entity\UserTest;

class UserEmailVerificationTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'    => 1,
    'user'  => null,
    'token' => null,
    'requestDate' => null,
  );

  public function setUp() {
    parent::setUp();
    self::$testData['user'] = UserTest::createTestUser();
    self::$testData['token'] = sha1(mt_rand());
    self::$testData['requestDate'] = new DateTime();
  }

  public static function createTestEmailVerification() {
    self::$testData['user'] = UserTest::createTestUser();
    self::$testData['token'] = sha1(mt_rand());
    self::$testData['requestDate'] = new DateTime();
    return new UserEmailVerification(self::$testData);
  }

  public static function createRandomTestEmailVerification() {
    $testData = self::$testData;
    $testData['user']  = UserTest::createRandomTestUser();
    $testData['token'] = sha1(mt_rand());
    $testData['requestDate'] = new DateTime();
    return new UserEmailVerification($testData);
  }

  public static function createOldTestEmailVerification() {
    $testData = self::$testData;
    $date = new DateTime();
    $date->sub(new DateInterval('P2D'));
    $testData['user']  = UserTest::createRandomTestUser();
    $testData['token'] = sha1(mt_rand());
    $testData['requestDate'] = $date;
    return new UserEmailVerification($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserEmailVerification', new UserEmailVerification());
  }

  public function testConstructorInjectionOfProperties() {
    $emailVerification = self::createTestEmailVerification();
    $expected = self::$testData;
    $this->assertEquals($expected, $emailVerification->toArray());
  }

  public function testGetters(UserEmailVerification $emailVerification = null) {
    if(null === $emailVerification) $emailVerification = self::createTestEmailVerification();
    $this->assertEquals(self::$testData['id'],      $emailVerification->getId());
    $this->assertEquals(self::$testData['name'],    $emailVerification->getName());
    $this->assertEquals(self::$testData['locked'],  $emailVerification->getLocked());
    $this->assertEquals(self::$testData['token'],   $emailVerification->getToken());
  }

  public function testSetters() {
    $emailVerification = new UserEmailVerification();
    $emailVerification->setId(self::$testData['id']);
    $emailVerification->setName(self::$testData['name']);
    $emailVerification->setLocked(self::$testData['locked']);
    $emailVerification->setToken(self::$testData['token']);

    $this->testGetters($emailVerification);
  }
}
