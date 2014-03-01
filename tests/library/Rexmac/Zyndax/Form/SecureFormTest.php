<?php

namespace Rexmac\Zyndax\Form;

use \Zend_Form_Decorator_ViewHelper,
    \Zend_Session;

class SecureFormTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    Zend_Session::$_unitTestEnabled = true;
    parent::setUp();
  }

  public function testConstructorSetsDecorators() {
    $form = new TestSecureForm();

    $csrf = $form->getElement('csrf');
    $this->assertInstanceOf('\Zend_Form_Element_Hash', $csrf);

    $decorator = $csrf->getDecorator('ViewHelper');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
  }
}

