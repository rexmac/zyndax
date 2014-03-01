<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * Zyndax is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zyndax is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zyndax.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */
use \DateTime,
    Rexmac\Zyndax\Auth\Adapter\DoctrineAdapter as DoctrineAuthAdapter,
    Rexmac\Zyndax\Auth\AuthResult,
    Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Log\Logger,
    Rexmac\Zyndax\Service\AclRoleService,
    Rexmac\Zyndax\Service\SocialNetworkService,
    Rexmac\Zyndax\Service\UserEditEventService,
    Rexmac\Zyndax\Service\UserEmailVerificationService,
    Rexmac\Zyndax\Service\UserLoginEventService,
    Rexmac\Zyndax\Service\UserPasswordResetTokenService,
    Rexmac\Zyndax\Service\UserProfileService,
    Rexmac\Zyndax\Service\UserService,
    Rexmac\Zyndax\Service\UserSocialNetworkIdentityService,
    Rexmac\Zyndax\View\Helper\Jquery as JqueryViewHelper,
    \Zend_Auth,
    \Zend_Controller_Front,
    \Zend_Json,
    \Zend_Registry,
    \Zend_Session,
    \Zend_Session_Namespace;

/**
 * User controller
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserController extends \Rexmac\Zyndax\Controller\Action {

  /**
   * Edit flags
   */
  const PROFILE_EDIT = 'profile';
  const SOCIAL_EDIT  = 'social';
  const USER_EDIT    = 'user';

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
      ->addActionContext('login', 'json')
      ->setActionContext('register', 'json')
      ->initContext();
  }

  /**
   * Index action
   *
   * @return void
   */
  public function indexAction() {
    $this->_forward('profile');
  }

  /**
   * Check action
   *
   * @return void
   */
  public function checkAction() {
    $request = $this->getRequest();
    $params  = $request->getParams();
    $result  = null;
    $user    = null;
    if(isset($params['userId'])) {
      $user = UserService::findOneById($params['userId']);
      unset($params['userId']);
    }

    foreach($params as $key => $value) {
      switch($key) {
        case 'username':
          if(null !== $user && $user->getUsername() === $value) {
            $result = true;
          } else {
            $result =
              (null === UserService::findOneByUsername(strtolower($value))) &&
              (null === AclRoleService::findOneByName(strtolower($value)));
          }
          break;
        case 'email':
          if($user && $user->getEmail() === $value) {
            $result = true;
          } else {
            $result = (null === UserService::findOneByEmail(strtolower($value)));
          }
          break;
      }

      if($result !== null) {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        #$jsonData = Zend_Json::encode($result);
        $jsonData = Zend_Json::encode(array($key, $result));
        $this->getResponse()->setBody($jsonData);
        break;  // Exit for-loop early
      }
    }
  }

  /**
   * Change Password Action
   *
   * @return void
   */
  public function changepasswordAction() {
    $user = $this->_user;
    $form = new \Application_Form_UserPasswordChange();
    $request = $this->getRequest();
    if($request->isPost()) {
      if($form->isValid($request->getPost())) {
        $data = $form->getValues();
        try {
          // Verify old password
          if(!UserService::verifyPassword($user, $data['oldPassword'])) {
            $message = 'Invalid old password';
            $this->view->messages()->addMessage($message, 'error');
          } else {
            $user->setPassword(UserService::encryptPassword($data['newPassword']));
            // Redirect to login page
            $this->_helper->sessionMessenger('Password changed successfully. You may now login using your new password.', 'success');
            Zend_Auth::getInstance()->clearIdentity();
            return $this->getHelper('Redirector')->gotoRoute(array(), 'login');
          }
        } catch(Exception $e) { // @codeCoverageIgnoreStart
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: UCCPA001');
          $this->view->messages()->addMessage($message, 'error');
          Logger::err($e->getMessage());
        } // @codeCoverageIgnoreEnd
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    } else { // Not a POST request
    }

    $this->view->form = $form;
  }

  /**
   * Login action
   *
   * @return void
   */
  public function loginAction() {
    if(Zend_Auth::getInstance()->hasIdentity()) {
      return $this->_helper->redirector('index', 'index');
    }
    $request = $this->getRequest();

    // Session expired?
    $authCookieName = Zend_Registry::get('config')->session->auth->name;
    if($request->getCookie($authCookieName)) {
      // Remove/Expire auth cookie
      if(!Zend_Session::$_unitTestEnabled) { // @codeCoverageIgnoreStart
        $cookieParams = session_get_cookie_params();
        setcookie($authCookieName, '', time()-3600, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], true);
      } // @codeCoverageIgnoreEnd
      Logger::debug(__METHOD__.':: session has expired');
      $this->view->messages()->addMessage('Your session has expired.', 'notice');
    }

    $form = new \Application_Form_UserLogin();

    // Process login request
    if($request->isPost()) {
      if($form->isValid($request->getPost()) && $this->_processAuth($form)) {
        // If user attempted to access page requiring authentication before
        // they were authenticated, then redirect them back to that page.
        $session = new Zend_Session_Namespace('referrer');
        if(isset($session->uri)) {
          $uri = $session->uri;
          Zend_Session::namespaceUnset('referrer');
          Logger::debug(__METHOD__.':: Post-login redirect to '.$uri);
          return $this->getHelper('Redirector')->gotoUrl($uri);
        }
        // Otherwise, redirect to home page
        return $this->_helper->redirector('index', 'index');
      }
    }

    $this->view->form = $form;
  }

  /**
   * Logout action
   *
   * @return void
   */
  public function logoutAction() {
    // If admin was logged in as non-admin user, then un-loginAs and return to admin home
    $authCookieName = Zend_Registry::get('config')->session->auth->name;
    $authSession = new Zend_Session_Namespace($authCookieName);
    if($authSession->loginAsUser) {
      unset($authSession->loginAsUser);
      return $this->getHelper('Redirector')->gotoRoute(array(), 'adminHome');
    }

    // Else, logout user and return home
    $this->_logoutUser();
    return $this->getHelper('Redirector')->gotoRoute(array(), 'home');
  }

  /**
   * Lost password action
   *
   * Allows user to initiate a password reset request.
   *
   * @return void
   */
  public function lostpasswordAction() {
    if(Zend_Auth::getInstance()->hasIdentity()) {
      return $this->getHelper('Redirector')->gotoRoute(array(), 'home');
    }

    $request = $this->getRequest();
    $form    = new \Application_Form_UserLostPassword();
    if($request->isPost()) {
      if($form->isValid($request->getPost())) {
        $data = $form->getValues();

        try {
          /*
          if(null === ($user = UserService::findOneByEmail($data['email']))) {
            throw new UserControllerException('Sorry. We have no record of that email address');
          }
          */

          if(null !== ($user = UserService::findOneByUsername($data['username']))) {
            UserService::sendPasswordResetEmail($user);
          }

          // Redirect user to home page
          $this->view->success = 1;
          //$message = 'An email containing further instructions has been sent to <code>'.$user->getEmail().'</code>.'
          $message = 'An email containing further instructions has been sent to the email address on file.'
                   . ' Please follow the instructions in the email to reset your password.';
          $this->_helper->sessionMessenger($message, 'success');
          return $this->getHelper('Redirector')->gotoRoute(array(), 'home');
        } catch(UserControllerException $e) {
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $this->view->messages()->addMessage($e->getMessage(), 'error');
        } catch(Exception $e) { // @codeCoverageIgnoreStart
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: UCLPA001');
          $this->view->messages()->addMessage($message, 'error');
          Logger::err($e->getMessage());
        } // @codeCoverageIgnoreEnd
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    }
    $this->view->form = $form;
  }

  /**
   * Password reset action
   *
   * Allows user to reset their password.
   *
   * @return void
   */
  public function resetpasswordAction() {
    if(Zend_Auth::getInstance()->hasIdentity()) {
      return $this->_helper->redirector('index', 'index');
    }

    $token = $this->getRequest()->getParam('token', null);

    if(null === $token || '' == $token) {
      throw new UserControllerException('Invalid verification token');
    }
    if(null == ($passwordResetToken = UserPasswordResetTokenService::findOneByToken($token))) {
      throw new UserControllerException('Invalid verification token');
    }

    $form = new \Application_Form_UserPasswordReset();
    $request = $this->getRequest();
    if($request->isPost()) {
      if($form->isValid($request->getPost())) {
        $data = $form->getValues();

        // Update user's password
        $user = $passwordResetToken->getUser();
        $user->setPassword(UserService::encryptPassword($data['password']));
        UserService::update();

        // Track changes
        UserEditEventService::create(array(
          'user'        => $user,
          'editor'      => $user,
          'ip'          => $this->getRequest()->getServer('REMOTE_ADDR'),
          'date'        => new DateTime(),
          'description' => 'Password reset.',
        ));

        // Delete sender verification record
        UserPasswordResetTokenService::delete($passwordResetToken);

        // Redirect to login page
        $this->_helper->sessionMessenger('Password reset successfully. You may now login using your new password.', 'success');
        return $this->getHelper('Redirector')->gotoRoute(array(), 'login');
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    }
    $this->view->form = $form;
  }

  /**
   * User profile action
   *
   * @return void
   */
  public function profileAction() {
    $request = $this->getRequest();
    $user    = $this->_user;
    $form    = new \Application_Form_UserUpdate($user);

    if($request->isPost()) {
      if($form->isValid($request->getPost())) {
        $data = $form->getValues();

        try {
          $changed = $this->_updateUser($user, $data);

          $this->view->success = 1;

          if($changed) {
            $message = 'Profile updated successfully.';
            $msgPriority = 'success';
            $form = new \Application_Form_UserUpdate($user);
          } else {
            $message = 'No changes were made.';
            $msgPriority = 'notice';
          }

          $this->view->messages()->addMessage($message, $msgPriority);
        } catch(Exception $e) {
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          #$message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: UCPA001');
          $message = 'Application error: UCPA001 - '.$e->getMessage();
          $this->view->messages()->addMessage($message, 'error');
          Logger::err($e->getMessage());
        }
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    } else { // Not a POST request
    }
    $this->view->form = $form;
    JqueryViewHelper::assignData(array(
      'userId' => $user->getId(),
    ));
  }

  /**
   * Email verification action
   *
   * @return void
   */
  public function verifyemailAction() {
    $verificationToken = $this->getRequest()->getParam('token', null);

    if(null === $verificationToken || '' == $verificationToken) {
      throw new UserControllerException('Invalid verification token');
    }
    if(null == ($emailVerification = UserEmailVerificationService::findOneByToken($verificationToken))) {
      throw new UserControllerException('Unknown verification token');
    }

    // Update user
    $user = $emailVerification->getUser();
    $user->setActive(true);
    UserService::update();

    // Track changes
    UserEditEventService::create(array(
      'user'        => $user,
      'editor'      => $user,
      'ip'          => $this->getRequest()->getServer('REMOTE_ADDR'),
      'date'        => new DateTime(),
      'description' => 'Email verification: '.$user->getEmail(),
    ));

    // Delete sender verification record
    UserEmailVerificationService::delete($emailVerification);

    // Redirect to login page
    $this->_helper->sessionMessenger('Email address verified successfully. You may now login.', 'success');
    return $this->getHelper('Redirector')->gotoRoute(array(), 'login');
  }

  /**
   * Logout user
   *
   * @return void
   */
  private function _logoutUser() {
    $authCookieName = Zend_Registry::get('config')->session->auth->name;
    $session = new Zend_Session_Namespace($authCookieName);
    Zend_Auth::getInstance()->clearIdentity();
    #Zend_Session::writeClose(false); // 'false', do not mark session as read-only

    if(!Zend_Session::$_unitTestEnabled) { // @codeCoverageIgnoreStart
      // Remove/Expire auth cookie
      $cookieParams = session_get_cookie_params();
      setcookie($authCookieName, '', time()-3600, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], true);

      // Destroy sesion
      Zend_Session::destroy();
    } // @codeCoverageIgnoreEnd
  }

  /**
   * Process login using form values
   *
   * @param Application_Form_UserLogin $form
   * @return void
   */
  private function _processAuth(\Application_Form_UserLogin $form) {
    $values  = $form->getValues();
    $adapter = new DoctrineAuthAdapter();
    $adapter->setIdentity($values['username'])
            ->setCredential($values['password']);

    $auth   = Zend_Auth::getInstance();
    $result = $auth->authenticate($adapter);

    switch($result->getCode()) {
      case AuthResult::FAILURE_IDENTITY_NOT_FOUND:
      case AuthResult::FAILURE_ACCOUNT_LOCKED:
        $message = "Failure - Identity not found";
        break;
      case AuthResult::FAILURE_CREDENTIAL_INVALID:
        $message = "Failure - Credential invalid";
        break;
      case AuthResult::FAILURE_REQUIRES_EMAIL_VERIFICATION:
        $message = "Failure - Account requires email verification";
        break;
      case AuthResult::SUCCESS:
        $message = "Success";
        break;
      // @codeCoverageIgnoreStart
      default:
        $message = "Failure - Unknown error";
      // @codeCoverageIgnoreEnd
    }
    $form->addErrorMessage($message);

    if($result->isValid()) {
      $user = $adapter->getUser();
      session_id();
      $siteDomain = Zend_Registry::get('siteDomain');

      // Track login event
      UserLoginEventService::create(array(
        'user' => $user,
        'date' => new DateTime(),
        'ip'   => $this->getRequest()->getServer('REMOTE_ADDR')
      ));

      $auth->getStorage()->write($user->getId());

      // Set auth cookie
      if(!Zend_Session::$_unitTestEnabled) { // @codeCoverageIgnoreStart
        $authCookieName = Zend_Registry::get('config')->session->auth->name;
        $cookieParams = session_get_cookie_params();
        setcookie($authCookieName, 1, 0, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], true);
      } // @codeCoverageIgnoreEnd

      return true;
    }

    return false;
  }

  /**
   * Update user
   *
   * @param User $user User to be updated
   * @param array $data User data to be updated
   * @throws Exception
   * @return bool True if changes were made
   */
  private function _updateUser(User $user, array $data) {
    Logger::debug(__METHOD__.'::'.var_export($data, true));
    #if(isset($data['email']) && '' != $data['email'] && $data['email'] != $user->getEmail()) {
    #  $user->setEmail($data['email']);
    #}
    $profile  = $user->getProfile();
    $social   = $profile->getSocialNetworkIdentities();

    // Track changes
    $changes = array(
      PROFILE_EDIT => array(),
      SOCIAL_EDIT  => array(),
      USER_EDIT    => array()
    );
    foreach($data as $key => $newValue) {
      Logger::debug(__METHOD__.":: $key");
      if(in_array($key, array('firstName', 'lastName', 'phone'))) {
        Logger::debug(__METHOD__.':: Profile key');
        $type = PROFILE_EDIT;
        $oldValue = $profile->{'get'.ucfirst($key)}();
      } elseif(preg_match('/^social(\d+)$/', $key, $matches)) {
        Logger::debug(__METHOD__.':: Social key: social' . $matches[1]);
        $type = SOCIAL_EDIT;
        $oldValue = $social[$matches[1] - 1];
      } else {
        Logger::debug(__METHOD__.':: User key');
        $type = USER_EDIT;
        $oldValue = $user->{'get'.ucfirst($key)}();
      }
      Logger::debug(__METHOD__.":: OLD => ".(is_object($oldValue) ? get_class($oldValue) : var_export($oldValue, true)));
      Logger::debug(__METHOD__.":: NEW => ".(is_object($newValue) ? get_class($newValue) : var_export($newValue, true)));
      // Only update changed properties, and keep track of the changes as well
      if($this->_valueChanged($oldValue, $newValue)) {
        Logger::debug(__METHOD__.":: $key has changed");
        Logger::debug(__METHOD__.":: OLD => ".(is_object($oldValue) ? get_class($oldValue) : var_export($oldValue, true)));
        Logger::debug(__METHOD__.":: NEW => ".(is_object($newValue) ? get_class($newValue) : var_export($newValue, true)));
        $oldVal = $oldValue;
        $newVal = $newValue;
        if($newValue instanceof Rexmac\Zyndax\Form\Element\SocialNetworkIdentity && $oldValue instanceof Rexmac\Zyndax\Entity\UserSocialNetworkIdentity) {
          $newVal = $newValue->getIdentityName() . '@' . SocialNetworkService::findOneById($newValue->getNetwork())->getName();
          $oldVal = $oldValue->getName() . '@' . $oldValue->getSocialNetwork()->getName();
        } elseif(is_object($newValue)) {
          if(isset($oldValue)) $oldVal = $oldValue->getName();
          else $oldVal = '';
          $newVal = $newValue->getName();
        } elseif(is_object($oldValue)) {
          $oldVal = $oldValue->getName();
        }
        $changes[$type][] = array(
          'item'     => $key,
          'oldValue' => $oldVal,
          'newValue' => $newVal
        );
        // Set new value
        if($type === SOCIAL_EDIT) {
          if('' === $newValue->getIdentityName()) {
            $removed = $profile->removeSocialNetworkIdentity($oldValue);
            Logger::debug(__METHOD__.':: Removed? '.var_export($removed, true));
            UserSocialNetworkIdentityService::delete($oldValue);
            #$profile->setSocialNetworkIdentities(UserSocialNetworkIdentityService::findBy(array('userProfile', $profile->getId())));
          } else {
            $oldValue->setSocialNetwork(SocialNetworkService::findOneById($newValue->getNetwork()));
            $oldValue->setName($newValue->getIdentityName());
          }
        } elseif($type === PROFILE_EDIT) {
          $profile->{'set'.ucfirst($key)}($newValue);
        } else {
          $user->{'set'.ucfirst($key)}($newValue);
        }
      }
    }
    UserService::update();
    UserProfileService::update();
    UserSocialNetworkIdentityService::update();

    // Any changes to record?
    $changed = false;
    foreach(array(PROFILE_EDIT, SOCIAL_EDIT, USER_EDIT) as $type) {
      Logger::debug(__METHOD__ . ':: Examining ' . $type . ' changes...');
      if(count($changes[$type]) > 0) {
        Logger::debug(__METHOD__.':: changes[\'' . $type . '\'] = ' . var_export($changes[$type], true));
        $description = '';
        foreach($changes[$type] as $change) {
          Logger::debug(__METHOD__.':: change = ' . var_export($change, true));
          $description .= sprintf('%s changed from "%s" to "%s".',
            $change['item'],
            $change['oldValue'] === 0 ? '0' : $change['oldValue'],
            $change['newValue']
          ) . PHP_EOL;
          Logger::debug(__METHOD__.':: description = ' . $description);
        }
        UserEditEventService::create(array(
          'user'        => $user,
          'editor'      => $this->_user,
          'ip'          => $this->getRequest()->getServer('REMOTE_ADDR'),
          'date'        => new DateTime(),
          'description' => rtrim($description),
        ));

        $changed = true;
      }
    }

    return $changed;
  }

  /**
   * Return TRUE if values are equal
   *
   * @param mixed $oldValue
   * @param mixed $newValue
   * @return bool
   */
  private function _valueChanged($oldValue, $newValue) {
    if(is_object($newValue)) {
      if($newValue instanceof DateTime && $oldValue instanceof DateTime) {
        if($oldValue->format('Y-m-d') != $newValue->format('Y-m-d')) return true;
        else return false;
      } elseif($newValue instanceof Rexmac\Zyndax\Form\Element\SocialNetworkIdentity && $oldValue instanceof Rexmac\Zyndax\Entity\UserSocialNetworkIdentity) {
        if($oldValue instanceof Rexmac\Zyndax\Entity\UserSocialNetworkIdentity) {
          if((int)$newValue->getNetwork() !== (int)$oldValue->getSocialNetwork()->getId()) {
            return true;
          }
          return $newValue->getIdentityName() !== $oldValue->getName();
        } else {
          return (int)$newValue->getNetwork() > 0 && '' !== $newValue->getIdentityName();
        }
      } else {
        return $oldValue !== $newValue;
      }
    }

    return $oldValue != $newValue;
  }
}

/**
 * Usercontroller exception class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Controller_Exception
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserControllerException extends \Zend_Exception {
}
