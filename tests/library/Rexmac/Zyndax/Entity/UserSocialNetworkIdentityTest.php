<?php

namespace Rexmac\Zyndax\Entity;

use Rexmac\Zyndax\Entity\UserProfileTest;

class UserSocialNetworkIdentityTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'            => 1,
    'userProfile'   => null,
    'socialNetwork' => null,
    'name'          => 'test_name',
  );

  public function setUp() {
    #$testData['userProfile'] = UserProfileTest::createTestUserProfile();
    $testData['socialNetwork'] = SocialNetworkTest::createTestSocialNetwork();
  }

  public static function createTestIdentity() {
    return new UserSocialNetworkIdentity(self::$testData);
  }

  public static function createRandomTestIdentity() {
    $testData = self::$testData;
    unset($testData['id']);
    #$testData['userProfile'] = UserProfileTest::createRandomTestUserProfile();
    $testData['socialNetwork'] = SocialNetworkTest::createRandomTestSocialNetwork();
    $testData['name'] .= substr(sha1(mt_rand()), 0, 4);
    return new UserSocialNetworkIdentity($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserSocialNetworkIdentity', new UserSocialNetworkIdentity());
  }

  public function testConstructorInjectionOfProperties() {
    $identity = self::createTestIdentity();
    $expected = self::$testData;
    $this->assertEquals($expected, $identity->toArray());
  }

  public function testGetters(UserSocialNetworkIdentity $identity = null) {
    if(null === $identity) $identity = self::createTestidentity();
    $this->assertEquals(self::$testData['id'],            $identity->getId());
    $this->assertEquals(self::$testData['userProfile'],   $identity->getUserProfile());
    $this->assertEquals(self::$testData['socialNetwork'], $identity->getSocialNetwork());
    $this->assertEquals(self::$testData['name'],          $identity->getName());
  }

  public function testSetters() {
    $identity = new UserSocialNetworkIdentity();
    $identity->setId(self::$testData['id']);
    $identity->setUserProfile(self::$testData['userProfile']);
    $identity->setSocialNetwork(self::$testData['socialNetwork']);
    $identity->setName(self::$testData['name']);

    $this->testGetters($identity);
  }
}
