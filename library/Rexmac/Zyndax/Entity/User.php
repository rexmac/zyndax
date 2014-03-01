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
 * Represents a user
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @ORM\Entity
 */
class User extends \Rexmac\Zyndax\Doctrine\Entity {
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
   * Profile
   *
   * @var Rexmac\Zyndax\Entity\UserProfile
   * @ORM\OneToOne(targetEntity="Rexmac\Zyndax\Entity\UserProfile", mappedBy="user", cascade={"all"})
   */
  protected $profile;

  /**
   * ACL role
   *
   * @var Rexmac\Zyndax\Entity\AclRole
   * @ORM\ManyToOne(targetEntity="Rexmac\Zyndax\Entity\AclRole", cascade={"all"})
   */
  protected $role = null;

  /**
   * Login name
   *
   * @var string
   * @ORM\Column(length=50,unique=true)
   */
  protected $username = null;

  /**
   * Encrypted password
   *
   * @var string
   * @ORM\Column(length=53)
   */
  protected $password = null;

  /**
   * Email address
   *
   * @var string
   * @ORM\Column(length=255,unique=true)
   */
  protected $email = null;

  /**
   * Date that User was created
   *
   * @var DateTime
   * @ORM\Column(type="datetime")
   */
  protected $dateCreated = null;

  /**
   * Date that User last connected to the server
   *
   * @var DateTime
   * @ORM\Column(type="datetime")
   */
  protected $lastConnect = null;

  /**
   * True if the User account has been activated
   *
   * @var bool
   * @ORM\Column(type="boolean")
   */
  protected $active = null;

  /**
   * True if the User account has been locked
   *
   * @var bool
   * @ORM\Column(type="boolean")
   */
  protected $locked = null;

  /**
   * Return true if User has an ACL role of 'Administrator'
   *
   * @return bool
   */
  public function isAdmin() {
    return $this->getRole()->getName() === 'Administrator';
  }
}
