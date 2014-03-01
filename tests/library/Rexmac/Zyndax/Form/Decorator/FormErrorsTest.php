<?php

namespace Rexmac\Zyndax\Form\Decorator;

use \Zend_Form,
    \Zend_Session,
    \Zend_Session_Namespace,
    \Zend_View;

class FormErrorsTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    Zend_Session::$_unitTestEnabled = true;
    $this->decorator = new FormErrors();
  }

  private function getView() {
    return new Zend_View();
  }

  public function setupForm() {
    $form = new Zend_Form;
    $sessionName = 'Zend_Form_Element_Hash_salt_csrf';
    $session = new Zend_Session_Namespace($sessionName);
    $session->hash = md5(mt_rand());

    $form->addElement('hash', 'csrf', array());

    $form->isValid(array(
      'csrf' => 'foo',
    ));
    $form->setView($this->getView());
    $this->decorator->setElement($form);
    $this->form = $form;
    return $form;
  }

  public function testRenderWithMissingToken() {
    $form = new Zend_Form;
    $form->addElement('hash', 'csrf', array());

    $form->isValid(array(
      'csrf' => 'foo',
    ));
    $form->setView($this->getView());
    $this->decorator->setElement($form);
    $this->form = $form;

    $markup = $this->decorator->render('');
    $this->assertContains('<li>Form expired. Please try again.</li>', $markup);
  }

  public function testRenderWithInvalidToken() {
    $this->setupForm();
    $markup = $this->decorator->render('');
    $this->assertContains('<li>Form expired. Please try again.</li>', $markup);
  }

  public function testRenderWithMessages() {
    $form = new Zend_Form;
    $form->addElement('text', 'foo', array('required' => true));

    $form->isValid(array());
    $form->setView($this->getView());
    $this->decorator->setElement($form);
    $this->form = $form;

    $markup = $this->decorator->render('');
    $this->assertContains('<li>Value is required and can\'t be empty</li>', $markup);
  }

  public function testRenderWithSubForm() {
    $subForm = new Zend_Form;
    $subForm->addElement('text', 'foo', array('required' => true));

    $form = new Zend_Form;
    $form->addSubForm($subForm, 'test_subform');

    $form->isValid(array());
    $form->setView($this->getView());
    $this->decorator->setElement($form);
    $this->form = $form;

    $markup = $this->decorator->render('');
    $this->assertContains('<li>Value is required and can\'t be empty</li>', $markup);
  }
}
