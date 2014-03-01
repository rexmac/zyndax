<?php

namespace Rexmac\Zyndax\Entity;

use \DateInterval,
    \DateTime,
    Rexmac\Zyndax\Entity\UserPasswordResetToken,
    Rexmac\Zyndax\Entity\UserTest;

class UserPasswordResetTokenTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'      => 1,
    'user'    => null,
    'token'   => null,
    'requestDate' => null,
  );

  public function setUp() {
    parent::setUp();
    self::$testData['user'] = UserTest::createTestUser();
    self::$testData['token'] = sha1(mt_rand());
    self::$testData['requestDate'] = new DateTime();
  }

  public static function createTestPasswordResetToken() {
    self::$testData['user'] = UserTest::createTestUser();
    self::$testData['token'] = sha1(mt_rand());
    self::$testData['requestDate'] = new DateTime();
    return new UserPasswordResetToken(self::$testData);
  }

  public static function createRandomTestPasswordResetToken() {
    $testData = self::$testData;
    $testData['user']  = UserTest::createRandomTestUser();
    $testData['token'] = sha1(mt_rand());
    $testData['requestDate'] = new DateTime();
    return new UserPasswordResetToken($testData);
  }

  public static function createOldTestPasswordResetToken() {
    $testData = self::$testData;
    $date = new DateTime();
    $date->sub(new DateInterval('P2D'));
    $testData['user']  = UserTest::createRandomTestUser();
    $testData['token'] = sha1(mt_rand());
    $testData['requestDate'] = $date;
    return new UserPasswordResetToken($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserPasswordResetToken', new UserPasswordResetToken());
  }

  public function testConstructorInjectionOfProperties() {
    $passwordResetToken = self::createTestPasswordResetToken();
    $expected = self::$testData;
    $this->assertEquals($expected, $passwordResetToken->toArray());
  }

  public function testGetters(UserPasswordResetToken $passwordResetToken = null) {
    if(null === $passwordResetToken) $passwordResetToken = self::createTestPasswordResetToken();
    $this->assertEquals(self::$testData['id'],      $passwordResetToken->getId());
    $this->assertEquals(self::$testData['name'],    $passwordResetToken->getName());
    $this->assertEquals(self::$testData['locked'],  $passwordResetToken->getLocked());
    $this->assertEquals(self::$testData['token'],   $passwordResetToken->getToken());
  }

  public function testSetters() {
    $passwordResetToken = new UserPasswordResetToken();
    $passwordResetToken->setId(self::$testData['id']);
    $passwordResetToken->setName(self::$testData['name']);
    $passwordResetToken->setLocked(self::$testData['locked']);
    $passwordResetToken->setToken(self::$testData['token']);

    $this->testGetters($passwordResetToken);
  }
}
