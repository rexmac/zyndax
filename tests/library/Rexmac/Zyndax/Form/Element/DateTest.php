<?php

namespace Rexmac\Zyndax\Form\Element;

use Rexmac\Zyndax\Form\Element\Date,
    \Zend_Form_Decorator_ViewHelper,
    \Zend_Form_Element,
    \Zend_Form_Element_Xhtml;

class DateTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test Date element
   *
   * @var Rexmac\Zyndax\Form\Element\Date
   */
  private $element;

  public function setUp() {
    $this->element = new Date('foo');
  }

  public function testDateElementSubclassesXhtmlElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
  }

  public function testDateElementInstanceOfBaseElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element);
  }

  public function testDateElementUsesTextHelperInViewHelperDecoratorByDefault() {
    $decorator = $this->element->getDecorator('viewHelper');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    $decorator->setElement($this->element);
    $helper = $decorator->getHelper();
    $this->assertEquals('formText', $helper);
  }
}
