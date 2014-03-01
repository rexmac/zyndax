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
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Service;

use \DateTime,
    \Exception,
    Doctrine\Common\Collections\ArrayCollection,
    Rexmac\Zyndax\Crypt\Bcrypt,
    Rexmac\Zyndax\Entity\UserEmailVerification,
    Rexmac\Zyndax\Entity\UserPasswordResetToken,
    Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Service\UserEmailVerificationService,
    Rexmac\Zyndax\Service\UserPasswordResetTokenService,
    Rexmac\Zyndax\Log\Logger,
    Rexmac\Zyndax\Mail\Transport\Mock as MockMailTransport,
    \Zend_Controller_Action_HelperBroker as HelperBroker,
    \Zend_Crypt_Hmac as Hmac,
    \Zend_Mail,
    \Zend_Mail_Transport_Abstract,
    \Zend_Registry,
    \Zend_Session,
    \Zend_View_Helper_ServerUrl,
    \Zend_View_Helper_Url;

/**
 * Service layer to ease the use and management of User entities
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserService extends \Rexmac\Zyndax\Doctrine\Service {

  /** 
   * Return an encrypted password
   *
   * @param string $password Password to be encrypted
   * @return string Enrypted password
   */
  public static function encryptPassword($password) {
    return substr(Bcrypt::create(Hmac::compute(Zend_Registry::get('staticSalt'), 'sha512', $password)), 7);
  }

  /**
   * Verify a user password
   *
   * @param User $user User entity
   * @param string $password Password to be verified
   * @return bool True if password matches
   */
  public static function verifyPassword(User $user, $password) {
    return Bcrypt::verify(
      Hmac::compute(Zend_Registry::get('staticSalt'), 'sha512', $password),
      '$2y$14$' . $user->getPassword()
    );
  }

  /**
   * Send password reset email to user
   *
   * @param User $user
   * @param Zend_Mail_Transport_Abstract $transport [Optional] Zend mail transport class
   * @return void
  */
  public static function sendPasswordResetEmail(User $user, Zend_Mail_Transport_Abstract $transport = null) {
    $serverUrlHelper = new Zend_View_Helper_ServerUrl();
    $urlHelper = HelperBroker::getStaticHelper('url');
    $siteDomain = preg_replace('/^https?:\/\//', '', $serverUrlHelper->serverUrl());
    $siteName   = Zend_Registry::get('siteName');
    $config = Zend_Registry::get('config');
    $from = 'noreply@' . $siteDomain;
    if(!empty($config->mail) && !empty($config->mail->from)) $from = $config->mail->from;
    if(null === $transport) {
      if(Zend_Session::$_unitTestEnabled) {
        $transport = new MockMailTransport();
      } else {
        if(!empty($config->mail) && !empty($config->mail->smtp) && !empty($config->mail->smtp->host)) {
          $options = $config->mail->smtp->toArray();
          unset($options['host']);
          $transport = new Zend_Mail_Transport_Smtp($config->mail->smtp->host, $options);
        }
      }
    }

    UserPasswordResetTokenService::collectGarbage(); // @todo cronjob?

    $resetToken = sha1(mt_rand() . $user->getEmail() . mt_rand());
    if(APPLICATION_ENV === 'testing') {
      $resetLink  = $serverUrlHelper->serverUrl() . '/resetPassword/' . $resetToken;
    } else { // @codeCoverageIgnoreStart
      $resetLink  = $serverUrlHelper->serverUrl() . $urlHelper->url(array('token' => $resetToken), 'resetPassword');
    } // @codeCoverageIgnoreEnd

    // Clear any existing tokens
    UserPasswordResetTokenService::clearTokensForUser($user);

    // Generate a new token
    UserPasswordResetTokenService::create(new UserPasswordResetToken(array(
      'user'        => $user,
      'token'       => $resetToken,
      'requestDate' => new DateTime(),
    )));

    $text = 'Hello ' . $user->getUsername() . ',
We recently received a request to reset your password.

Please use the following link within the next 24 hours to reset your password.

'.$resetLink.'

If you did not request to have your password reset, then please ignore this message.

Thank you,
The '.$siteName.' Team
';

    $html = '<p>Hello ' . $user->getUsername() . ',</p>
<p>We recently received a request to reset your password.</p>

<p>Please use the following link within the next 24 hours to reset your password.</p>

<p><a href="'.$resetLink.'" title="Reset your password">'.$resetLink.'</a></p>

<p>If you did not request to have your password reset, then please ignore this message.</p>

<p>Thank you,<br>
The '.$siteName.' Team</p>
';

    try {
      Logger::info('Attempting to send email to \'' . $user->getEmail() . '\'.');
      $mail = new Zend_Mail('utf-8');
      $mail->setFrom($from, $siteName)
        ->setSubject('['.$siteName.'] Lost password')
        ->setBodyText($text)
        ->setBodyHtml($html)
        ->addTo($user->getEmail());

      $mail->send($transport);
    } catch(Exception $e) {
      Logger::crit($e->getMessage());
      throw $e;
    }
  }

  /**
   * Send email address verification email to user
   *
   * @param User $user
   * @param Zend_Mail_Transport_Abstract $transport [Optional] Zend mail transport class
   * @return void
   */
  public static function sendVerificationEmail(User $user, Zend_Mail_Transport_Abstract $transport = null) {
    $serverUrlHelper = new Zend_View_Helper_ServerUrl();
    $urlHelper = HelperBroker::getStaticHelper('url');
    $siteDomain = preg_replace('/^https?:\/\//', '', $serverUrlHelper->serverUrl());
    $siteName   = Zend_Registry::get('siteName');
    $config = Zend_Registry::get('config');
    $from = 'noreply@' . $siteDomain;
    if(!empty($config->mail) && !empty($config->mail->from)) $from = $config->mail->from;
    if(null === $transport) {
      if(Zend_Session::$_unitTestEnabled) {
        $transport = new MockMailTransport();
      } else {
        if(!empty($config->mail) && !empty($config->mail->smtp) && !empty($config->mail->smtp->host)) {
          $options = $config->mail->smtp->toArray();
          unset($options['host']);
          $transport = new Zend_Mail_Transport_Smtp($config->mail->smtp->host, $options);
        }
      }
    }

    UserEmailVerificationService::collectGarbage(); // @todo cronjob?; should also remove any unverified user accounts

    $verificationToken = sha1(mt_rand() . $user->getEmail() . mt_rand());
    if(APPLICATION_ENV === 'testing') {
      $verificationLink  = $serverUrlHelper->serverUrl() . '/verifyEmail/' . $verificationToken;
    } else { // @codeCoverageIgnoreStart
      $verificationLink  = $serverUrlHelper->serverUrl() . $urlHelper->url(array('token' => $verificationToken), 'verifyEmail');
    } // @codeCoverageIgnoreEnd

    UserEmailVerificationService::create(new UserEmailVerification(array(
      'user'        => $user,
      'token'       => $verificationToken,
      'requestDate' => new DateTime(),
    )));

    $text = 'Hello ' . $user->getUsername().',
Thank you for registering with '.$siteName.'. To activate your account and complete the registration process, please click the
 following link: '.$verificationLink.'.

You are receiving this email because someone recently registered on our site and provided <'.$user->getEmail().'> as their ema
il address. If you did not recently register at '.$siteDomain.', then please ignore this email. Your information will be remov
ed from our system within 24 hours.

Thank you,
The '.$siteName.' Team
';
    $html = '<p>Hello '.$user->getUsername().',</p>
<p>Thank you for registering with '.$siteName.'. To activate your account and complete the registration process, please click 
the following link: <a href="'.$verificationLink.'" title="Verify your email address">'.$verificationLink.'</a>.</p>

<p>You are receiving this email because someone recently registered on our site and provided &lt;'.$user->getEmail().'&gt; as 
their email address. If you did not recently register at '.$siteDomain.', then please ignore this email. Your information will
 be removed from our system within 24 hours.</p>

<p>Thank you,<br>
The '.$siteName.' Team</p>
';

    try {
      Logger::info('Attempting to send email to \'' . $user->getEmail() . '\'.');
      $mail = new Zend_Mail('utf-8');
      $mail->setFrom($from, $siteName)
        ->setSubject('['.$siteName.'] Email Verification')
        ->setBodyText($text)
        ->setBodyHtml($html)
        ->addTo($user->getEmail());

      $mail->send($transport);
    } catch(Exception $e) {
      Logger::crit($e->getMessage());
      throw $e;
    }
  }
}
