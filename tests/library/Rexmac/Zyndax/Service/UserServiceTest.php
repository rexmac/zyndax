<?php

namespace Rexmac\Zyndax\Service;

use Rexmac\Zyndax\Crypt\Bcrypt,
    Rexmac\Zyndax\Entity\AclRoleTest,
    Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Entity\UserTest,
    Rexmac\Zyndax\Mail\Transport\Mock as MockMailTransport,
    Rexmac\Zyndax\Service\UserService,
    \Zend_Crypt_Hmac as Hmac,
    \Zend_Registry;

class UserServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\UserModelTestCase {

  public function setUp() {
    parent::setUp();
  }

  private function setUp2() {
    $entityManager = UserService::getEntityManager();
    $testRole      = AclRoleTest::createTestAclRole();
    $entityManager->persist($testRole);
    for($i = 0; $i < 3; ++$i) {
      $testUser = UserTest::createRandomTestUser($testRole);
      $entityManager->persist($testUser);
    }
    for($i = 0; $i < 2; ++$i) {
      $testUser = UserTest::createRandomTestUser($testRole);
      $testUser->setActive(false);
      $testUser->setLocked(true);
      $entityManager->persist($testUser);
    }
    $entityManager->flush();
  }

  public function testConstructFromArray() {
    $testEntity = UserTest::createTestUser();
    $a = $testEntity->toArray();
    unset($testEntity);
    UserService::create($a);
    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\User e')->execute();

    $this->assertEquals(1, count($entities));
    $this->assertEquals($a, $entities[0]->toArray());
  }

  public function testConstructFromObject() {
    $testEntity = UserTest::createTestUser();
    UserService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\User e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    UserService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    UserService::create(new \stdClass());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(UserTest::createRandomTestUser());
    }
    self::$entityManager->flush();

    $entities = UserService::find();
    $this->assertEquals(5, count($entities));
    foreach($entities as $entity) {
      $this->assertTrue($entity instanceof User);
    }
  }

  public function testFindWithParameter() {
    $testEntity = UserTest::createTestUser();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = UserService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testDelete() {
    $testEntity = UserTest::createTestUser();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\User e')->execute();
    $this->assertEquals(1, count($entities));

    UserService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\User e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = UserTest::createTestUser();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    UserService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Entity\User e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }

  public function testEncryptPassword() {
    $password = 'password';
    Zend_Registry::set('staticSalt', sha1(mt_rand()));
    $encrypted = UserService::encryptPassword($password);
    $this->assertEquals(53, strlen($encrypted));
  }

  public function testVerifyPassword() {
    $user = UserTest::createTestUser();
    $password = 'password';
    Zend_Registry::set('staticSalt', sha1(mt_rand()));
    $encrypted = UserService::encryptPassword($password);
    $user->setPassword($encrypted);
    $this->assertTrue(UserService::verifyPassword($user, $password));
  }

  /**
   * @expectedException Zend_Mail_Transport_Exception
   * @expectedExceptionMessage Unable to send mail.
   */
  public function testSendPasswordResetEmailFailure() {
    $siteDomain = 'mytestsite.tld';
    $siteName = 'MY_TEST_SITE';
    $_SERVER['HTTP_HOST'] = $siteDomain;
    Zend_Registry::set('config', array());
    Zend_Registry::set('siteName', $siteName);

    $recipient = 'root@localhost';
    $user = UserTest::createTestUser();
    $user->setEmail($recipient);  // Real address in case we actually send mail

    $mock = new MockMailTransport();
    $mock->forceException = true;
    UserService::sendPasswordResetEmail($user, $mock);
  }

  public function testSendPasswordResetEmail() {
    $siteDomain = 'mytestsite.tld';
    $siteName = 'MY_TEST_SITE';
    $_SERVER['HTTP_HOST'] = $siteDomain;
    Zend_Registry::set('config', array());
    Zend_Registry::set('siteName', $siteName);

    $recipient = 'root@localhost';
    $user = UserTest::createTestUser();
    $user->setEmail($recipient);  // Real address in case we actually send mail

    $mock = new MockMailTransport();
    UserService::sendPasswordResetEmail($user, $mock);

    $subject = '['.$siteName.'] Lost password';
    $this->assertTrue($mock->called);
    $this->assertEquals($subject, $mock->subject);
    $this->assertEquals('noreply@' . $siteDomain, $mock->from);
    $this->assertContains($recipient, $mock->recipients);
    $this->assertContains('We recently received a request to reset your password.', $mock->mail->getBodyText()->getRawContent());
    #$this->assertContains('Content-Transfer-Encoding: quoted-printable', $mock->header);
    #$this->assertContains('Content-Type: text/plain', $mock->header);
    $this->assertContains("From: {$siteName} <noreply@{$siteDomain}>", $mock->header);
    $this->assertContains("Subject: {$subject}", $mock->header);
    $this->assertContains("To: {$recipient}", $mock->header);
    #$this->assertContains('Cc: Example no. 1 for cc <recipient1_cc@example.com>', $mock->header);
  }

  /**
   * @expectedException Zend_Mail_Transport_Exception
   * @expectedExceptionMessage Unable to send mail.
   */
  public function testSendVerificationEmailFailure() {
    $siteDomain = 'mytestsite.tld';
    $siteName = 'MY_TEST_SITE';
    $_SERVER['HTTP_HOST'] = $siteDomain;
    Zend_Registry::set('siteName', $siteName);

    $recipient = 'root@localhost';
    $user = UserTest::createTestUser();
    $user->setEmail($recipient);  // Real address in case we actually send mail

    $mock = new MockMailTransport();
    $mock->forceException = true;
    UserService::sendVerificationEmail($user, $mock);
  }

  public function testSendVerificationEmail() {
    $siteDomain = 'mytestsite.tld';
    $siteName = 'MY_TEST_SITE';
    $_SERVER['HTTP_HOST'] = $siteDomain;
    Zend_Registry::set('siteName', $siteName);

    $recipient = 'root@localhost';
    $user = UserTest::createTestUser();
    $user->setEmail($recipient);  // Real address in case we actually send mail

    $mock = new MockMailTransport();
    UserService::sendVerificationEmail($user, $mock);

    $subject = '['.$siteName.'] Email Verification';
    $this->assertTrue($mock->called);
    $this->assertEquals($subject, $mock->subject);
    $this->assertEquals('noreply@' . $siteDomain, $mock->from);
    $this->assertContains($recipient, $mock->recipients);
    $this->assertContains("Thank you for registering with {$siteName}.", $mock->mail->getBodyText()->getRawContent());
    $this->assertContains("From: {$siteName} <noreply@{$siteDomain}>", $mock->header);
    $this->assertContains("Subject: {$subject}", $mock->header);
    $this->assertContains("To: {$recipient}", $mock->header);
  }
}
