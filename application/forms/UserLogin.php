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
use Rexmac\Zyndax\Form\Decorator\DescriptionTooltip;

/**
 * Login form
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Application_Form_UserLogin extends \Rexmac\Zyndax\Form\SecureForm {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setName('userLoginForm');
    $this->setMethod('post');

    $this->addElement('text', 'username', array(
      'filters'    => array('StringTrim', 'StringToLower'),
      'validators' => array(
        array('StringLength', false, array(0, 50)),
      ),
      'required'   => true,
      'label'  => 'Username:',
      'decorators' => array(
        'ViewHelper',
        new DescriptionTooltip(),
        'Label',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->addElement('password', 'password', array(
      'filters'     => array('StringTrim'),
      'validators'  => array(
        array('StringLength', false, array(0, 50)),
      ),
      'required'    => true,
      'label'   => 'Password:',
      'description' => '<a href="'.$this->getView()->url(array(), 'lostPassword').'" title="Forgot your password?">Forgot?</a>',
      'decorators'  => array(
        'ViewHelper',
        array('Description', array('class' => 'description', 'escape' => false)),
        'Label',
        array('HtmlTag', array('tag' => 'li')),
      )
    ));

    $this->addElement('submit', 'login', array(
      'required' => false,
      'ignore'   => true,
      'label'    => 'Login',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li', 'class' => 'align-content-center')),
      ),
    ));

    #$this->addElement('hash', 'csrf', array(
    #  'ignore' => true,
    #));
  }
}
