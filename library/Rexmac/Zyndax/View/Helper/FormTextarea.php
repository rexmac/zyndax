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
 * Helper to generate a "textarea" element
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormTextarea extends \Zend_View_Helper_FormElement {
  /**
   * The default number of rows for a textarea.
   *
   * @access public
   *
   * @var int
   */
  public $rows = 24;

  /**
   * The default number of columns for a textarea.
   *
   * @access public
   *
   * @var int
   */
  public $cols = 80;

  /**
   * Generates a 'textarea' element.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param mixed $value The element value.
   *
   * @param array $attribs Attributes for the element tag.
   *
   * @return string The element XHTML.
   */
  public function formTextarea($name, $value = null, $attribs = null) {
    $info = $this->_getInfo($name, $value, $attribs);
    extract($info); // name, value, attribs, options, listsep, disable

    // is it disabled?
    $disabled = '';
    if($disable) {
      // disabled.
      $disabled = ' disabled="disabled"';
    }

    // Make sure that there are 'rows' and 'cols' values
    // as required by the spec.  noted by Orjan Persson.
    if(empty($attribs['rows'])) {
      $attribs['rows'] = (int) $this->rows;
    }
    if(empty($attribs['cols'])) {
      $attribs['cols'] = (int) $this->cols;
    }

    // build the element
    $xhtml = '<textarea name="' . $this->view->escape($name) . '"'
            . ' id="' . $this->view->escape($id) . '"'
            . $disabled
            . $this->_htmlAttribs($attribs) . '>'
            . $value . '</textarea>';

    return $xhtml;
  }
}
