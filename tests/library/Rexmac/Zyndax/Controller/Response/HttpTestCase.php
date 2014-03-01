<?php

namespace Rexmac\Zyndax\Controller\Response;

class HttpTestCase extends \Zend_Controller_Response_HttpTestCase {
  /**
   * Get response header
   *
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  public function getHeader($name, $default = null) {
    foreach($this->_headers as $header) {
      if($header['name'] === $name) {
        return $header;
      }
    }
    return $default;
  }
}
