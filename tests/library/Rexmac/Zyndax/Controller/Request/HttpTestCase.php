<?php

namespace Rexmac\Zyndax\Controller\Request;

class HttpTestCase extends \Zend_Controller_Request_HttpTestCase {

  /**
   * Is the request from a mobile browser?
   *
   * @return boolean
   */
  public function isMobileRequest() {
    return !!preg_match('/^mobile\./', $this->getServer('SERVER_NAME'));
  }
}
