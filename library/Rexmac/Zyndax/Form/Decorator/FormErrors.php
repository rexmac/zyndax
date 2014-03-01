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

use \Zend_Form,
    \Zend_Form_Element,
    \Zend_Form_Element_Hash,
    \Zend_View_Interface;

/**
 * Displays all form errors in one view.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class FormErrors extends \Zend_Form_Decorator_FormErrors {

  /**
   * Render element label
   *
   * @param  Zend_Form_Element $element
   * @param  Zend_View_Interface $view
   * @return string Element's label
   */
  public function renderLabel(Zend_Form_Element $element, Zend_View_Interface $view) {
    $label = $element->getLabel();
    if(empty($label)) {
      $label = $element->getName();
    }
    return $this->getMarkupElementLabelStart()
      . $view->escape($label)
      . $this->getMarkupElementLabelEnd();
  }

  /**
   * Recurse through a form object, rendering errors
   *
   * @param  Zend_Form $form
   * @param  Zend_View_Interface $view
   * @return string
   */
  protected function _recurseForm(Zend_Form $form, Zend_View_Interface $view) {
    $content = '';
    foreach($form->getElementsAndSubFormsOrdered() as $subitem) {
      if($subitem instanceof Zend_Form_Element && !$this->getOnlyCustomFormErrors()) {
        $messages = $subitem->getMessages();
        if($subitem instanceof Zend_Form_Element_Hash) {
          if(isset($messages['missingToken'])) {
            #$messages['missingToken'] = 'Form expired. Please try again.';
            $form->addErrorMessage('Form expired. Please try again.');
            unset($messages['missingToken']);
          } elseif(isset($messages['notSame'])) {
            #$messages['notSame'] = 'Form expired. Please try again.';
            $form->addErrorMessage('Form expired. Please try again.');
            unset($messages['notSame']);
          }
        }
        if(count($messages)) {
          $subitem->setView($view);
          $content .= $this->getMarkupListItemStart()
                   .  $this->renderLabel($subitem, $view)
                   .  $view->formErrors($messages, $this->getOptions())
                   .  $this->getMarkupListItemEnd();
        }
      } elseif($subitem instanceof Zend_Form && !$this->ignoreSubForms()) {
        $markup = $this->_recurseForm($subitem, $view);

        if(!empty($markup)) {
          $content .= $this->getMarkupListStart()
                    . $markup
                    . $this->getMarkupListEnd();
        }
      }
    }

    $custom = $form->getCustomMessages();
    if($this->getShowCustomFormErrors() && count($custom)) {
      $content = $this->getMarkupListItemStart()
               . $view->formErrors($custom, $this->getOptions())
               . $this->getMarkupListItemEnd()
               . $content;
    }

    return $content;
  }
}
