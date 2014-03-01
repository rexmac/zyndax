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
 * Helper for rendering navigation bar
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.github.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Navbar extends \Zend_View_Helper_Abstract {

  /**
   * Application ACL object
   *
   * @var Rexmac\Zyndax\Acl\Acl
   */
  private $_acl = null;

  /**
   * Render navigation bar
   *
   * @todo Integrate with Zend_Navigation?
   * @return string
   */
  public function navbar() {
    if(!Zend_Auth::getInstance()->hasIdentity()) return;

    $this->_acl = Zend_Registry::get('acl');

    $page = $this->view->navigation()->findActive($this->view->navigation()->getContainer());

    if($page && $page['page']->getRoute() === 'adminSitePreview') {
      $markup = $this->_getNavbar(true);
    } elseif($this->_acl->isUserAllowed('mvc:admin:all', 'view')) {
      $markup = $this->_getAdminNavbar();
    } else {
      $markup = $this->_getNavbar();
    }

    return $markup;
  }

  /**
   * Return HTML for admin navbar
   *
   * @return string
   */
  private function _getAdminNavbar() {
      $markup = '<ul role="navigation" class="nav">
  <li><a class="site-logo" href="' . $this->view->url(array(), 'home') . '" title="Home"><img src="/images/logo_dark.png" alt="' . Zend_Registry::get('siteName') . '" width="149" height="40" /></a></li>
  <li>
    <a href="#" title="Miscellaneous admin">Admin</a>
    <ul>
      <li><a href="' . $this->view->url(array(), 'adminUsers') . '" title="Manage users">User Management</a></li>
    </ul>
  </li>
  <li>
    <a href="' . $this->view->url(array(), 'adminSiteManagement') . '" title="Manage site content">Site Content</a>
    <ul>
      <li><a href="' . $this->view->url(array(), 'adminSitePrivacyPolicy') . '" title="Manage privacy policy">Privacy Policy</a></li>
      <li><a href="' . $this->view->url(array(), 'adminSiteTOS') . '" title="Manage terms of service">Terms and Condiditions</a></li>
      <li><a href="' . $this->view->url(array(), 'adminSiteTheme') . '" title="Manage publisher UI theme">Publisher UI Theme</a></li>
    </ul>
  </li>
</ul>';

    $user = $this->_acl->getUser();
    $markup .= '<ul role="navigation" class="nav user">
  <li>
    <a href="' . $this->view->url(array(), 'userProfile') . '" title="View your profile">
      <span class="icon-wrapper"><span class="user-icon"></span></span>' .
      $user->getProfile()->getFirstName() . '
    </a>
    <ul>
      <li><a href="' . $this->view->url(array(), 'userProfile') . '" title="View your profile">Settings</a></li>
      <li><a href="' . $this->view->url(array(), 'logout') . '" title="Logout">Logout</a></li>
    </ul>
  </li>
</ul>';

    return $markup;
  }

  /**
   * Return HTML for regular user (non-admin) navbar
   *
   * @return string
   */
  private function _getNavbar() {
    $markup = '<ul class="dropdown">
  <li><a href="'.$this->view->url(array(), 'home').'" title="Home"><img src="/images/nav_home.png" alt="Home"></a></li>
  <li><a href="'.$this->view->url(array(), 'userProfile').'" title="View your profile"><img src="/images/nav_profile.png" alt="My Profile"></a>
    <ul class="accountMenu">
      <li><a href="'.$this->view->url(array(), 'userProfile').'" title="Update your profile information">Update Profile</a></li>
      <li class="last"><a href="'.$this->view->url(array(), 'changePassword').'" title="Change your password">Change Password</a></li>
    </ul>
  </li>
</ul>';

    return $markup;
  }
}
