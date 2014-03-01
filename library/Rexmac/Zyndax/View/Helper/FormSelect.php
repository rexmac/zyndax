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
 * Helper class to represent an HTML <select> element
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormSelect extends \Zend_View_Helper_FormElement {
  /**
   * Generates 'select' list of options.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param mixed $value The option value to mark as 'selected'; if an
   * array, will mark all values in the array as 'selected' (used for
   * multiple-select elements).
   *
   * @param array|string $attribs Attributes added to the 'select' tag.
   *
   * @param array $options An array of key-value pairs where the array
   * key is the radio value, and the array value is the radio text.
   *
   * @param string $listsep When disabled, use this list separator string
   * between list values.
   *
   * @return string The select tag and options XHTML.
   */
  public function formSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, id, value, attribs, options, listsep, disable
    #list($name, $id, $value, $attribs, $options, $listsep, $disable, $escape) = $info;

    // force $value to array so we can compare multiple values to multiple
    // options; also ensure it's a string for comparison purposes.
    $value = array_map('strval', (array) $value);

    // check if element may have multiple values
    $multiple = '';

    if(substr($name, -2) == '[]') {
      // multiple implied by the name
      $multiple = ' multiple="multiple"';
    }

    if(isset($attribs['size'])) {
      $size = $attribs['size'];
    } else {
      $size = '';
    }

    if(isset($attribs['multiple'])) {
      // Attribute set
      if($attribs['multiple']) {
        // True attribute; set multiple attribute
        $multiple = ' multiple="multiple"';

        // Make sure name indicates multiple values are allowed
        if(!empty($multiple) && (substr($name, -2) != '[]')) {
          $name .= '[]';
        }
      } else {
        // False attribute; ensure attribute not set
        $multiple = '';
      }
      unset($attribs['multiple']);
    }

    // now start building the XHTML.
    $disabled = '';
    if(true === $disable) {
      $disabled = ' disabled="disabled"';
    }

    // Build the surrounding select element first.
    $xhtml = '<select'
           . ' name="' . $this->view->escape($name) . '"'
           . ' id="' . $this->view->escape($id) . '"'
           . $size
           . $multiple
           . $disabled
           . $this->_htmlAttribs($attribs)
           . ">\n    ";

    // build the list of options
    $list       = array();
    $translator = $this->getTranslator();
    foreach((array)$options as $optValue => $option) {
      if(is_array($option) && array_key_exists('label', $option)) {
        $optLabel = $option['label'];
      } else {
        $optLabel = $option;
      }

      if(is_array($optLabel)) {
        $optDisable = '';
        if(is_array($disable) && in_array($optValue, $disable)) {
          $optDisable = ' disabled="disabled"';
        }
        if(null !== $translator) {
          $optValue = $translator->translate($optValue);
        }
        $list[] = '<optgroup'
                . $optDisable
                . ' label="' . $this->view->escape($optValue) .'">';
        foreach($optLabel as $val => $lab) {
          $list[] = $this->_build($val, $lab, $value, $disable, $option);
        }
        $list[] = '</optgroup>';
      } else {
        $list[] = $this->_build($optValue, $optLabel, $value, $disable, $option);
      }
    }

    // add the options to the xhtml and close the select
    $xhtml .= implode("\n    ", $list) . "\n</select>";

    return $xhtml;
  }

  /**
   * Builds the actual <option> tag
   *
   * @param string $value Options Value
   * @param string $label Options Label
   * @param array  $selected The option value(s) to mark as 'selected'
   * @param array|bool $disable Whether the select is disabled, or individual options are
   * @param mixed
   * @return string Option Tag XHTML
   */
  protected function _build($value, $label, $selected, $disable, $options) {
    if(is_bool($disable)) {
      $disable = array();
    }

    $opt = '<option'
         . ' value="' . $this->view->escape($value) . '"'
         #. ' label="' . $this->view->escape($label) . '"';
         #. ' label="' . $this->view->escape(preg_replace('/<span[^>]*>.+<\/span>/', '', $label)) . '"';
         . ' htmllabel="' . $this->view->escape($label) . '"';

    // Class?
    if(is_array($options) && isset($options['class'])) {
      $opt .= ' class="'.$options['class'].'"';
    }

    // selected?
    if(in_array((string) $value, $selected)) {
      $opt .= ' selected="selected"';
    }

    // disabled?
    if(in_array($value, $disable)) {
      $opt .= ' disabled="disabled"';
    }

    #$opt .= '>' . $this->view->escape($label) . "</option>";
    $opt .= '>' . $this->view->escape(preg_replace('/<span[^>]*>.+<\/span>/', '', $label)) . "</option>";

    return $opt;
  }
}
