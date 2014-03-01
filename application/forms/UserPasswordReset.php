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

/**
 * User password reset form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Application_Form_UserPasswordReset extends \Rexmac\Zyndax\Form\SecureForm {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setName('userPasswordResetForm');
    $this->setMethod('post');

    $this->addElement('password', 'password', array(
      'filters'     => array('StringTrim'),
      'validators'  => array(
        array('StringLength', false, array(6, 50)),
      ),
      'required'    => true,
      'label'       => 'Password:',
      'description' => 'Minimum of 6 characters. Must include at least one number and one letter.'
    ));

    $this->addElement('password', 'passwordConfirm', array(
      'filters'    => array('StringTrim'),
      'validators' => array(
        array('StringLength', false, array(6, 50)),
        array('Identical', false, array('token' => 'password')),
      ),
      'required'   => true,
      'ignore'     => true,
      'label'      => 'Confirm password:',
    ));

    $this->addElement('submit', 'submit', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Submit',
      'decorators' => array('ViewHelper'),
    ));
  }
}
