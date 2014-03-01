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
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */
use \Zend_Validate_Regex;

/**
 * User password change form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Application_Form_UserPasswordChange extends \Rexmac\Zyndax\Form\SecureForm {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setName('userPasswordChangeForm');
    $this->setMethod('post');

    $this->addElement('password', 'oldPassword', array(
      'filters'     => array('StringTrim'),
      'validators'  => array(array('StringLength', false, array(6, 50))),
      'required'    => true,
      'label'       => 'Current Password:',
    ));

    $this->addElement('password', 'newPassword', array(
      'filters'     => array('StringTrim'),
      'validators'  => array(
        array('StringLength', false, array(6, 40)),
        array('RegEx', false, array(
          'pattern'  => '/[a-z]\d|\d[a-z]/',
          'messages' => array(
            Zend_Validate_Regex::NOT_MATCH => 'Password must contain one letter, one number, and at least 6 characters.',
            Zend_Validate_Regex::ERROROUS  => 'Internal application error. Please try again.',
          )
        )),
      ),
      'required'    => true,
      'label'       => 'New Password:',
      #'description' => 'Minimum of 6 characters. Must include at least one number and one letter.'
    ));

    $this->addElement('password', 'passwordConfirm', array(
      'filters'    => array('StringTrim'),
      'validators' => array(
        array('StringLength', false, array(6, 40)),
        array('Identical', false, array('token' => 'newPassword')),
      ),
      'required'   => true,
      'ignore'     => true,
      'label'      => 'Confirm new password:',
    ));

    $this->addElement('submit', 'submit', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Submit',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li', 'class' => 'align-content-center')),
      ),
      #'style'   => 'margin:3em 0 0 25em;',
    ));
  }
}
