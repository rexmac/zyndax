<?php
/**
 * Rexmac\UUID
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
 * @package    Rexmac
 * @category   Security
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @author     Andrew Moore
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac;

/**
 * The following class generates valid [RFC 4122][ref-rfc-4122] compliant
 * Universally Unique IDentifiers (UUID) version 3, 4 and 5. UUIDs generated
 * validate using OSSP UUID Tool, and output for named-based UUIDs are exactly
 * the same. This is a pure PHP implementation.
 *
 * Adapted from code published by [Andrew Moore][ref-php-94959].
 *
 * [ref-rfc-4122]: http://www.ietf.org/rfc/rfc4122.txt
 * [ref-php-94959]: http://www.php.net/manual/en/function.uniqid.php#94959
 *
 * @package    Rexmac
 * @category   Security
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @author     Andrew Moore
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UUID {

  /**
   * @var string DNS namespace
   */
  const DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

  /**
   * @var string URL namespace
   */
  const URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

  /**
   * @var string ISO object identifier namespace
   */
  const OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

  /**
   * @var string X.500 namespace
   */
  const X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

  /**
   * @var string NULL UUID string
   */
  const NIL = '00000000-0000-0000-0000-000000000000';

  /**
   * Checks if a UUID has a valid format.
   *
   * @param string $uuid
   * @return boolean
   */
  public static function validate($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid);
  }

  /**
   * Convert a string UUID to binary format.
   *
   * @param string $uuid
   * @return string
   */
  public static function toBinary($uuid) {
    if(!self::validate($uuid)) return FALSE;

    // Get hexadecimal components of uuid
    $hex = str_replace(array('-', '{', '}'), '', $uuid);

    // Binary Value
    $bin = '';

    for($i = 0, $max = strlen($hex); $i < $max; $i += 2) {
      // Convert each character to a bit
      $bin .= chr(hexdec($hex[$i].$hex[$i + 1]));
    }

    return $bin;
  }

  /**
   * Convert a binary UUID to string format.
   *
   * @param string  $uuid
   * @return string
   */
  public static function str($uuid) {
    // String value
    $str = '';

    for($i = 0, $max = strlen($uuid); $i < $max; $i++) {
      // Add dash at proper offsets
      if($i >= 4 && $i <= 10 && ($i % 2) === 0) $str .= '-';

      // Convert each bit to an uppercase character
      $str .= sprintf('%02x', ord($uuid[$i]));
    }

    return $str;
  }

  /**
   * Version 3 UUIDs are named based. They require a namespace (another
   * valid UUID) and a value (the name). Given the same namespace and
   * name, the output is always the same.
   *
   * @param string $namespace
   * @param string $name
   * @return string
   */
  public static function v3($namespace, $name) {
    if(!self::validate($namespace)) return false;

    // Get namespace in binary format
    $nstr = self::toBinary($namespace);

    // Calculate hash value
    $hash = md5($nstr.$name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',
      // 32 bits for "time_low"
      substr($hash, 0, 8),
      // 16 bits for "time_mid"
      substr($hash, 8, 4),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 3
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }

  /**
   * Version 4 UUIDs are pseudo-random.
   *
   * @return string
   */
  public static function v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,
      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

  /**
   * Version 5 UUIDs are named based. They require a namespace (another
   * valid UUID) and a value (the name). Given the same namespace and
   * name, the output is always the same.
   *
   * @param  string $namespace
   * @param  string $name
   * @return string
   */
  public static function v5($namespace, $name) {
    if(!self::validate($namespace)) return false;

/*
    function bin2bstr($input) {
      if (!is_string($input)) return null; // Sanity check
      return pack('H*', base_convert($input, 2, 16));
    }

    function bstr2bin($input) {
      if(!is_string($input)) return null; // Sanity check
      // Unpack as a hexadecimal string
      $value = unpack('H*', $input);
      // Output binary representation
      return base_convert($value[1], 16, 2);
    }

    function str2bin($str, $mode=0) {
      $out = false;
      for($a=0; $a < strlen($str); $a++) {
        $dec = ord(substr($str,$a,1));
        $bin = '';
        for($i=7; $i>=0; $i--) {
            if ( $dec >= pow(2, $i) ) {
                $bin .= "1";
                $dec -= pow(2, $i);
            } else {
                $bin .= "0";
            }
        }
        // Default-mode
        if ( $mode == 0 ) $out .= $bin;
        // Human-mode (easy to read)
        if ( $mode == 1 ) $out .= $bin . " ";
        // Array-mode (easy to use)
        if ( $mode == 2 ) $out[$a] = $bin;
      }
      return $out;
    }
*/

    // Get namespace in binary format
    $nstr = self::toBinary($namespace);

    // Calculate hash value
    $hash = sha1($nstr.$name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',
      // 32 bits for "time_low"
      substr($hash, 0, 8),
      // 16 bits for "time_mid"
      substr($hash, 8, 4),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 5
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }
}
