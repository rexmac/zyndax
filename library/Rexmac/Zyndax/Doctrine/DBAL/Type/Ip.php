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
 * @subpackage Doctrine_DBAL_Type
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform,
    Doctrine\DBAL\Types\Type;

/**
 * Doctrine type that maps an IP address to an int
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Doctrine_DBAL_Type
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Ip extends Type {

  /**
   * Name of this type
   */
  const IP = 'ip';

  /**
   * Getter for name
   *
   * @return string
   */
  public function getName() {
    return self::IP;
  }

  /**
   * Gets the SQL declaration snippet for a field of this type.
   *
   * @param array $fieldDeclaration The field declaration.
   * @param AbstractPlatform $platform The currently used database platform.
   * @return string
   */
  public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
    return $platform->getDoctrineTypeMapping('IP');
  }

  /**
   * Converts a value from its PHP representation to its database representation
   * of this type.
   *
   * @param mixed $value The value to convert.
   * @param AbstractPlatform $platform The currently used database platform.
   * @return mixed The database representation of the value.
   */
  public function convertToDatabaseValue($value, AbstractPlatform $platform) {
    return ($value === null) ? null : sprintf('%u', ip2long($value));
  }

  /**
   * Converts a value from its database representation to its PHP representation
   * of this type.
   *
   * @param mixed $value The value to convert.
   * @param AbstractPlatform $platform The currently used database platform.
   * @return mixed The PHP representation of the value.
   */
  public function convertToPHPValue($value, AbstractPlatform $platform) {
    return ($value === null) ? null : long2ip($value);
  }
}
