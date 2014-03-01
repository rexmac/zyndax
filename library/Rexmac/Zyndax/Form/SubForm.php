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
 * Custom Zend_Form_SubForm class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class SubForm extends Form {
  /**
   * Whether or not form elements are members of an array
   * @var bool
   */
  protected $_isArray = true;

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();

    $this->setDecorators(array(
      'FormElements'
    ));
  }

  /**
   * Load the default decorators
   *
   * @return Rexmac\Zyndax\Form\SubForm Provides a fluent interface
   */
  public function loadDefaultDecorators() {
    return $this;
  }
}
