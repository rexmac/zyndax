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

use \DateTime,
    \DateTimeZone,
    \Zend_Auth,
    \Zend_Controller_Front as FrontController;

/**
 * Helper class for displaying dates
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class PrintDate extends \Zend_View_Helper_Abstract {

  /**
   * Helper method to display page title
   *
   * @param DateTime $date Date to print
   * @param bool $echo (Optional) If TRUE, then echo output. Else, return as string. Default is TRUE.
   * @return mixed String if $echo is FALSE. Void otherwise.
   */
  public function printDate(DateTime $date = null, $echo = true) {
    if(null === $date) return;
    if(Zend_Auth::getInstance()->hasIdentity()) {
      if($acl = FrontController::getInstance()->getPlugin('Rexmac\Zyndax\Controller\Plugin\Acl')) {
        $user = $acl->getAcl()->getUser();
        $date->setTimezone(new DateTimeZone($user->getProfile()->getTimeZone()->getName()));
      }
    }

    if($echo) echo $date->format('Y-m-d H:i:s');
    else return $date->format('Y-m-d H:i:s');
  }
}
