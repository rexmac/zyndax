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
 * Represents a user login-as event
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @ORM\Entity
 */
class UserLoginAsEvent extends \Rexmac\Zyndax\Doctrine\Entity {
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
   * User that performed the login-as
   *
   * @var Rexmac\Zyndax\Entity\User
   * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
   */
  protected $user = null;

  /**
   * User account that was accessed via login-as
   *
   * @var Rexmac\Zyndax\Entity\User
   * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
   */
  protected $account = null;

  /**
   * Date that login-as event occurred
   *
   * @var DateTime
   * @ORM\Column(type="datetime")
   */
  protected $date = null;

  /**
   * IP address that performed login-as event
   *
   * @var string
   * @ORM\Column(type="ip")
   */
  protected $ip = null;
}