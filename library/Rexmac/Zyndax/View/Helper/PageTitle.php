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
 * Helper class for displaying page titles
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class PageTitle extends \Zend_View_Helper_Placeholder_Container_Standalone {

  /**
   * Suffix to be appended to output
   *
   * @var string
   */
  private $_suffix = '';

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Helper method to display page title
   *
   * @param string $suffix Suffix to be appended to output
   * @return string
   */
  public function pageTitle($suffix = '') {
    $this->_suffix .= $suffix;
    return $this;
  }

  /**
   * Return page title
   *
   * @return string
   */
  public function toString() {
    if($this->view) {
      if($page = $this->view->navigation()->findActive($this->view->navigation()->getContainer())) {
        return $this->view->escape($page['page']->getLabel()) . $this->_suffix;
      }
      return ''.$this->_suffix;
    }
  }
}
