<?php
/**
 * Zyndax
 *
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

use \Zend_Registry;

/**
 * Helper class for generating CSS class names for body element
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class BodyClass extends \Zend_View_Helper_Abstract {

  /**
   * Helper method to generate CSS class names for <body>
   *
   * @return string
   */
  public function bodyClass() {
    if($this->view) {
      $class = array();

      $acl  = Zend_Registry::get('acl');
      $userCanViewAdmin = $acl->isUserAllowed('mvc:admin', 'view');

      if($page = $this->view->navigation()->findActive($this->view->navigation()->getContainer())) {
        $module = $page['page']->getModule();
        if($userCanViewAdmin && $module === 'default') $module = 'admin';
        $class[] = $module;
        $class[] = $page['page']->getController();
        $class[] = $page['page']->getAction();
      }

      return implode(' ', $class);
    }
  }
}
