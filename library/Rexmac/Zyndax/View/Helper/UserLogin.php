<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL:
 * http://rexmac.github.com/license/bsd2c.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to
 * license@rexmac.com so we can send you a copy.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.github.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\View\Helper;

use \Zend_Auth,
    \Zend_Registry;

/**
 * View helper tho display user login info in header
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.github.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserLogin extends \Zend_View_Helper_Abstract {

  /**
   * Helper method to display user login info
   *
   * @return string
   */
  public function userLogin() {
    return $this->direct();
  }

  /**
   * Helper method to display user login info
   *
   * @return string
   */
  public function direct() {
    if(Zend_Auth::getInstance()->hasIdentity()) {
      $user = Zend_Registry::get('acl')->getUser();

      $profileUrl = $this->view->url(array(), 'userProfile');
      $logoutUrl  = $this->view->url(array(), 'logout');
      $username   = $user->getUsername();
      if(strlen($username) > 12) {
        $username = substr($username, 0, 6).'&hellip;';
      }   

      $markup = '<div class="first">Welcome back: <span class="bold">' . $user->getProfile()->getFirstName() . '</span></div>'
              . '<div>Login Name: <span class="bold">'. $username . '</span></div>'
              . '<div><a href="' . $profileUrl . '">Profile</a> | <a href="' . $logoutUrl . '" title="Logout">Logout</a></div>';
      return $markup;
    } else {
      /*$form = new \Application_Form_UserLogin();
      $form->setDecorators(array(
        'FormElements',
        'Form'
      ));
      return $form->render();*/

      $form = new \Application_Form_UserLogin();
      $form->render();

      $markup = '<form id="userLogin" enctype="application/x-www-form-urlencoded" method="post" action="'.$this->view->url(array(), 'login').'">'
              . $form->getElement('csrf')->render()
              . '<label for="loginUsername">Username:</label>'
              . '<input type="text" name="username" id="loginUsername">'
              . '<label for="loginPassword">Password:</label>'
              . '<input type="password" name="password" id="loginPassword">'
              . '<input type="submit" name="login" id="login" value="Login">'
              . '</form>';
      return $markup;
    }
  }
}
