<?php

namespace Rexmac\Zyndax\Form\Element;

use Rexmac\Zyndax\Form\Element\ButtonMenu,
    \Zend_Form_Decorator_ViewHelper,
    \Zend_Form_Element,
    \Zend_Form_Element_Submit,
    \Zend_Form_Element_Xhtml,
    \Zend_Translate,
    \Zend_View;

class ButtonMenuTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test ButtonMenu element
   *
   * @var Rexmac\Zyndax\Form\Element\ButtonMenu
   */
  private $element;

  public function setUp() {
    $this->element = new ButtonMenu('foo');
  }

  public function getView() {
    $view = new Zend_View();
    $view->addHelperPath(dirname(__FILE__) . '/../../../../../../library/Rexmac/Zyndax/View/Helper', 'Rexmac\Zyndax\View\Helper\\');
    return $view;
  }

  public function testButtonMenuElementSubclassesSubmitElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Submit);
  }

  public function testButtonMenuElementSubclassesXhtmlElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
  }

  public function testButtonMenuElementInstanceOfBaseElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element);
  }

  public function testHelperAttributeSetToFormButtonMenuByDefault() {
    $this->assertEquals('formButtonMenu', $this->element->getAttrib('helper'));
  }

  public function testButtonMenuElementUsesButtonMenuHelperInViewHelperDecoratorByDefault() {
    $decorator = $this->element->getDecorator('viewHelper');
    $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    $decorator->setElement($this->element);
    $helper = $decorator->getHelper();
    $this->assertEquals('formButtonMenu', $helper);
  }

  public function testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered() {
    $translations = include dirname(__FILE__) . '/../_files/locale/array.php';
    $translate = new Zend_Translate('array', $translations, 'en');
    $this->element->setTranslator($translate)
                  ->setLabel('submit');
    $test = $this->element->getLabel();
    $this->assertEquals($translations['submit'], $test);
  }

  public function testTranslatedLabelIsRendered() {
    $this->testGetLabelReturnsTranslatedLabelIfTranslatorIsRegistered();
    $this->element->setView($this->getView());
    $decorator = $this->element->getDecorator('ViewHelper');
    $decorator->setElement($this->element);
    $html = $decorator->render('');
    $this->assertRegexp('/<button[^>]*?>Submit Button/', $html, $html);
  }

  public function testValuePropertyShouldNotBeRendered() {
    $this->element->setLabel('Button Label')
                  ->setView($this->getView());
    $html = $this->element->render();
    $this->assertContains('Button Label', $html, $html);
    $this->assertNotContains('value="', $html);
  }

  public function testSetDefaultIgnoredToTrueWhenNotDefined() {
    $this->assertTrue($this->element->getIgnore());
  }
}
