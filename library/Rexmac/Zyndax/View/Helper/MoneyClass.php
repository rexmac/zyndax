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

/**
 * Helper class to return CSS class name(s) for a monetary value.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class MoneyClass extends \Zend_View_Helper_Abstract {

  /**
   * Returns CSS class name(s) for a moentary value.
   *
   * -param float Monetary value
   * @return string CSS class name(s)
   */
  public function moneyClass() {
    return call_user_func_array(array($this, 'direct'), func_get_args());
  }

  /**
   * Returns CSS class name(s) for a moentary value.
   *
   * -param float Monetary value
   * @return string CSS class name(s)
   */
  public function direct() {
    $value = func_get_arg(0);
    $class = 'monetary ';
    if($value > 0) $class .= 'positive';
    elseif($value < 0) $class .= 'negative';
    else $class .= 'neutral';
    return $class;
  }
}
