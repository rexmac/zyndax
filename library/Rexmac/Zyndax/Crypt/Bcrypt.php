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
 * @subpackage Crypt
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Crypt;

/**
 * Bcrypt algorithm using crypt() function of PHP
 *
 * Based on Zend\Crypt\Password\Bcrypt class of ZF2
 * Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Crypt
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Bcrypt {

  /**
   * Two digit cost paramter; base-2 logarithm of the iteration count
   *
   * @var string
   */
  public static $cost = '14';

  /**
   * Pre-defined salt value
   *
   * @var string
   */
  public static $salt;

  /**
   * Create a one-way hash
   *
   * @param string $string String to be hashed
   * @return string Hashed string
   */
  public static function create($string) {
    if(empty(self::$salt)) {
      $salt = self::getBytes(16);
    } else {
      $salt = self::$salt;
    }
    $salt64 = substr(str_replace('+', '.', base64_encode($salt)), 0, 22);

    #$prefix = '$2a$';
    $prefix = '$2y$';

    $hash = crypt($string, $prefix . self::$cost . '$' . $salt64);
    if(strlen($hash) <= 13) {
      throw new Exception\RuntimeException('Error during the bcrypt generation');
    }
    return $hash;
  }

  /**
   * Verify if string matches hash
   *
   * @param string $string String to verify
   * @param string $hash Hashed version of string
   * @return bool
   */
  public static function verify($string, $hash) {
    return $hash === crypt($string, $hash);
  }

  /**
   * Generate random bytes using OpenSSL or Mcrypt
   *
   * Based on Zend\Math\Rand::getBytes of ZF2
   * Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
   *
   * @param int $length Length of string to return
   * @return string
   * @throws Exception\RuntimeException
   */
  private static function getBytes($length) {
    if($length <= 0) {
      return false;
    }

    if(extension_loaded('openssl')) {
      $rand = openssl_random_pseudo_bytes($length, $secure);
      if($secure === true) {
        return $rand;
      }
    }

    if(extension_loaded('mcrypt')) {
      $rand = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
      if($rand !== false && strlen($rand) === $length) {
        return $rand;
      }
    }

    throw new Exception\RuntimeException(
      'This PHP environment doesn\'t support secure random number generation. ' .
      'Please consider installing the OpenSSL and/or Mcrypt extensions.'
    );
  }
}
