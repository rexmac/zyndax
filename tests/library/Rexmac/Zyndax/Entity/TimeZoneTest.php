<?php

namespace Rexmac\Zyndax\Entity;

use Rexmac\Zyndax\Entity\TimeZone;

class TimeZoneTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'   => 1,
    'name' => 'America/Los_Angeles'
  );

  public static function createTestTimeZone() {
    return new TimeZone(self::$testData);
  }

  public static function createRandomTestTimeZone() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['name'] = sha1(mt_rand());
    return new TimeZone($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\TimeZone', new TimeZone());
  }

  public function testConstructorInjectionOfProperties() {
    $timeZone = self::createTestTimeZone();
    $expected = self::$testData;
    $this->assertEquals($expected, $timeZone->toArray());
  }

  public function testGetters(TimeZone $timeZone = null) {
    if(null === $timeZone) $timeZone = self::createTestTimeZone();
    $this->assertEquals(self::$testData['id'],   $timeZone->getId());
    $this->assertEquals(self::$testData['name'], $timeZone->getName());
  }

  public function testSetters() {
    $timeZone = new TimeZone();
    $timeZone->setId(self::$testData['id']);
    $timeZone->setName(self::$testData['name']);

    $this->testGetters($timeZone);
  }
}
