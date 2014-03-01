<?php

namespace Rexmac\Zyndax\Entity;

use \DateTime,
    Rexmac\Zyndax\Entity\UserLoginAsEvent,
    Rexmac\Zyndax\Entity\UserTest;

class UserLoginAsEventTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'      => 1,
    'user'    => null,
    'account' => null,
    'date'    => null,
    'ip'      => '127.0.0.1'
  );

  public function setUp() {
  }

  public static function createTestUserLoginAsEvent() {
    self::$testData['user'] = UserTest::createRandomTestUser();
    self::$testData['account'] = UserTest::createRandomTestUser();
    self::$testData['date'] = new DateTime();
    return new UserLoginAsEvent(self::$testData);
  }

  public static function createRandomTestUserLoginAsEvent() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['user'] = UserTest::createRandomTestUser();
    $testData['account'] = UserTest::createRandomTestUser();
    $testData['date'] = new DateTime();
    return new UserLoginAsEvent($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserLoginAsEvent', new UserLoginAsEvent());
  }

  public function testConstructorInjectionOfProperties() {
    $loginAsEvent = self::createTestUserLoginAsEvent();
    $expected = self::$testData;
    $this->assertEquals($expected, $loginAsEvent->toArray());
  }

  public function testGetters(UserLoginAsEvent $loginAsEvent = null) {
    if(null === $loginAsEvent) $loginAsEvent = self::createTestUserLoginAsEvent();
    $this->assertEquals(self::$testData['id'],      $loginAsEvent->getId());
    $this->assertEquals(self::$testData['user'],    $loginAsEvent->getUser());
    $this->assertEquals(self::$testData['account'], $loginAsEvent->getAccount());
    $this->assertEquals(self::$testData['date'],    $loginAsEvent->getDate());
    $this->assertEquals(self::$testData['ip'],      $loginAsEvent->getIp());
  }

  public function testSetters() {
    $loginAsEvent = new UserLoginAsEvent();
    $loginAsEvent->setId(self::$testData['id']);
    $loginAsEvent->setUser(self::$testData['user']);
    $loginAsEvent->setAccount(self::$testData['account']);
    $loginAsEvent->setDate(self::$testData['date']);
    $loginAsEvent->setIp(self::$testData['ip']);

    $this->testGetters($loginAsEvent);
  }
}
