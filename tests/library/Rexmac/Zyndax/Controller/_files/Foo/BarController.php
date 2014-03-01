<?php

class Foo_BarController extends Zend_Controller_Action {

  /**
   * Test Function for bazAction
   *
   * @return void
   */
  public function bazAction() {
    $this->_response->appendBody("Baz action called\n");
  }
}
