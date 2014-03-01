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
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Form\Decorator;

/**
 * Displays form element description as title attribute of icon image
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class DescriptionTooltip extends \Zend_Form_Decorator_Description {

  /**
   * Render a description tooltip
   *
   * @param  string $content
   * @return string
   */
  public function render($content) {
    $element = $this->getElement();
    $view    = $element->getView();
    if(null === $view) {
      return $content;
    }

    $description = $element->getDescription();
    $description = trim($description);

    if(!empty($description) && (null !== ($translator = $element->getTranslator()))) {
      $description = $translator->translate($description);
    }

    /*if(empty($description)) {
      return $content;
    }*/

    $separator = $this->getSeparator();
    $placement = $this->getPlacement();
    #$tag       = $this->getTag();
    #$class     = $this->getClass();
    $escape    = $this->getEscape();
    #$options   = $this->getOptions();

    if($escape) {
      $description = $view->escape($description);
    }

    /*if(!empty($tag)) {
      require_once 'Zend/Form/Decorator/HtmlTag.php';
      $options['tag'] = $tag;
      $decorator = new Zend_Form_Decorator_HtmlTag($options);
      $description = $decorator->render($description);
    }*/
    #$description = '<span title="'.$description.'" class="ui-state-default ui-corner-all ui-icon ui-icon-help">?</span>';
    $markup = '<span ';
    if(empty($description)) {
      $markup .= 'style="visibility:hidden" ';
    } else {
      $markup .= 'title="' . $description . '" ';
    }
    $markup .= 'class="ui-state-default ui-corner-all ui-icon ui-icon-help">?</span>';

    switch($placement) {
      case self::PREPEND:
        return $markup . $separator . $content;
      case self::APPEND:
      default:
        return $content . $separator . $markup;
    }
  }
}
