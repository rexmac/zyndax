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
use Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Form\Decorator\FormErrors,
    Rexmac\Zyndax\Form\Element\SocialNetworkIdentity;

/**
 * User profile form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Application_Form_UserUpdate extends \Rexmac\Zyndax\Form\Form {

  /**
   * User
   *
   * @var User
   */
  private $_user;

  /**
   * Constructor
   *
   * @param User $user
   * @return void
   */
  public function __construct(User $user) {
    $this->_user = $user;
    parent::__construct();
  }

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setName('userUpdateForm');
    $this->setMethod('post');

    $this->setDecorators(array(
      'FormElements',
      'Form',
      new FormErrors(array(
        'placement'       => 'prepend',
        'markupListEnd'   => '</div>',
        'markupListItemEnd'   => '</span>',
        'markupListItemStart' => '<span>',
        'markupListStart'     => '<div class="form-errors messages error"><span class="message-icon"></span>',
      )),
    ));

    $this->_addContactInfoFieldset();

    // Add Submit button
    $this->addElement('submit', 'Update', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Update',
      'decorators' => array('ViewHelper')
    ));
  }

  /**
   * Add contact info fieldset
   *
   * @return void
   */
  private function _addContactInfoFieldset() {
    $this->addElement('text', 'firstName', array(
      'filters'    => array('HtmlEntities', 'StringTrim'),
      'validators' => array(array('StringLength', false, array(2, 40))),
      'required'   => true,
      'label'      => 'First name:',
      'value'      => $this->_user->getProfile()->getFirstName()
    ));

    $this->addElement('text', 'lastName', array(
      'filters'    => array('HtmlEntities', 'StringTrim'),
      'validators' => array(array('StringLength', false, array(2, 40))),
      'required'   => true,
      'label'      => 'Last name:',
      'value'      => $this->_user->getProfile()->getLastName()
    ));

    // @todo Need to confirm email: 2nd input field and send verification email
    $this->addElement('text', 'email', array(
      'filters'     => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators'  => array(
        array('StringLength', false, array(1, 255)),
        'EmailAddress'
      ),
      'required'    => true,
      'label'       => 'Email address:',
      'value'       => $this->_user->getEmail()
    ));

    $this->addElement('text', 'phone', array(
      'filters'     => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators'  => array(array('StringLength', false, array(10, 20))),
      'required'    => false,
      'label'       => 'Phone number:',
      'value'       => $this->_user->getProfile()->getPhone()
    ));

    $fields = $this->_addSocial();
    $fields = array_merge(array('firstName', 'lastName', 'email', 'phone', 'social1'), $fields);
    $this->addDisplayGroup($fields, 'contactInfo', array(
      'legend'     => 'Contact Information',
      'decorators' => array(
        'FormElements',
        array('HtmlTag', array('tag' => 'ol')),
        'Fieldset'
      )
    ));
  } // close _addContactInfoFieldset

  /**
   * Add social network account fields
   *
   * @return void
   */
  private function _addSocial() {
    $names = array();
    $socialNetworkIdentities = array_values($this->_user->getProfile()->getSocialNetworkIdentities()->toArray());
    $c = count($socialNetworkIdentities);
    if(0 === $c) {
      $names[0] = 'social1';
      $this->addElement(new SocialNetworkIdentity($names[0], array(
        'filters'    => array(),
        'validators' => array(),
        'required'   => false,
        'label'      => 'Social Identities:',
        'decorators' => array(
          array('SocialNetworkIdentity', array('link' => true)),
          array('Label', array(
            'optionalSuffix' => '<span class="optional">&nbsp;&nbsp;</span>',
            'requiredSuffix' => '<span class="required">&nbsp;*</span>',
            'escape'         => false
          )),
          array('HtmlTag', array('tag' => 'li'))
        )
      )));
    } else {
      for($i = 0; $i < $c; ++$i) {
        $socialNetworkIdentity = $socialNetworkIdentities[$i];
        #$names[$i] = 'social'.($i > 0 ? ($i+1) : '');
        $names[$i] = 'social' . ($i + 1);
        $social = new SocialNetworkIdentity($names[$i], array(
          'filters'    => array(),
          'validators' => array(),
          'required'   => false,
          'label'      => 'Social Identities:',
          'value'      => array(
            'name'    => $socialNetworkIdentity->getName(),
            'network' => $socialNetworkIdentity->getSocialNetwork()->getId()
          ),
          'decorators' => array(
            'SocialNetworkIdentity',
            array('Label', array(
              'optionalSuffix' => '<span class="optional">&nbsp;&nbsp;</span>',
              'requiredSuffix' => '<span class="required">&nbsp;*</span>',
              'escape'         => false
            )),
            array('HtmlTag', array('tag' => 'li'))
          )
        ));
        $this->addElement($social);
        if($i > 0) {
          $social->getDecorator('Label')->setOption('class', 'invisible');
        }
        if(($i + 1) === $c) {
          $social->getDecorator('SocialNetworkIdentity')->setOption('link', true);
        }
      }
    }

    return $names;
  }
}
