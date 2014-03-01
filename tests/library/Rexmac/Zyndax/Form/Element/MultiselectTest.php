<?php

namespace Rexmac\Zyndax\Form\Element;

use Rexmac\Zyndax\Form\Element\Multiselect,
    \Zend_Form_Decorator_ViewHelper,
    \Zend_Form_Element,
    \Zend_Form_Element_Multi,
    \Zend_Form_Element_Xhtml,
    \Zend_View;

class MultiselectTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test Multiselect element
   *
   * @var Rexmac\Zyndax\Form\Element\Multiselect
   */
  private $element;

  public function setUp() {
    $this->element = new Multiselect('foo');
  }

  public function getView() {
    $view = new Zend_View();
    $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper/');
    return $view;
  }

  public function testMultiselectElementInstanceOfMultiElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
  }

  public function testMultiselectElementInstanceOfXhtmlElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
  }

  public function testMultiselectElementInstanceOfBaseElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element);
  }

  public function testMultiselectElementIsAnArrayByDefault() {
    $this->assertTrue($this->element->isArray());
  }

  public function testMultiselectElementUsesSelectHelperInViewHelperDecoratorByDefault() {
    $decorator = $this->element->getDecorator('viewHelper');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    $decorator->setElement($this->element);
    $helper = $decorator->getHelper();
    $this->assertEquals('formSelect', $helper);
  }
}
