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
 * @subpackage Test_PHPUnit
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Test\PHPUnit;

use Doctrine\ORM\Tools\SchemaTool;

/**
 * Functional testing scaffold for ACL
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Test_PHPUnit
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class AclModelTestCase extends DoctrineTestCase {

  /**
   * Set up before class.
   *
   * @return void
   */
  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    $tool = new SchemaTool(self::$entityManager);
    $tool->createSchema(array(
      self::getClassMetadata('Rexmac\Zyndax\Entity\AclPermission'),
      self::getClassMetadata('Rexmac\Zyndax\Entity\AclResource'),
      self::getClassMetadata('Rexmac\Zyndax\Entity\AclRole'),
      self::getClassMetadata('Rexmac\Zyndax\Entity\User'),
      self::getClassMetadata('Rexmac\Zyndax\Entity\UserProfile'),
    ));
  }
}
