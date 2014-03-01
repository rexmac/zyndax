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
 * @subpackage Form
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Form;

/**
 * Custom Zend_Form class that always includes a CSRF hash field
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
abstract class SecureForm extends Form {

  /**
   * Static hash element allows for reuse by multiple forms on a single page
   *
   * @var string
   */
  private static $_csrf = null;

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    // If static hash element has not been created yet, do so now
    if(null === self::$_csrf) {
      $this->_createCsrfElement();
    }
    parent::__construct();
  }

  /**
   * Create a static CSRF element
   *
   * @return void
   */
  private function _createCsrfElement() {
    self::$_csrf = $this->createElement('hash', 'csrf', array(
      #'salt'   => 's3cr3t',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
  }

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    if('testing' === APPLICATION_ENV) {
      $this->_createCsrfElement();
      #self::$_csrf->initCsrfToken();
      #self::$_csrf->initCsrfValidator();
    }
    $this->addElement(self::$_csrf);
  }
}
