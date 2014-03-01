<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * This source file is subject to the Modified BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL:
 * http://rexmac.com/license/bsd2c.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to
 * license@rexmac.com so that we can send you a copy.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Auth_Adapter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Auth\Adapter;

use Rexmac\Zyndax\Service\UserService,
    Rexmac\Zyndax\Auth\AuthResult;

/**
 * Provides Zend_Auth adapter that uses Doctrine ORM to authenticate
 * against a database.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Auth_Adapter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class DoctrineAdapter implements \Zend_Auth_Adapter_Interface {

  /**
   * Identity value
   *
   * @var string
   */
  protected $identity = null;

  /**
   * Credential values
   *
   * @var string
   */
  protected $credential = null;

  /**
   * Config array for Zend_Auth
   *
   * @var array
   */
  private $authResultInfo = null;

  /**
   * User
   *
   * @var Rexmac\Zyndax\Entity\User
   */
  private $user;

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    $this->authResultInfo = array(
      'code'     => AuthResult::FAILURE,
      'identity' => null,
      'messages' => array()
    );
  }

  /**
   * Sets the value to be used as the identity
   *
   * @param  string $value Identity value
   * @return Rexmac\Auth\Adapter\DoctrineAdapter Provides a fluent interface
   */
  public function setIdentity($value) {
    $this->identity = $value;
    return $this;
  }

  /**
   * Sets the credential value to be used
   *
   * @param  string $credential Credential value
   * @return Rexmac\Auth\Adapter\DoctrineAdapter Provides a fluent interface
   */
  public function setCredential($credential) {
    $this->credential = $credential;
    return $this;
  }

  /**
   * Atempts to authenticate
   *
   * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
   * @return Zend_Auth_Result
   */
  public function authenticate() {
    if(null !== ($user = UserService::findOneByUsername($this->identity))) {
      if(!UserService::verifyPassword($user, $this->credential)) {
        $this->authResultInfo['code'] = AuthResult::FAILURE_CREDENTIAL_INVALID;
        $this->authResultInfo['messages'][] = 'Supplied credential is invalid.';
      } elseif(!$user->getActive()) {
        $this->authResultInfo['code'] = AuthResult::FAILURE_REQUIRES_EMAIL_VERIFICATION;
        $this->authResultInfo['messages'][] = 'User account requires email address verification.';
      } elseif($user->getLocked()) {
        $this->authResultInfo['code'] = AuthResult::FAILURE_ACCOUNT_LOCKED;
        $this->authResultInfo['messages'][] = 'User account is locked.';
      } else {
        $this->user = $user;
        $user->setLastConnect(new \DateTime());
        UserService::update();
        $this->authResultInfo['code'] = AuthResult::SUCCESS;
        $this->authResultInfo['messages'][] = 'Authentication successful.';
      }
    } else {
      $this->authResultInfo['code'] = AuthResult::FAILURE_IDENTITY_NOT_FOUND;
      $this->authResultInfo['messages'][] = 'Identity not found.';
    }

    return $this->authenticateCreateAuthResult();
  }

  /**
   * Creates a Zend_Auth_Result object from the information that was collected
   * during the authenticate() attempt.
   *
   * @return Zend_Auth_Result
   */
  private function authenticateCreateAuthResult() {
    return new AuthResult(
      $this->authResultInfo['code'],
      $this->authResultInfo['identity'],
      $this->authResultInfo['messages']
    );
  }

  /**
   * Returns current user
   *
   * @return Rexmac\Zyndax\Entity\User Current user
   */
  public function getUser() {
    return $this->user;
  }
}
