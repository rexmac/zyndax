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
 * Represents a PHP session
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @ORM\Entity
 */
class Session extends \Rexmac\Zyndax\Doctrine\Entity {
  /**
   * Unique identifier
   *
   * @var string
   * @ORM\Id
   * @ORM\Column(type="string",length=128)
   */
  protected $id = null;

  /**
   * Timestamp of last modification
   *
   * @var int
   * @ORM\Column(type="integer")
   */
  protected $modified = null;

  /**
   * Timestamp of expiration
   *
   * @var int
   * @ORM\Column(type="integer")
   */
  protected $lifetime = null;

  /**
   * Session data
   *
   * @var string
   * @ORM\Column(type="text")
   */
  protected $data = null;
}
