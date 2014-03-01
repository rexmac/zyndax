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
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Controller;

use \DateTime,
    \DateTimeZone,
    \Zend_Registry;

/**
 * Abstract Zend controller action class to extract messages from session data
 * for XmlHttpRequest requests.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
abstract class Action extends \Zend_Controller_Action {

  /**
   * ACL object
   *
   * @var Rexmac\Zyndax\Acl
   */
  protected $_acl = null;

  /**
   * Current DateTime
   *
   * @var DateTime
   */
  protected $_now = null;

  /**
   * User entity
   *
   * @var Rexmac\Zyndax\Entity\User
   */
  protected $_user = null;

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    $this->_acl  = Zend_Registry::get('acl');
    $this->_user = $this->_acl->getUser();

    $timeZone = 'America/Los_Angeles';
    if(null !== $this->_user && null !== ($userProfile = $this->_user->getProfile())) {
      if(null !== ($tz = $userProfile->getTimeZone())) {
        $timeZone = $tz->getName();
      }
    }
    $this->_now = new DateTime('now', new DateTimeZone($timeZone));
    if(!$this->getRequest()->isXmlHttpRequest()) {
      $this->view->now = $this->_now;
    }

    $this->_helper->contextSwitch()->setContext('Download CSV', array(
      'suffix'    => 'csv',
      'headers'   => array(
        'Pragma'              => 'public', // Required by IE?
        'Expires'             => '0',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Cache-Control'       => 'public',
        'Content-Description' => 'File Transfer',
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename=report.csv',
        #'Content-Transfer-Encoding' => 'binary',
      ),
    ));
  }

  /**
   * Post-dispatch method to extract messages from session data for
   * XmlHttpRequest requests. Messages are stored in view variable.
   *
   * @return void
   */
  public function postDispatch() {
    if($this->getRequest()->isXmlHttpRequest()) {
      $messages = $this->view->messages()->getMessages();
      if(!empty($messages)) {
        $this->view->messages = $messages;
      }
    }
  }
}
