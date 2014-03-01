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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection,
    Rexmac\Zyndax\Entity\UserSocialNetworkIdentity;

/**
 * Represents a user profile
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_idx", columns={"firstName", "lastName"})})
 */
class UserProfile extends \Rexmac\Zyndax\Doctrine\Entity {
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
   * User that owns the profile
   *
   * @var Rexmac\Zyndax\Entity\User
   * @ORM\OneToOne(targetEntity="Rexmac\Zyndax\Entity\User", inversedBy="profile", cascade={"persist"})
   */
  protected $user = null;

  /**
   * Social network identities associated with user
   *
   * @var Doctrine\Common\Collections\ArrayCollection
   * @ORM\OneToMany(targetEntity="Rexmac\Zyndax\Entity\UserSocialNetworkIdentity", mappedBy="userProfile", cascade={"persist"})
   */
  protected $socialNetworkIdentities = null;

  /**
   * First name of user
   * 
   * @var string
   * @ORM\Column(length=20)
   */
  protected $firstName = null;

  /**
   * Surname of user
   *
   * @var string
   * @ORM\Column(length=20)
   */
  protected $lastName = null;

  /**
   * Phone number of user
   *
   * @var string
   * @ORM\Column(length=20)
   */
  protected $phone = null;

  /**
   * Website URL of user's personal website
   *
   * @var string
   * @ORM\Column(length=255)
   */
  protected $website = null;

  /**
   * Preferred timezone of user
   *
   * @var Rexmac\Zyndax\Entity\TimeZone
   * @ORM\ManyToOne(targetEntity="Rexmac\Zyndax\Entity\TimeZone", cascade={"persist"})
   */
  protected $timeZone = null;

  /**
   * Constructor
   *
   * @param array $data Associative array of entity's properties and their values
   * @return void
   */
  public function __construct(array $data = null) {
    $this->socialNetworkIdentities = new ArrayCollection();
    parent::__construct($data);
  }

  /**
   * Remove a social network identity
   *
   * @param UserSocialNetworkIdentity $socialNetworkIdentity
   * @return void
   */
  public function removeSocialNetworkIdentity(UserSocialNetworkIdentity $socialNetworkIdentity) {
    $this->socialNetworkIdentities->removeElement($socialNetworkIdentity);
  }
}
