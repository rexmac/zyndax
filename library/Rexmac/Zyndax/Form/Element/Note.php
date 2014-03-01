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
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Form\Element;

/**
 * Note form element
 *
 * Form element for displaying simple text or HTML
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Note extends \Zend_Form_Element_Xhtml {
  /**
   * Default form view helper to use for rendering
   * @var string
   */
  public $helper = 'formNote';

  /**
   * Default decorators
   *
   * @return Note
   */
  public function loadDefaultDecorators() {
    if($this->loadDefaultDecoratorsIsDisabled()) {
      return $this;
    }

    $decorators = $this->getDecorators();
    if(empty($decorators)) {
      $this->addDecorator('ViewHelper')
           ->addDecorator('Description', array('tag' => 'p', 'class' => 'description', 'escape' => false))
           ->addDecorator('Label', array(
             'optionalSuffix' => '<span class="optional"> &nbsp;</span>',
             'requiredSuffix' => '<span class="required"> *</span>',
             'escape' => false
           ))
           ->addDecorator('HtmlTag', array('tag' => 'li'));
    }
    return $this;
  }
}
