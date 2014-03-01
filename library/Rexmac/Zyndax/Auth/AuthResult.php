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
 * @subpackage Auth
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Auth;

/**
 * Represents the result of attempting to authenticate a user using a
 * Zend_Auth adapter.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Auth
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
*/
class AuthResult {
  /**
   * General Failure
   */
  const FAILURE = 0;

  /**
   * Failure due to identity not being found.
   */
  const FAILURE_IDENTITY_NOT_FOUND = -1;

  /**
   * Failure due to identity being ambiguous.
   */
  const FAILURE_IDENTITY_AMBIGUOUS = -2;

  /**
   * Failure due to invalid credential being supplied.
   */
  const FAILURE_CREDENTIAL_INVALID = -3;

  /**
   * Failure due to user account being locked.
   */
  const FAILURE_ACCOUNT_LOCKED = -4;

  /**
   * Failure due to user account requires email verification.
   */
  const FAILURE_REQUIRES_EMAIL_VERIFICATION = -5;

  /**
   * Failure due to uncategorized reasons.
   */
  const FAILURE_UNCATEGORIZED = -6;

  /**
   * Authentication success.
   */
  const SUCCESS = 1;

  /**
   * Authentication result code
   *
   * @var int
   */
  private $_code;

  /**
   * The identity used in the authentication attempt
   *
   * @var mixed
   */
  private $_identity;

  /**
   * An array of string reasons why the authentication attempt was unsuccessful
   *
   * If authentication was successful, this should be an empty array.
   *
   * @var array
   */
  private $_messages;

  /**
   * Sets the result code, identity, and failure messages
   *
   * @param  int   $code
   * @param  mixed $identity
   * @param  array $messages
   * @return void
   */
  public function __construct($code, $identity, array $messages = array()) {
    $code = (int)$code;
    if($code < self::FAILURE_UNCATEGORIZED) {
      $code = self::FAILURE;
    } elseif($code > self::SUCCESS ) {
      $code = self::SUCCESS;
    }

    $this->_code     = $code;
    $this->_identity = $identity;
    $this->_messages = $messages;
  }

  /**
   * Returns whether the result represents a successful authentication attempt
   *
   * @return boolean
   */
  public function isValid() {
    return ($this->_code > 0) ? true : false;
  }

  /**
   * Returns the result code for this authentication attempt
   *
   * @return int
   */
  public function getCode() {
    return $this->_code;
  }

  /**
   * Returns the identity used in the authentication attempt
   *
   * @return mixed
   */
  public function getIdentity() {
    return $this->_identity;
  }

  /**
   * Returns an array of string reasons why the authentication attempt was unsuccessful
   *
   * If authentication was successful, this method returns an empty array.
   *
   * @return array
   */
  public function getMessages() {
    return $this->_messages;
  }
}
