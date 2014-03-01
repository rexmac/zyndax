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
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\View\Helper;

use Rexmac\Zyndax\Entity\User,
    \Zend_Registry;

/**
 * View helper that provides an HTML anhor tag with a link to
 * activate a user.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserActivateLink extends \Zend_View_Helper_Abstract {

  /**
   * Returns an HTML anchor tag that links to user activate action
   *
   * @param User $user User
   * @param string $label Link label
   * @return string HMTL anchor tag
   */
  public function userActivateLink(User $user, $label = null) {
    if(!Zend_Registry::get('acl')->isUserAllowed('mvc:admin:users:edit', 'view')) return '';

    if($user->getActive() === true) return '';

    if(null === $label) {
      $label = 'Activate';
    }
    $class = ' class="userActivateLink ss_sprite ss_check"';
    return '<a'.$class.' id="userActivateLink-'.$user->getId().'" href="javascript:;">'.$label.'</a>';
  }
}
