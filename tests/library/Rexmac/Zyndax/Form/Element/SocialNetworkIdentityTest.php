<?php

namespace Rexmac\Zyndax\Form\Element;

use Rexmac\Zyndax\Form\Element\SocialNetworkIdentity as SocialNetworkIdentityElement,
    \Zend_Form_Element,
    \Zend_Form_Element_Xhtml,
    \Zend_View;

class SocialNetworkIdentityTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test SocialNetworkIdentity element
   *
   * @var Rexmac\Zyndax\Form\Element\SocialNetworkIdentity
   */
  private $element;

  public function setUp() {
    $this->element = new SocialNetworkIdentityElement('foo');
  }

  public function getView() {
    $view = new Zend_View();
    #$view->addHelperPath(dirname(__FILE__) . '/../../../../../../library/Rexmac/Zyndax/View/Helper', 'Rexmac\Zyndax\View\Helper\\');
    return $view;
  }

  public function testSocialNetworkIdentityElementSubclassesXhtmlElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
  }

  public function testSocialNetworkIdentityElementInstanceOfBaseElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element);
  }

  public function testGettingValue() {
    $this->assertEquals($this->element, $this->element->getValue());
  }

  public function testIsEmpty() {
    $this->assertTrue($this->element->isEmpty());

    $this->element->setValue(array(
      'name' => '',
      'network' => 0
    ));
    $this->assertTrue($this->element->isEmpty());

    $this->element->setValue(array(
      'name' => 'myident',
      'network' => 1
    ));
    $this->assertFalse($this->element->isEmpty());
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Invalid value provided
   */
  public function testSettingValueWithInvalidValue() {
    $this->element->setValue('foo');
  }

  public function testSettingValueWithInvalidValueWithNullResultsInEmptyValue() {
    $this->element->setValue(null);
    $this->assertTrue($this->element->isEmpty());
  }

  public function testSettingValue() {
    $identityName = 'myident';
    $network = 1;
    $returned = $this->element->setValue(array(
      'name' => $identityName,
      'network' => $network
    ));
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($identityName, $this->element->getIdentityName());
    $this->assertEquals($network, $this->element->getNetwork());
  }
}
