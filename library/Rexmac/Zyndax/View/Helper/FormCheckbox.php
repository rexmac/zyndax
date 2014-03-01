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
 * The sole purpose of this class is to override the behavior of the default
 * Zend FormMultiCheckbox element (which inserts a hidden <input> field).
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormCheckbox extends \Zend_View_Helper_FormCheckbox {
  /**
   * Generates a 'checkbox' element.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   * @param mixed $value The element value.
   * @param array $attribs Attributes for the element tag.
   * @param array $checkedOptions Array of options checked by default
   * @return string The element XHTML.
   */
  public function formCheckbox($name, $value = null, $attribs = null, array $checkedOptions = null) {
    $info = $this->_getInfo($name, $value, $attribs);
    extract($info); // name, id, value, attribs, options, listsep, disable

    $checked = false;
    if(isset($attribs['checked']) && $attribs['checked']) {
      $checked = true;
      unset($attribs['checked']);
    } elseif(isset($attribs['checked'])) {
      $checked = false;
      unset($attribs['checked']);
    }

    $checkedOptions = self::determineCheckboxInfo($value, $checked, $checkedOptions);

    // is the element disabled?
    $disabled = '';
    if($disable) {
      $disabled = ' disabled="disabled"';
    }

    // XHTML or HTML end tag?
    $endTag = ' />';
    if(($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
      $endTag= '>';
    }

    // build the element
    $xhtml = '';
    if(!$disable && !strstr($name, '[]')) {
      #$xhtml = $this->_hidden($name, $checkedOptions['uncheckedValue']);
    }
    $xhtml .= '<input type="checkbox"'
            . ' name="' . $this->view->escape($name) . '"'
            . ' id="' . $this->view->escape($id) . '"'
            . ' value="' . $this->view->escape($checkedOptions['checkedValue']) . '"'
            . $checkedOptions['checkedString']
            . $disabled
            . $this->_htmlAttribs($attribs)
            . $endTag;

    return $xhtml;
  }
}

