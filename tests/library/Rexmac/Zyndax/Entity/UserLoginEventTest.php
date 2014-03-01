<?php

namespace Rexmac\Zyndax\Entity;

use \DateTime,
    Rexmac\Zyndax\Entity\UserLoginEvent,
    Rexmac\Zyndax\Entity\UserTest;

class UserLoginEventTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'   => 1,
    'user' => null,
    'date' => null,
    'ip'   => '127.0.0.1'
  );

  public function setUp() {
  }

  public static function createTestUserLoginEvent() {
    self::$testData['user'] = UserTest::createRandomTestUser();
    self::$testData['date'] = new DateTime();
    return new UserLoginEvent(self::$testData);
  }

  public static function createRandomTestUserLoginEvent() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['user'] = UserTest::createRandomTestUser();
    $testData['date'] = new DateTime();
    return new UserLoginEvent($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserLoginEvent', new UserLoginEvent());
  }

  public function testConstructorInjectionOfProperties() {
    $loginEvent = self::createTestUserLoginEvent();
    $expected = self::$testData;
    $this->assertEquals($expected, $loginEvent->toArray());
  }

  public function testGetters(UserLoginEvent $loginEvent = null) {
    if(null === $loginEvent) $loginEvent = self::createTestUserLoginEvent();
    $this->assertEquals(self::$testData['id'],   $loginEvent->getId());
    $this->assertEquals(self::$testData['user'], $loginEvent->getUser());
    $this->assertEquals(self::$testData['date'], $loginEvent->getDate());
    $this->assertEquals(self::$testData['ip'],   $loginEvent->getIp());
  }

  public function testSetters() {
    $loginEvent = new UserLoginEvent();
    $loginEvent->setId(self::$testData['id']);
    $loginEvent->setUser(self::$testData['user']);
    $loginEvent->setDate(self::$testData['date']);
    $loginEvent->setIp(self::$testData['ip']);

    $this->testGetters($loginEvent);
  }
}
