<?php

namespace Rexmac\Zyndax\Form;

class TestForm extends Form {

  /**
   * Initialization
   *
   * Initialize login form fields with validators and decorators.
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->setName('login_form');
    $this->setMethod('post');

    $this->addElement('text', 'foo', array());
    $this->addElement('text', 'bar', array());
    $this->addElement('text', 'baz', array());
  }
}
