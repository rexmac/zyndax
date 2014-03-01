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
 * @subpackage Monitor_Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Monitor\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an error log entry
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Monitor_Entity
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @ORM\Entity
 */
class Error extends \Rexmac\Zyndax\Doctrine\Entity {
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
   * Log type
   *
   * @var string
   * @ORM\Column(length=50)
   */
  protected $logType = 'default';

  /**
   * Priority
   *
   * @var int
   * @ORM\Column(type="integer")
   */
  protected $priority = null;

  /**
   * Error number (see http://www.php.net/manual/en/errorfunc.constants.php)
   *
   * @var int
   * @ORM\Column(type="integer",nullable=true)
   */
  protected $errno = null;

  /**
   * Message
   *
   * @var string
   * @ORM\Column(type="text")
   */
  protected $message = null;

  /**
   * File
   *
   * @var string
   * @ORM\Column(length=255,nullable=true)
   */
  protected $file = null;

  /**
   * Line number
   *
   * @var int
   * @ORM\Column(type="integer",nullable=true)
   */
  protected $line = null;

  /**
   * Context
   *
   * @var string
   * @ORM\Column(type="text",nullable=true)
   */
  protected $context = null;

  /**
   * Stack trace
   *
   * @var string
   * @ORM\Column(type="text",nullable=true)
   */
  protected $stackTrace = null;

  /**
   * Date
   *
   * @var DateTime
   * @ORM\Column(type="datetime")
   */
  protected $date = null;

  /**
   * Priority name
   *
   * @var string
   * @ORM\Column(length=15)
   */
  protected $priorityName = null;
}
