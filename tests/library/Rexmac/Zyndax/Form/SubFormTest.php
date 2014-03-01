<?php

namespace Rexmac\Zyndax\Form;

use Rexmac\Zyndax\Form\SubForm;

class SubFormTest extends \PHPUnit_Framework_TestCase {

  public function testSubFormUtilizesDefaultDecorators() {
    $form = new SubForm();
    $this->assertTrue(array_key_exists('Zend_Form_Decorator_FormElements', $form->getDecorators()));
  }
}
