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
 * Helper class for displaying button menus.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormButtonMenu extends \Zend_View_Helper_FormElement {

  /**
   * Generates a button menu.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param mixed $value The element value.
   *
   * @param array|string $attribs Attributes for the element tag.
   *
   * @param array $options An array of key-value pairs for the menu items.
   *
   * @return string The element HTML.
   */
  public function formButtonMenu($name, $value = null, $attribs = null, $options = null) {
    $info = $this->_getInfo($name, $value, $attribs, $options);
    extract($info); // name, value, attribs, options, disable, id

    // Get content
    $content = '';
    if(isset($attribs['content'])) {
      $content = $attribs['content'];
      unset($attribs['content']);
    } else {
      $content = $value;
    }
    $content = ($escape) ? $this->view->escape($content) : $content;

    $markup = '<button'
      . ' name="' . $this->view->escape($name) . '"'
      . ' id="' . $this->view->escape($id) . '"'
      . ' type="button"';

    // Add a value if one is given
    if(!empty($value)) {
      $markup .= ' value="' . $this->view->escape($value) . '"';
    }

    // Add attributes and close start tag
    $markup .= $this->_htmlAttribs($attribs) . '>';

    // Add content and end tag
    $markup .= $content . '</button>';

    $markup .= '<ul id="' . $this->view->escape($id) . 'Menu" class="button-menu">';
    if(is_array($attribs['menuOptions'])) {
      foreach($attribs['menuOptions'] as $v) {
        $markup .= "<li>{$v}</li>";
      }
    }
    $markup .= '</ul>';

    return $markup;
  }
}
