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
 * @subpackage Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an ACL role
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="integer")
 * @ORM\DiscriminatorMap({1="AclRole", 2="AclAdminRole"})
 */
class AclRole extends \Rexmac\Zyndax\Doctrine\Entity {
  /**
   * Unique identifier
   *
   * @var int
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   */
  protected $id = null;

  /**
   * Name of role
   *
   * @var string
   * @ORM\Column(length=50,unique=true)
   */
  protected $name = null;

  /**
   * Description of role
   *
   * @var string
   * @ORM\Column(length=255)
   */
  protected $description = null;

  /**
   * Test if role is an elevated role
   *
   * @return bool
   */
  public function isAdminRole() {
    return $this instanceof AclAdminRole;
  }
}
