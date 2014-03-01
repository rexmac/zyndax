<?php

use \DateTime,
    \Rexmac\Zyndax\Service\UserEmailVerificationService,
    \Rexmac\Zyndax\Service\UserPasswordResetTokenService,
    \Rexmac\Zyndax\Service\UserService;

class UserControllerTest extends \Rexmac\Zyndax\Test\PHPUnit\ControllerTestCase {

  public static function setUpBeforeClass() {
    parent::$withData = true;
    parent::setUpBeforeClass();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testLoginWithUnknownIdentity() {
    $this->_loginUser('nobody');
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertQueryContentContains(
      'ul.errors li',
      'Failure - Identity not found'
    );
  }

  public function testLoginWithLockedUserResultsInError() {
    $user = UserService::findOneByUsername('testuser');
    $user->setLocked(true);
    $this->_loginTestUser();
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertQueryContentContains(
      'ul.errors li',
      'Failure - Identity not found'
    );
  }

  public function testLoginWithInactiveUserResultsInError() {
    $user = UserService::findOneByUsername('testuser');
    $user->setActive(false);
    $this->_loginTestUser();
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertQueryContentContains(
      'ul.errors li',
      'Failure - Account requires email verification'
    );
  }

  public function testLoginWithInvalidCredential() {
    $this->_loginUser('testuser', 'notapassword');
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertQueryContentContains(
      'ul.errors li',
      'Failure - Credential invalid'
    );
  }

  public function testLoginWithEmptyPasswordResultsInError() {
    $this->_loginUser('testuser', '');
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertQueryContentContains(
      'ul.errors li',
      'Value is required and can\'t be empty'
    );
  }

  public function testLoginWithLoggedInUserRedirectsToIndex() {
    $this->_loginTestUser();
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());
    $this->redispatch('/user/login');
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertRedirectTo('/', 'Failed to redirect already logged in user');
  }

  public function testLoginWithExpiredSession() {
    $this->_loginTestUser();
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());

    // Simulate session expiration
    \Zend_Auth::getInstance()->clearIdentity();
    $_COOKIE['MYSSA'] = true;

