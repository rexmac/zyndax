<?php

namespace Rexmac\Zyndax\Entity;

class SocialNetworkTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'     => 1,
    'name'   => 'test_network_name',
    'abbrev' => 'tabrv',
  );

  public static function createTestSocialNetwork() {
    return new SocialNetwork(self::$testData);
  }

  public static function createRandomTestSocialNetwork() {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['name']   .= substr(sha1(mt_rand()), 0, 4);
    $testData['abbrev'] = substr(sha1(mt_rand()), 0, 8);
    return new SocialNetwork($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\SocialNetwork', new SocialNetwork());
  }

  public function testConstructorInjectionOfProperties() {
    $network = self::createTestSocialNetwork();
    $expected = self::$testData;
    $this->assertEquals($expected, $network->toArray());
  }

  public function testGetters(SocialNetwork $network = null) {
    if(null === $network) $network = self::createTestSocialNetwork();
    $this->assertEquals(self::$testData['id'],     $network->getId());
    $this->assertEquals(self::$testData['name'],   $network->getName());
    $this->assertEquals(self::$testData['abbrev'], $network->getAbbrev());
  }

  public function testSetters() {
    $network = new SocialNetwork();
    $network->setId(self::$testData['id']);
    $network->setName(self::$testData['name']);
    $network->setAbbrev(self::$testData['abbrev']);

    $this->testGetters($network);
  }
}
