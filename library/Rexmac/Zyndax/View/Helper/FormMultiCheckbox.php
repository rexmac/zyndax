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

use \Zend_Filter_Alnum,
    \Zend_View_Abstract;

/**
 * Helper class for displaying multiple HTML checkboxes
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormMultiCheckbox extends \Zend_View_Helper_FormRadio {
  /**
   * Input type to use
   * @var string
   */
  protected $_inputType = 'checkbox';

  /**
   * Whether or not this element represents an array collection by default
   * @var bool
   */
  protected $_isArray = true;

  /**
   * Generates a set of checkbox button elements.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param mixed $value The checkbox value to mark as 'checked'.
   *
   * @param array|string $attribs Attributes added to each radio.
   *
   * @param array $options An array of key-value pairs where the array
   * key is the checkbox value, and the array value is the radio text.
   *
   * @param string $listsep List separator
   *
   * @return string The radio buttons XHTML.
   */
  public function formMultiCheckbox($name, $value = null, $attribs = null, $options = null, $listsep = "") {//<br />\n") {
    #return $this->formRadio($name, $value, $attribs, $options, $listsep);

    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable

    // retrieve attributes for labels (prefixed with 'label_' or 'label')
    $label_attribs = array();
    foreach($attribs as $key => $val) {
      $tmp    = false;
      $keyLen = strlen($key);
      if((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
        $tmp = substr($key, 6);
      } elseif((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
        $tmp = substr($key, 5);
      }

      if($tmp) {
        // make sure first char is lowercase
        $tmp[0] = strtolower($tmp[0]);
        $label_attribs[$tmp] = $val;
        unset($attribs[$key]);
      }
    }

    $labelPlacement = 'append';
    foreach($label_attribs as $key => $val) {
      switch(strtolower($key)) {
        case 'placement':
          unset($label_attribs[$key]);
          $val = strtolower($val);
          if(in_array($val, array('prepend', 'append'))) {
            $labelPlacement = $val;
          }
          break;
      }
    }

    // the radio button values and labels
    $options = (array) $options;

    // build the element
    $markup = '';
    $list  = array();

    // should the name affect an array collection?
    $name = $this->view->escape($name);
    if($this->_isArray && ('[]' != substr($name, -2))) {
      $name .= '[]';
    }

    // ensure value is an array to allow matching multiple times
    $value = (array) $value;

    // XHTML or HTML end tag?
    $endTag = ' />';
    if(($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
      $endTag= '>';
    }

    // add radio buttons to the list.
    $filter = new Zend_Filter_Alnum();
    foreach($options as $opt_value => $opt_label) {
      // Should the label be escaped?
      if($escape) {
        $opt_label = $this->view->escape($opt_label);
      }

      // is it disabled?
      $disabled = '';
      if(true === $disable) {
        $disabled = ' disabled="disabled"';
      } elseif(is_array($disable) && in_array($opt_value, $disable)) {
        $disabled = ' disabled="disabled"';
      }

      // is it checked?
      $checked = '';
      if(in_array($opt_value, $value)) {
        $checked = ' checked="checked"';
      }

      // generate ID
      $optId = $id . '-' . $filter->filter($opt_value);

      // Wrap the radios in labels
      $radio = '<li><label'
             . $this->_htmlAttribs($label_attribs) . ' for="' . $optId . '">'
             . (('prepend' == $labelPlacement) ? $opt_label : '')
             . '<input type="' . $this->_inputType . '"'
             . ' name="' . $name . '"'
             . ' id="' . $optId . '"'
             . ' value="' . $this->view->escape($opt_value) . '"'
             . $checked
             . $disabled
             . $this->_htmlAttribs($attribs)
             . $endTag
             . (('append' == $labelPlacement) ? $opt_label : '')
             . '</label></li>';

      // add to the array of radio buttons
      $list[] = $radio;
    }

    // done!
    $markup .= '<ol class="one-line">'.implode($listsep, $list).'</ol>';

    return $markup;
  }
}
