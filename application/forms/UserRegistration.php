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
use Rexmac\Zyndax\Form\Decorator\FormErrors,
    Rexmac\Zyndax\Form\Element\SocialNetworkIdentity,
    \Zend_Controller_Front as FrontController,
    \Zend_Validate_Regex;

/**
 * User registration form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Application_Form_UserRegistration extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setMethod('post');

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

    $this->_addUserAuthFieldset();
    $this->_addContactInfoFieldset();
  }

  /**
   * Pre-validate form
   *
   * Adds dyanmic SocialNetworkIdentity fields based on submitted POST data
   *
   * @param array $data Array of POST data
   * @return void
   */
  public function preValidate(array $data) {
    foreach($data as $key => $value) {
      if(1 === preg_match('/^social(\d+)$/', $key, $matches)) {
        $this->_addSocial($matches[1]);
      }
    }
  }

  /**
   * Add user auth fieldset
   *
   * @return void
   */
  private function _addUserAuthFieldset() {
    $this->addElement('text', 'username', array(
      'filters'     => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators'  => array(array('StringLength', false, array(3, 20))),
      'required'    => true,
      'label'       => 'Username:',
      'description' => 'Minimum of 3 characters. No spaces.',
    ));

    $this->addElement('password', 'password', array(
      'filters'     => array('StringTrim'),
      'validators'  => array(
        array('StringLength', false, array(6, 255)),
        array('Regex', false, array(
          'pattern'  => '/[a-z]\d|\d[a-z]/',
          'messages' => array(
            Zend_Validate_Regex::NOT_MATCH => 'Password must contain one letter, one number, and at least 6 characters.',
            Zend_Validate_Regex::ERROROUS  => 'Internal application error. Please try again.',
          )
        )),
       ),
      'required'    => true,
      'label'       => 'Password:',
      'description' => 'Minimum of 6 characters. Must include at least one number and one letter.',
  ));

    $this->addElement('password', 'passwordConfirm', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('Identical', false, array('token' => 'password'))),
      'required'   => true,
      'ignore'     => true,
      'label'      => 'Confirm'.(FrontController::getInstance()->getRequest()->isMobileRequest() ? '' : ' password').':',
    ));

    $this->addDisplayGroup(array('username', 'password', 'passwordConfirm'), 'userAuth', array(
      'legend'     => 'Login Information',
      'decorators' => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'ol')),
        'Fieldset'
      )
    ));
  }

  /**
   * Add contact info fieldset
   *
   * @return void
   */
  private function _addContactInfoFieldset() {
    $this->addElement('text', 'firstname', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('StringLength', false, array(2, 20))),
      'required'   => true,
      'label'      => 'First Name',
    ));

    $this->addElement('text', 'lastname', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('StringLength', false, array(2, 20))),
      'required'   => true,
      'label'      => 'Last Name',
    ));

    $this->addElement('text', 'email', array(
      'filters'    => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators' => array(
        array('StringLength', false, array(1, 255)),
        'EmailAddress'
      ),
      'required'   => true,
      'label'      => 'Email address:',
    ));

    $this->addElement('text', 'emailConfirm', array(
      'filters'    => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators' => array(array('Identical', false, array('token' => 'email'))),
      'required'   => true,
      'ignore'     => true,
      'label'      => 'Confirm email:',
    ));

    $this->addElement('text', 'phone', array(
      'filters'    => array('StringTrim'),
      'validators' => array(array('StringLength', false, array(10, 20))),
      'required'   => false,
      'label'      => 'Phone Number:',
    ));

    $this->_addSocial();

    $this->addDisplayGroup(array('firstname', 'lastname', 'email', 'emailConfirm', 'phone', 'social1'), 'contactInfo', array(
      'legend'     => 'Contact Information',
      'decorators' => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'ol')),
        'Fieldset'
      )
    ));
  }

  /**
   * Add social network account fields
   *
   * @param int $number
   * @return void
   */
  private function _addSocial($number = 1) {
    $social = $this->addElement(new SocialNetworkIdentity('social' . $number, array(
      'filters'    => array(),
      'validators' => array(),
      'required'   => false,
      'label'      => $number === 1 ? 'Social Identities:' : 'Social Identity ' . $number . ':',
      'decorators' => array(
        array('SocialNetworkIdentity', array('link' => true)),
        array('Label', array(
          'optionalSuffix' => '<span class="optional"> &nbsp;</span>',
          'requiredSuffix' => '<span class="required"> *</span>',
          'escape'         => false
        )),
        array('HtmlTag', array('tag' => 'li'))
      )
    )));
    if($number > 1) {
      $social->getDecorator('Label')->setOption('class', 'invisible');
    }
  }
}
