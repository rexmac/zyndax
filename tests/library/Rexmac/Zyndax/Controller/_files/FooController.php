<?php

class FooController extends Zend_Controller_Action {

  /**
   * Test function for indexAction
   *
   * @return void
   */
  public function indexAction() {
    $this->_response->appendBody("Index action called\n");
  }

  /**
   * Test function for homeAction
   *
   * @return void
   */
  public function homeAction() {
    $this->_response->appendBody("Home action called\n");
  }
}
