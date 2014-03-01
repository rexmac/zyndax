<?php

namespace Rexmac\Zyndax\Entity;

use \DateTime,
    Rexmac\Zyndax\Entity\Session;

class SessionTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'       => null,
    'modified' => null,
    'lifetime' => null,
    'data'     => 'testing...1...2...3'
  );

  public function setUp() {
  }

  public static function createTestSession() {
    self::$testData['id'] = hash('whirlpool', mt_rand());
    self::$testData['modified'] = time();
    self::$testData['lifetime'] = 3600;
    return new Session(self::$testData);
  }

  public static function createRandomTestSession() {
    $testData = self::$testData;
    $testData['id'] = hash('whirlpool', mt_rand());
    $testData['modified'] = time();
    $testData['lifetime'] = 3600;
    return new Session($testData);
  }

  public function createOldTestSession() {
    $testData = self::$testData;
    $testData['id'] = hash('whirlpool', mt_rand());
    $testData['modified'] = time() - 1;
    $testData['lifetime'] = 0;
    return new Session($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\Session', new Session());
  }

  public function testConstructorInjectionOfProperties() {
    $session = self::createTestSession();
    $expected = self::$testData;
    $this->assertEquals($expected, $session->toArray());
  }

  public function testGetters(Session $session = null) {
    if(null === $session) $session = self::createTestSession();
    $this->assertEquals(self::$testData['id'],       $session->getId());
    $this->assertEquals(self::$testData['modified'], $session->getModified());
    $this->assertEquals(self::$testData['lifetime'], $session->getLifetime());
    $this->assertEquals(self::$testData['data'],     $session->getData());
  }

  public function testSetters() {
    $session = new Session();
    $session->setId(self::$testData['id']);
    $session->setModified(self::$testData['modified']);
    $session->setLifetime(self::$testData['lifetime']);
    $session->setData(self::$testData['data']);

    $this->testGetters($session);
  }
}
