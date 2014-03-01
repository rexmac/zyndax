<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * This source file is subject to the Modified BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL:
 * http://rexmac.com/license/bsd2c.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to
 * license@rexmac.com so that we can send you a copy.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Response
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Controller\Response;

/**
 * HTTP response object for use with Zend_Controller family.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Controller_Response
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class HttpResponse extends \Zend_Controller_Response_Http {
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