    // Now that session has "expired", visit login page
    $this->redispatch('/user/login');
    $this->assertRoute('login', 'Failed to use \'login\' route');
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertBodyContains('Your session has expired', ' Missing session expirationm essage');
  }

  public function testLogout() {
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->_loginTestUser();
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());

    $this->redispatch('/logout');

    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertRedirectTo('/home', 'Failed to redirect upon successful logout');
  }

  public function testLogoutAfterLoginAs() {
    $this->assertFalse(\Zend_Auth::getInstance()->hasIdentity());
    $this->_loginAdmin();

    $this->redispatch('/');
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());

    $session = new Zend_Session_Namespace('MYSSA');
    $session->loginAsUser = '2';

    $this->redispatch('/logout');

    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());
    $this->assertRedirectTo('http://admin/home', 'Failed to redirect upon successful logout');
  }

  public function testIndexActionForwardsToProfileAction() {
    $this->_loginTestUser();

    $this->redispatch('/user/index');
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');

    $this->redispatch('/user/');
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');

    $this->redispatch('/user');
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');
  }

  public function testCheckActionWithValidUsername() {
    $this->dispatch('/user/check?username=testuser');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["username",false]');
  }

  public function testCheckActionWithInvalidUsername() {
    $this->dispatch('/user/check?username=testuser2');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["username",true]');
  }

  public function testCheckActionWithValidUserIdAndValidUsername() {
    $this->dispatch('/user/check/2/?username=testuser');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["username",true]');
  }

  public function testCheckActionWithValidUserIdAndInvalidUsername() {
    $this->dispatch('/user/check/2/?username=testuser2');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["username",true]');
  }

  public function testCheckActionWithInvalidUserIdAndValidUsername() {
    $this->dispatch('/user/check/99/?username=testuser');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["username",false]');
  }

  public function testCheckActionWithInvalidUserIdAndInvalidUsername() {
    $this->dispatch('/user/check/99/?username=testuser2');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["username",true]');
  }

  public function testCheckActionWithValidEmail() {
    $this->dispatch('/user/check?email=testuser@example.com');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["email",false]');
  }

  public function testCheckActionWithInvalidEmail() {
    $this->dispatch('/user/check?email=testuser2@example.com');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["email",true]');
  }

  public function testCheckActionWithValidUserIdAndValidEmail() {
    $this->dispatch('/user/check/2/?email=testuser@example.com');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["email",true]');
  }

  public function testCheckActionWithValidUserIdAndInvalidEmail() {
    $this->dispatch('/user/check/2/?email=testuser2@example.com');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["email",true]');
  }

  public function testCheckActionWithInvalidUserIdAndValidEmail() {
    $this->dispatch('/user/check/99/?email=testuser@example.com');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["email",false]');
  }

  public function testCheckActionWithInvalidUserIdAndInvalidEmail() {
    $this->dispatch('/user/check/99/?email=testuser2@example.com');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('check');
    $this->assertTrue($this->getResponse()->getBody() === '["email",true]');
  }

  public function testChangePasswordActionAsGuest() {
    $this->dispatch('/user/changepassword');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('changepassword');
    $this->assertRedirectTo('/user/login', 'Failed to redirect away for unauthenticated user');
  }

  public function testChangePasswordActionAsUserWithValidPassword() {
    $this->_loginTestUser();
    $this->redispatch('/user/changepassword');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('changepassword');
    $this->assertNotRedirect();
    $this->assertQuery('body.default.user.changepassword');

    $user = UserService::findOneByUsername('testuser');
    $oldPassword = $user->getPassword();
    $request = $this->getRequest();
    $request->setMethod('POST')->setPost(array(
      'csrf' => $this->_getFormCsrf(),
      'oldPassword' => 'testuser',
      'newPassword' => 'testuser2',
      'passwordConfirm' => 'testuser2',
    ));
    $this->dispatch('/user/changepassword');
    $this->assertTrue($oldPassword !== $user->getPassword(), "New password should not match old password.");
    $this->assertRedirectTo('/login', 'Failed to redirect after password change');
  }

  public function testChangePasswordActionAsUserWithInvalidPassword() {
    $this->_loginTestUser();
    $this->redispatch('/user/changepassword');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('changepassword');
    $this->assertNotRedirect();
    $this->assertQuery('body.default.user.changepassword');

    $user = UserService::findOneByUsername('testuser');
    $oldPassword = $user->getPassword();
    $request = $this->getRequest();
    $request->setMethod('POST')->setPost(array(
      'csrf' => $this->_getFormCsrf(),
      'oldPassword' => 'testuserx',
      'newPassword' => 'testuser2',
      'passwordConfirm' => 'testuser2',
    ));
    $this->redispatch('/user/changepassword', false);
    $this->assertTrue($oldPassword === $user->getPassword());
    $this->assertBodyContains('Invalid Old Password');
  }

  public function testChangePasswordActionAsUserWithInvalidNewPassword() {
    $this->_loginTestUser();
    $this->redispatch('/user/changepassword');
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('changepassword');
    $this->assertNotRedirect();
    $this->assertQuery('body.default.user.changepassword');

    $user = UserService::findOneByUsername('testuser');
    $oldPassword = $user->getPassword();
    $request = $this->getRequest();
    $request->setMethod('POST')->setPost(array(
      'csrf' => $this->_getFormCsrf(),
      'oldPassword' => 'testuser',
      'newPassword' => '456',
      'passwordConfirm' => '457',
    ));
    $this->redispatch('/user/changepassword', false);
    $this->assertTrue($oldPassword === $user->getPassword());
    $this->assertBodyContains('is less than 6 characters long');
  }

  public function testLostPasswordActionAsUserRedirectsToHome() {
    $this->_loginTestUser();
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());
    $this->redispatch('/user/lostpassword');
    $this->assertRedirectTo('/home', 'Failed to redirect');
  }

  public function testLostPasswordActionAsGuestWithUnknownUsername() {
    $this->dispatch('/user/lostpassword');

    // Submit form data
    $request = $this->getRequest();
    $request->setMethod('POST')->setPost(array(
      'username' => 'nosuchuser',
    ));
    $this->redispatch('/user/lostpassword', false);
    #$this->assertNotRedirect();
    #$this->assertBodyContains('Sorry. We have no record of that email address');
    $this->assertRedirectTo('/home', 'Failed to redirect');
    $resetTokens = UserPasswordResetTokenService::find();
    $this->assertEquals(0, count($resetToken));
  }

  public function testLostPasswordActionAsGuestWithValidUsername() {
    $user = UserService::findOneByUsername('testuser');
    $this->dispatch('/user/lostpassword');

    // Submit form data
    $request = $this->getRequest();
    $request->setMethod('POST')->setPost(array(
      'username' => $user->getUsername(),
    ));
    $this->redispatch('/user/lostpassword', false);
    $this->assertRedirectTo('/home', 'Failed to redirect');
    $resetToken = UserPasswordResetTokenService::findOneByUser($user->getId());
    $this->assertTrue(null !== $resetToken);
    return $resetToken->getToken();
  }

  /**
   * @depends testLostPasswordActionAsGuestWithValidEmail
   */
  public function testResetPasswordActionWithValidTokenAndValidFormData($resetToken) {
    $user = UserService::findOneByUsername('testuser');

    $this->dispatch('/user/resetpassword?token='.$resetToken);
    $this->assertNotRedirect();
    $this->assertQuery('form#userPasswordResetForm');

    $this->getRequest()->setMethod('POST')->setPost(array(
      'csrf' => $this->_getFormCsrf(),
      'password' => 'testuser2',
      'passwordConfirm' => 'testuser2',
    ));
    $this->redispatch('/user/resetpassword?token='.$resetToken, false);
    $this->assertFalse(UserService::verifyPassword($user, 'testuser'));
    $this->assertRedirectTo('/login', 'Failed to redirect');
    $user->setPassword(UserService::encryptPassword($user->getUsername(), $user->getSalt()));
    UserService::update();
  }

  public function testResetPasswordActionWithInvalidFormData() {
    // Preparation
    $user = UserService::findOneByUsername('testuser');
    $this->dispatch('/user/lostpassword');
    $this->getRequest()->setMethod('POST')->setPost(array('username' => $user->getUsername()));
    $this->redispatch('/user/lostpassword', false);
    $this->assertRedirectTo('/home', 'Failed to redirect');
    $resetToken = UserPasswordResetTokenService::findOneByUser($user->getId());
    $this->assertTrue(null !== $resetToken);

    // Test
    $this->redispatch('/user/resetpassword?token='.$resetToken->getToken());
    $this->assertNotRedirect();
    $this->assertQuery('form#userPasswordResetForm');
    $this->getRequest()->setMethod('POST')->setPost(array(
      'csrf' => $this->_getFormCsrf(),
      'password' => '123',
      'passwordConfirm' => '123',
    ));
    $this->redispatch('/user/resetpassword?token='.$resetToken->getToken(), false);
    $this->assertNotRedirect();
    $this->assertQuery('form#userPasswordResetForm');
    $this->assertTrue(UserService::verifyPassword($user, 'testuser'));
  }

  public function testResetPasswordActionWithoutToken() {
    $this->dispatch('/user/resetpassword');
    $this->assertNotRedirect();
    $this->assertBodyContains('Invalid verification token');
  }

  public function testResetPasswordActionWithInvalidToken() {
    $this->dispatch('/user/resetpassword?token=MY_TOKEN');
    $this->assertNotRedirect();
    $this->assertBodyContains('Invalid verification token');
  }

  public function testResetPasswordActionAsUserRedirectsToHome() {
    $this->_loginTestUser();
    $this->assertTrue(\Zend_Auth::getInstance()->hasIdentity());
    $this->redispatch('/user/resetpassword');
    $this->assertRedirectTo('/');
  }

  public function testVerifyEmailActionWithoutToken() {
    $this->dispatch('/user/verifyemail');
    $this->assertNotRedirect();
    $this->assertBodyContains('Invalid verification token');
  }

  public function testVerifyEmailActionWithInvalidToken() {
    $this->dispatch('/user/verifyemail?token=123');
    $this->assertNotRedirect();
    $this->assertBodyContains('Unknown verification token');
  }

  public function testVerifyEmailActionWithValidToken() {
    $user = UserService::findOneByUsername('testuser');
    $user->setActive(false);
    UserService::update();

    $this->assertFalse($user->getActive());
    $verificationToken = sha1(mt_rand() . $user->getEmail() . mt_rand());
    $emailVerification = UserEmailVerificationService::create(array(
      'user'  => $user,
      'token' => $verificationToken,
      'requestDate' => new DateTime(),
    ));

    $this->dispatch('/user/verifyemail?token='.$verificationToken);
    $this->assertRedirect('/login', 'Failed to redirect after verifying email.');
    $this->assertTrue($user->getActive());
  }

  public function testProfileActionForwardsToHomeForUnauthenticatedUser() {
    $this->dispatch('/user/profile');
    $this->assertRedirectTo('/user/login', 'Failed to redirect');
  }

  public function testProfileAction() {
    $this->_loginTestUser();

    $url = '/user/profile';
    $this->redispatch($url);
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');
  }

  public function testProfileActionWithInvalidFormData() {
    $this->_loginTestUser();
    $this->redispatch('/user/profile');
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');

    $this->getRequest()->setMethod('POST')->setPost(array('forgetful' => 'jones'));
    $this->redispatch('/user/profile', false);
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');
    $this->assertResponseCode(500);
    $this->assertXpathContentContains('//div[@class="form-errors messages error"]/span/ul[@class="errors"]/li', 'Value is required and can\'t be empty');
  }

  public function testProfileActionWithExceptionalValues() {
    $user    = UserService::findOneByUsername('testuser');
    $profile = $user->getProfile();
    $adminUser = UserService::findOneByUsername('admin');
    $this->assertNotEquals($adminUser->getEmail(), $user->getEmail());

    $this->_loginTestUser();
    $this->redispatch('/user/profile');
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');

    $this->getRequest()->setMethod('POST')->setPost(array(
      #'email'     => $user->getEmail(),
      'email'     => $adminUser->getEmail(),
      'firstName' => $profile->getFirstName(),
      'lastName'  => $profile->getLastName(),
    ));
    $this->redispatch('/user/profile', false);
    $this->assertNotRedirect();
    $this->assertModule('default');
    $this->assertController('user');
    $this->assertAction('profile');
    $this->assertResponseCode(500);
    $this->assertBodyContains('Application error: UCPA001 - SQLSTATE[23000]: Integrity constraint violation: ', 'Missing application error');
  }
}
