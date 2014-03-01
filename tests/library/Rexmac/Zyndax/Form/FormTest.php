<?php

namespace Rexmac\Zyndax\Form;

use Rexmac\Zyndax\Form\Decorator\FormErrors,
    Rexmac\Zyndax\Controller\Request\HttpRequest,
    \Zend_Controller_Front as FrontController,
    \Zend_Form_Decorator_Description,
    \Zend_Form_Decorator_Form,
    \Zend_Form_Decorator_FormElements,
    \Zend_Form_Decorator_HtmlTag,
    \Zend_Form_Decorator_Label,
    \Zend_Form_Decorator_ViewHelper;

class FormTest extends \PHPUnit_Framework_TestCase {

  public function testConstructorSetsDecorators() {
    $form = new TestForm();

    $decorator = $form->getDecorator('FormElements');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_FormElements);

    $decorator = $form->getDecorator('HtmlTag');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
    $options = $decorator->getOptions();
    $this->assertEquals('ol', $options['tag']);

    $decorator = $form->getDecorator('Form');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);

    $decorator = $form->getDecorator('FormErrors');
    $this->assertTrue($decorator instanceof FormErrors);
    $options = $decorator->getOptions();
    $this->assertEquals('prepend', $options['placement']);
    $this->assertEquals('</div>', $options['markupListEnd']);
    $this->assertEquals('</span>', $options['markupListItemEnd']);
    $this->assertEquals('<span>', $options['markupListItemStart']);
    $this->assertEquals('<div class="form-errors messages error"><span class="message-icon"></span>', $options['markupListStart']);
  }

  public function testConstructorSetsElementDecorators() {
    $form = new TestForm();
    foreach($form->getElements() as $element) {
      $this->assertFalse($element->getDecorator('Errors'));

      $decorator = $element->getDecorator('ViewHelper');
      $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);

      $decorator = $element->getDecorator('Description');
      $this->assertTrue($decorator instanceof Zend_Form_Decorator_Description);
      #$decorator = $element->getDecorator('DescriptionTooltip');
      #$this->assertTrue($decorator instanceof \Rexmac\Zyndax\Form\Decorator\DescriptionTooltip);
      #$options = $decorator->getOptions();
      #$this->assertEquals('description', $options['class']);

      $decorator = $element->getDecorator('Label');
      $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
      $options = $decorator->getOptions();
      $this->assertFalse($options['escape']);
      $this->assertEquals('<span class="required">&nbsp;*</span>', $options['requiredSuffix']);

      $decorator = $element->getDecorator('HtmlTag');
      $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
      $options = $decorator->getOptions();
      $this->assertEquals('li', $options['tag']);
    }
  }

  public function testConstructorSetsDecoratorsForMobileRequest() {
    $_SERVER['SERVER_NAME'] = 'mobile.'.$_SERVER['SERVER_NAME'];
    FrontController::getInstance()->setRequest(new HttpRequest());
    $form = new TestForm();

    $decorator = $form->getDecorator('FormElements');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_FormElements);

    $this->assertFalse($form->getDecorator('HtmlTag'));

    $decorator = $form->getDecorator('Form');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);

    $decorator = $form->getDecorator('FormErrors');
    $this->assertTrue($decorator instanceof FormErrors);
    $options = $decorator->getOptions();
    $this->assertEquals('prepend', $options['placement']);
    $this->assertEquals('</div>', $options['markupListEnd']);
    $this->assertEquals('</span>', $options['markupListItemEnd']);
    $this->assertEquals('<span>', $options['markupListItemStart']);
    $this->assertEquals('<div class="form-errors messages error"><span class="message-icon"></span>', $options['markupListStart']);

    foreach($form->getElements() as $element) {
      $this->assertFalse($element->getDecorator('Errors'));

      $decorator = $element->getDecorator('ViewHelper');
      $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);

      $decorator = $element->getDecorator('Label');
      $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
    }
  }
}

