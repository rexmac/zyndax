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
 * @subpackage Form
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Form;

use Rexmac\Zyndax\Controller\Request\HttpRequest,
    Rexmac\Zyndax\Form\Decorator\FormErrors,
    \Zend_Controller_Front as FrontController,
    \Zend_Form_Element as FormElement;

/**
 * Custom Zend_Form class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
abstract class Form extends \Zend_Form {

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    $request = FrontController::getInstance()->getRequest();
    if($request instanceof HttpRequest && $request->isMobileRequest()) {
      $this->setDecorators(array(
        'FormElements',
        'Form',
        new FormErrors(array(
          'placement'           => 'prepend',
          'markupListEnd'       => '</div>',
          'markupListItemEnd'   => '</span>',
          'markupListItemStart' => '<span>',
          'markupListStart'     => '<div class="form-errors messages error"><span class="message-icon"></span>',
        )),
      ));

      $this->setElementDecorators(array(
        'ViewHelper',
        'Label'
      ));
    } else {
      $this->setDecorators(array(
        'FormElements',
        array('HtmlTag', array('tag' => 'ol')),
        'Form',
        new FormErrors(array(
          'placement'           => 'prepend',
          'markupListEnd'       => '</div>',
          'markupListItemEnd'   => '</span>',
          'markupListItemStart' => '<span>',
          'markupListStart'     => '<div class="form-errors messages error"><span class="message-icon"></span>',
        )),
      ));

      $this->setElementDecorators(array(
        'ViewHelper',
        #new \Rexmac\Zyndax\Form\Decorator\DescriptionTooltip(),
        array('Description', array('tag' => 'p', 'class' => 'description', 'escape' => false)),
        array('Label', array(
          'optionalSuffix' => '<span class="optional">&nbsp;&nbsp;</span>',
          'requiredSuffix' => '<span class="required">&nbsp;*</span>',
          'escape' => false
        )),
        array('HtmlTag', array('tag' => 'li'))
      ));
    }

    parent::__construct();
  }

  /**
   * Add a new element
   *
   * $element may be either a string element type, or an object of type
   * Zend_Form_Element. If a string element type is provided, $name must be
   * provided, and $options may be optionally provided for configuring the
   * element.
   *
   * If a Zend_Form_Element is provided, $name may be optionally provided,
   * and any provided $options will be ignored.
   *
   * @param  string|Zend_Form_Element $element
   * @param  string $name
   * @param  array|Zend_Config $options
   * @throws Zend_Form_Exception on invalid element
   * @return Zend_Form_Element
   */
  public function addElement($element, $name = null, $options = null) {
    parent::addElement($element, $name, $options);
    if($element instanceof FormElement) {
      return $element;
    }
    return $this->getElement($name);
  }
}
