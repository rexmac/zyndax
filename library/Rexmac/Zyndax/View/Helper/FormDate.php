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

use \Zend_View_Abstract;

/**
 * Helper class for displaying a date selector in an HTML form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormDate extends \Zend_View_Helper_FormElement {
  /**
   * Generates a 'text' element.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are used in place of added parameters.
   *
   * @param mixed $value The element value.
   *
   * @param array $attribs Attributes for the element tag.
   *
   * @return string The element XHTML.
   */
  public function formDate($name, $value = null, $attribs = null) {
    $info = $this->_getInfo($name, $value, $attribs);
    extract($info); // name, value, attribs, options, listsep, disable

    // build the element
    $disabled = '';
    if($disable) {
      // disabled
      $disabled = ' disabled="disabled"';
    }

    // XHTML or HTML end tag?
    $endTag = ' />';
    if(($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
      $endTag= '>';
    }

    $xhtml = '<input type="date"'
           . ' name="' . $this->view->escape($name) . '"'
           . ' id="' . $this->view->escape($id) . '"'
           . ' value="' . $this->view->escape($value) . '"'
           . $disabled
           . $this->_htmlAttribs($attribs)
           . $endTag;

    return $xhtml;
  }
}
