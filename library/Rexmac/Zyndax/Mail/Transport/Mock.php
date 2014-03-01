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
 * @subpackage Mail_Transport
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Mail\Transport;

use \Zend_Mail_Transport_Abstract,
    \Zend_Mail_Transport_Exception;

/**
 * Mock Zend_Mail transport class for use in mocking/testing SMTP transport.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Mail_Transport
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Mock extends Zend_Mail_Transport_Abstract {
  /**
   * Zend_Mail object
   *
   * @var Zend_Mail
   */
  public $mail = null;

  /**
   * Return path of email.
   *
   * @var string
   */
  public $returnPath = null;

  /**
   * Subject line of email.
   *
   * @var string
   */
  public $subject = null;

  /**
   * From line of mail
   *
   * @var string
   */
  public $from = null;

  /**
   * Array of message headers
   * @var array
   */
  public $headers = null;

  /**
   * Whether or not the _sendMail method ha been called.
   *
   * @var bool
   */
  public $called = false;

  /**
   * Force the _sendMail method to throw an exception
   *
   * @var bool
   */
  public $forceException = false;

  /**
   * Send an email independent from the used transport
   *
   * The requisite information for the email will be found in the following
   * properties:
   *
   * @return void
   */
  public function _sendMail() {
    if($this->forceException) throw new Zend_Mail_Transport_Exception('Unable to send mail.');
    $this->mail       = $this->_mail;
    $this->subject    = $this->_mail->getSubject();
    $this->from       = $this->_mail->getFrom();
    $this->returnPath = $this->_mail->getReturnPath();
    $this->headers    = $this->_headers;
    $this->called     = true;
  }
}
