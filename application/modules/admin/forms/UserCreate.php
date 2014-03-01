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

use Rexmac\Zyndax\Service\AclRoleService,
    \Zend_Validate_Regex;

/**
 * Admin user creation form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_Form_UserCreate extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * Initialize "create user" form fields with validators and decorators.
   *
   * @return void
   */
  public function init() {
    $this->setName('userCreateForm');
    $this->setMethod('post');

    $this->_addUserAuthFieldset();
    $this->_addContactInfoFieldset();
    $this->_addButtons();
  }

  /**
   * Add user auth fieldset
   *
   * @return void
   */
  private function _addUserAuthFieldset() {
    $this->addElement('text', 'username', array(
      'filters'    => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators' => array(array('StringLength', false, array(3, 20))),
      'required'   => true,
      'label'      => 'Username:',
    ));

    $this->addElement('password', 'password', array(
      'filters'    => array('StringTrim'),
      'validators' => array(
        array('StringLength', false, array(6, 255)),
        array('Regex', false, array(
          'pattern'  => '/[a-z]\d|\d[a-z]/',
          'messages' => array(
            Zend_Validate_Regex::NOT_MATCH => 'Password must contain one letter, one number, and at least 6 characters.',
            Zend_Validate_Regex::ERROROUS  => 'Internal application error. Please try again.',
          )
        )),
      ),
      'required'   => true,
      'label'      => 'Password:',
    ));

    $this->addElement('password', 'passwordConfirm', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('Identical', false, array('token' => 'password'))),
      'required'   => true,
      'ignore'     => true,
      'label'      => 'Confirm password:',
    ));

    $this->addElement('select', 'role', array(
      'filters'      => array('StringTrim'),
      'validators'   => array(),
      'required'     => true,
      'label'        => 'Role:',
      'multiOptions' => AclRoleService::getOptionsForSelect(true, false, AclRoleService::findAllExcludingGuest()),
    ));

    $this->addDisplayGroup(array('username', 'password', 'passwordConfirm', 'role'), 'userAuth', array(
      'legend'     => 'Login Information',
      'decorators' => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'ol')),
        'Fieldset'
      ),
    ));
  }

  /**
   * Add contact info fieldset
   *
   * @return void
   */
  private function _addContactInfoFieldset() {
    $this->addElement('text', 'firstName', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('StringLength', false, array(2, 20))),
      'required'   => true,
      'label'      => 'First Name:',
    ));

    $this->addElement('text', 'lastName', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('StringLength', false, array(2, 20))),
      'required'   => true,
      'label'      => 'Last Name:',
    ));

    $this->addElement('text', 'email', array(
      'filters'    => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators' => array(
        array('StringLength', false, array(1, 255)),
        'EmailAddress'
      ),
      'required'   => true,
      'label'      => 'Email address:'
    ));

    $this->addElement('text', 'phone', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('StringLength', false, array(10, 20))),
      'required'   => false,
      'label'      => 'Phone Number:',
    ));

    $this->addDisplayGroup(array('firstName', 'lastName', 'email', 'phone'), 'contactInfo', array(
      'legend'     => 'Contact Information',
      'decorators' => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'ol')),
        'Fieldset'
      )
    ));
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

    $this->addElement('submit', 'create', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Create',
      'decorators' => array('ViewHelper'),
    ));

    $this->addDisplayGroup(array('cancel', 'create'), 'submitButtons', array(
      'decorators'   => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'li', 'class' => 'align-content-center submit-buttons')),
      ),
    ));
  }
}
