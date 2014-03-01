<?php

namespace Rexmac\Zyndax\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Rexmac\Zyndax\Entity\TimeZoneTest,
    Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Entity\UserTest;

class UserProfileTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'        => 1,
    'user'      => null,
    'firstName' => 'test_firstname',
    'lastName'  => 'test_lastname',
    'position'  => 'Director of Marketing',
    'phone'     => '408-555-5555',
    'website'   => 'http://zyndax.org/',
    'timeZone'  => null,
    'socialNetworkIdentities' => null,
  );

  public function setUp() {
  }

  public static function createTestUserProfile(User $user = null) {
    self::$testData['user'] = UserTest::createTestUser();
    $socialNetworkIdentity = UserSocialNetworkIdentityTest::createTestIdentity();
    self::$testData['socialNetworkIdentities'] = new ArrayCollection(array($socialNetworkIdentity));
    self::$testData['timeZone'] = TimeZoneTest::createTestTimeZone();
    $testData = self::$testData;
    if(null !== $user) $testData['user'] = $user;
    return new UserProfile($testData);
  }

  public static function createRandomTestUserProfile(User $user = null) {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['user'] = null === $user ? UserTest::createRandomTestUser() : $user;
    $testData['firstName'] .= substr(sha1(mt_rand()), 0, 4);
    $testData['lastName']  .= substr(sha1(mt_rand()), 0, 4);
    $testData['socialNetworkIdentities'] = new ArrayCollection(array(UserSocialNetworkIdentityTest::createTestIdentity()));
    $testData['timeZone'] = TimeZoneTest::createRandomTestTimeZone();
    return new UserProfile($testData);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\UserProfile', new UserProfile());
  }

  public function testConstructorInjectionOfProperties() {
    $profile = self::createTestUserProfile();
    $expected = self::$testData;
    $this->assertEquals($expected, $profile->toArray());
  }

  public function testGetters(UserProfile $profile = null) {
    if(null === $profile) $profile = self::createTestUserProfile();
    $this->assertEquals(self::$testData['id'],        $profile->getId());
    $this->assertEquals(self::$testData['user'],      $profile->getUser());
    $this->assertEquals(self::$testData['firstName'], $profile->getFirstName());
    $this->assertEquals(self::$testData['lastName'],  $profile->getLastName());
    $this->assertEquals(self::$testData['position'],  $profile->getPosition());
    $this->assertEquals(self::$testData['phone'],     $profile->getPhone());
    $this->assertEquals(self::$testData['website'],   $profile->getWebsite());
    $this->assertEquals(self::$testData['timeZone'],  $profile->getTimeZone());
  }

  public function testSetters() {
    $profile = new UserProfile();
    $profile->setId(self::$testData['id']);
    $profile->setUser(self::$testData['user']);
    $profile->setFirstName(self::$testData['firstName']);
    $profile->setLastName(self::$testData['lastName']);
    $profile->setPosition(self::$testData['position']);
    $profile->setPhone(self::$testData['phone']);
    $profile->setWebsite(self::$testData['website']);
    $profile->setTimeZone(self::$testData['timeZone']);

    $this->testGetters($profile);
  }
}
