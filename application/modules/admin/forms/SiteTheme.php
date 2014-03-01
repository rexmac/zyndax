<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * Zyndax is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zyndax is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zyndax.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */

/**
 * Form for manipulating non-admin UI theme settings
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_Form_SiteTheme extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * Initialize form fields with validators and decorators.
   *
   * @return void
   */
  public function init() {
    $this->setName('siteThemeForm');
    $this->setMethod('post');

    $this->_addLogoField();
    $this->_addButtons();

    foreach($this->getElements() as $element) {
      $element->setAttrib('id', $this->getName().'-'.$element->getId());
    }
  }

  /**
   * Set form default values
   *
   * @return void
   */
  public function setDefaults() {
  }

  /**
   * Add logo field
   *
   * @return void
   */
  private function _addLogoField() {
    $this->addElement('file', 'logo', array(
      'label'       => 'Logo image:',
      'description' => 'Valid image types are .png, .jpg, and .gif. Image will be resized if too large to fit in template.',
      'required'    => false,
      'destination' => Zend_Registry::get('config')->uploads->path,
      'maxFileSize' => 10485760,
      'decorators'  => array(
        'File',
        array('Description', array('tag' => 'p', 'class' => 'description', 'escape' => false)),
        array('Label', array(
          'optionalSuffix' => '<span class="optional">&nbsp;&nbsp;</span>',
          'requiredSuffix' => '<span class="required">&nbsp;*</span>',
          'escape' => false
        )),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'validators'  => array(
        array('Count', false, 1),
        array('Size', false, 10485760),
        array('Extension', false, 'gif,jpg,jpeg,png')
      )
    ));
    #))->removeDecorator('ViewHelper')->addDecorator('File')->getDecorator('Description')->setEscape(false);
    #))->getDecorator('Description')->setEscape(false);
  }

  /**
   * Add buttons
   *
   * @return void
   */
  private function _addButtons() {
    $this->addElement('button', 'cancel', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Cancel',
      'decorators' => array('ViewHelper'),
    ));

    $this->addElement('submit', 'submit', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Submit',
      'decorators' => array('ViewHelper'),
    ));

     $this->addDisplayGroup(array('cancel', 'submit'), 'submitButtons', array(
      'decorators'   => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'li', 'class' => 'align-content-center submit-buttons')),
      ),
    ));
  }
}
