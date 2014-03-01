<?php

namespace Rexmac\Zyndax\Entity;

use \DateTime,
    Rexmac\Zyndax\Entity\AclRole,
    Rexmac\Zyndax\Entity\AclRoleTest,
    Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Entity\UserProfileTest;

class UserTest extends \PHPUnit_Framework_TestCase {

  private static $testData = array(
    'id'          => 1,
    'role'        => null,
    'profile'     => null,
    'username'    => 'test_user_username',
    'password'    => 'test_user_password',
    'email'       => 'test_user_email',
    'dateCreated' => null,
    'lastConnect' => null,
    'active'      => true,
    'locked'      => false,
  );

  public function setUp() {
    self::$testData['role']        = AclRoleTest::createTestAclRole();
    self::$testData['dateCreated'] = new DateTime();
    self::$testData['lastConnect'] = new DateTime();
  }

  public static function createTestUser() {
    self::$testData['role']        = AclRoleTest::createTestAclRole();
    self::$testData['dateCreated'] = new DateTime();
    self::$testData['lastConnect'] = new DateTime();
    $user = new User(self::$testData);
    #$user->profile = UserProfileTest::createRandomTestUserProfile($user);
    return $user;
  }

  public static function createRandomTestUser(AclRole $role = null, Business $business = null) {
    $testData = self::$testData;
    unset($testData['id']);
    $testData['username']    = sha1(mt_rand());
    $testData['email']       = sha1(mt_rand()).'@example.com';
    $testData['role']        = null === $role ? AclRoleTest::createRandomTestAclRole() : $role;
    $testData['dateCreated'] = new DateTime();
    $testData['lastConnect'] = new DateTime();
    $user = new User($testData);
    #$user->profile = UserProfileTest::createRandomTestUserProfile($user);
    return $user;
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Entity\User', new User());
  }

  public function testConstructorInjectionOfProperties() {
    $user = self::createTestUser();
    $expected = self::$testData;
    $this->assertEquals($expected, $user->toArray());
  }

  public function testGetters(User $user = null) {
    if(null === $user) $user = self::createTestUser();
    $this->assertEquals(self::$testData['id'],          $user->getId());
    $this->assertEquals(self::$testData['role'],        $user->getRole());
    #$this->assertEquals(self::$testData['profile'],     $user->getProfile());
    $this->assertEquals(self::$testData['username'],    $user->getUsername());
    $this->assertEquals(self::$testData['password'],    $user->getPassword());
    $this->assertEquals(self::$testData['email'],       $user->getEmail());
    $this->assertEquals(self::$testData['dateCreated'], $user->getDateCreated());
    $this->assertEquals(self::$testData['lastConnect'], $user->getLastConnect());
    $this->assertEquals(self::$testData['active'],      $user->getActive());
    $this->assertEquals(self::$testData['locked'],      $user->getLocked());
  }

  public function testSetters() {
    $user = new User();
    $user->setId(self::$testData['id']);
    $user->setRole(self::$testData['role']);
    #$user->setProfile(self::$testData['profile']);
    $user->setUsername(self::$testData['username']);
    $user->setPassword(self::$testData['password']);
    $user->setEmail(self::$testData['email']);
    $user->setDateCreated(self::$testData['dateCreated']);
    $user->setLastConnect(self::$testData['lastConnect']);
    $user->setActive(self::$testData['active']);
    $user->setLocked(self::$testData['locked']);

    $this->testGetters($user);
  }

  public function testIsAdmin() {
    $role = AclRoleTest::createTestAclRole('Administrator');
    $user = self::createTestUser();
    $this->assertFalse($user->isAdmin());
    $user->setRole($role);
    $this->assertTrue($user->isAdmin());
  }
}
