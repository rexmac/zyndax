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

use Rexmac\Zyndax\Service\UserService,
    \Zend_Registry;

/**
 * Helper for rendering a user edit link
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserEditLink extends \Zend_View_Helper_Abstract {

  /**
   * Render user edit link
   *
   * @param Rexmac\Zyndax\Entity\User|int $user User entity or ID
   * @return string
   */
  public function userEditLink($user = null) {
    if(is_numeric($user)) $user = UserService::findOneById($user);
    if(null === $user) return '';
    if(!Zend_Registry::get('acl')->isUserAllowed('mvc:admin:users:edit', 'view')) {
      return $this->view->escape($user->getUsername());
    }
    return sprintf('<a href="%s" title="Edit user">%s</a>',
      $this->view->url(array('userId' => $user->getId()), 'adminUserEdit'),
      $this->view->escape($user->getUsername())
    );
  }
}
