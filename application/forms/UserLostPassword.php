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
 * User lost password form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Application_Form_UserLostPassword extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setName('userLostPasswordForm');
    $this->setMethod('post');

    /*
    $this->addElement('text', 'email', array(
      'filters'    => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators' => array(
        array('StringLength', false, array(1, 255)),
        'EmailAddress'
      ),
      'required'   => true,
      'label'  => 'Email address:',
    ));
    */

    $this->addElement('text', 'username', array(
      'filters'     => array('HtmlEntities', 'StringTrim', 'StringToLower'),
      'validators'  => array(array('StringLength', false, array(3, 20))),
      'required'    => true,
      'label'       => 'Username:'
    ));

    $this->addElement('submit', 'submit', array(
      'required' => false,
      'ignore'   => true,
      'label'    => 'Submit',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li', 'class' => 'align-content-center')),
      ),
    ));
  }
}
