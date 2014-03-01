<?php

namespace Rexmac\Zyndax\Entity;

use \DateTime,
    Rexmac\Zyndax\Entity\UserEditEvent,
    Rexmac\Zyndax\Entity\UserTest;

class UserEditEventTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'          => 1,
    'user'        => null,
    'editor'      => null,
    'ip'          => '127.0.0.1',
    'date'        => null,
    'description' => '',
  );

  public function setUp() {
  }

  public static function createTestUserEditEvent() {
    self::$testData['user'] = UserTest::createRandomTestUser();
    self::$testData['editor'] = UserTest::createRandomTestUser();
    self::$testData['date'] = new DateTime();
    return new UserEditEvent(self::$testData);
  }

  public static function createRandomTestUserEditEvent() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['user'] = UserTest::createRandomTestUser();
    $testData['editor'] = UserTest::createRandomTestUser();
    $testData['date'] = new DateTime();
    return new UserEditEvent($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserEditEvent', new UserEditEvent());
  }

  public function testConstructorInjectionOfProperties() {
    $editEvent = self::createTestUserEditEvent();
    $expected = self::$testData;
    $this->assertEquals($expected, $editEvent->toArray());
  }

  public function testGetters(UserEditEvent $editEvent = null) {
    if(null === $editEvent) $editEvent = self::createTestUserEditEvent();
    $this->assertEquals(self::$testData['id'],          $editEvent->getId());
    $this->assertEquals(self::$testData['user'],        $editEvent->getUser());
    $this->assertEquals(self::$testData['editor'],      $editEvent->getEditor());
    $this->assertEquals(self::$testData['ip'],          $editEvent->getIp());
    $this->assertEquals(self::$testData['date'],        $editEvent->getDate());
    $this->assertEquals(self::$testData['description'], $editEvent->getDescription());
  }

  public function testSetters() {
    $editEvent = new UserEditEvent();
    $editEvent->setId(self::$testData['id']);
    $editEvent->setUser(self::$testData['user']);
    $editEvent->setEditor(self::$testData['editor']);
    $editEvent->setIp(self::$testData['ip']);
    $editEvent->setDate(self::$testData['date']);
    $editEvent->setDescription(self::$testData['description']);

    $this->testGetters($editEvent);
  }
}
