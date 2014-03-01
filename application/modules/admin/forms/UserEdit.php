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

use Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Service\AclRoleService,
    \Zend_Validate_Regex;

/**
 * Admin edit user form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_Form_UserEdit extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * Initialize "edit user" form fields with validators and decorators.
   *
   * @return void
   */
  public function init() {
    $this->setName('userEditForm');
    $this->setMethod('post');

    $this->_addUserAuthFieldset();
    $this->_addContactInfoFieldset();
    $this->_addStatusFieldset();
    $this->_addButtons();
  }

  /**
   * Pre-validate form field values
   *
   * @param array $data Form field values
   * @return void
   */
  public function preValidate($data) {
    if(!empty($data['newPassword'])) {
      $this->newPassword->setRequired(true);
      $this->newPasswordConfirm->setRequired(true);
    }
  }

  /**
   * Set form field default values
   *
   * @param User $user
   * @return void
   */
  public function setDefaults(User $user) {
    $profile = $user->getProfile();
    parent::setDefaults(array(
      'userId'    => $user->getId(),
      'username'  => $user->getUsername(),
      'role'      => $user->getRole()->getId(),
      'firstName' => $profile->getFirstName(),
      'lastName'  => $profile->getLastName(),
      'email'     => $user->getEmail(),
      'phone'     => $profile->getPhone(),
      'active'    => $user->getActive(),
      'locked'    => $user->getLocked(),
    ));
  }

  /**
   * Add user auth fieldset
   *
   * @return void
   */
  private function _addUserAuthFieldset() {
    $this->addElement('hidden', 'userId', array(
      'filters'    => array('Int'),
      'required'   => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('text', 'username', array(
      'filters'    => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators' => array(array('StringLength', false, array(3, 20))),
      'required'   => true,
      'label'      => 'Username:',
    ));

    $this->addElement('password', 'newPassword', array(
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
      'required'   => false,
      'label'      => 'New password:',
    ));

    $this->addElement('password', 'newPasswordConfirm', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('Identical', false, array('token' => 'newPassword'))),
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Confirm password:',
    ));

    $this->addElement('select', 'role', array(
      'filters'      => array('StringTrim'),
      'validators'   => array(),
      'required'     => true,
      'label'        => 'Role:',
      'multiOptions' => AclRoleService::getOptionsForSelect(true),
    ));

    $this->addDisplayGroup(array('username', 'newPassword', 'newPasswordConfirm', 'role'), 'userAuth', array(
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
   * Add status fieldset
   *
   * @return void
   */
  private function _addStatusFieldset() {
    $this->addElement('hidden', 'active', array(
      'filters'    => array('Int'),
      'required'   => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('checkbox', 'locked', array(
      'required'   => true,
      'label'  => 'Locked:',
    ));
    #$this->getElement('locked')->getDecorator('Label')->setRequiredSuffix('<span class="required">&nbsp;&nbsp;</span>');

    $this->addDisplayGroup(array('active', 'locked'), 'userStatus', array(
      'legend'     => 'User Status',
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

    $this->addElement('submit', 'edit', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Save',
      'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('cancel', 'edit'), 'submitButtons', array(
      'decorators'   => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'li', 'class' => 'align-content-center submit-buttons')),
      ),
    ));
  }
}
