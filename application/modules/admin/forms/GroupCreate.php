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
 * Admin group creation form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_Form_GroupCreate extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * Initialize "create group" form fields with validators and decorators.
   *
   * @return void
   */
  public function init() {
    $this->setName('group_create_form');
    $this->setMethod('post');

    $this->addElement('text', 'name', array(
      'filters'    => array('HtmlEntities', 'StringTrim'),
      'validators' => array(
        #array('Alnum', true),
        array('StringLength', false, array(1, 50)),
      ),
      'required'   => true,
      'label'      => 'Name:',
    ));

    $this->addElement('text', 'description', array(
      'filters'    => array('HtmlEntities', 'StringTrim'),
      'validators' => array(
        #array('Alnum', true),
        array('StringLength', false, array(1, 255)),
      ),
      'required'   => true,
      'label'      => 'Description:'
    ));

    $this->addElement('checkbox', 'locked', array(
      'required' => true,
      'label'    => 'Locked:',
    ));

    $this->addDisplayGroup(array('name', 'description', 'locked'), 'groupInfo', array(
      'legend'     => 'Group Info',
      'decorators' => array(
        'FormElements',
        'Fieldset'
      )
    ));

    $this->setElementDecorators(array(
      'ViewHelper',
      array('Label', array('requiredSuffix' => '<span class="required">&nbsp;*</span>', 'escape' => false)),
      array('HtmlTag', array('tag' => 'li'))
    ));
    $this->getElement('locked')->getDecorator('Label')->setRequiredSuffix('<span class="required">&nbsp;&nbsp;</span>');

    $this->addElement('submit', 'edit', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Save',
      'decorators' => array('ViewHelper')
    ));
  }
}
