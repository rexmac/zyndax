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
 * @subpackage Filter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace {
  if(!function_exists('mb_ucfirst')) {
    /**
     * Multi-byte version of ucfirst function
     *
     * @param string $str
     * @param string $encoding
     * @return string
     */
    function mb_ucfirst($str, $encoding = 'UTF-8') {
      return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding)
        . mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
  }
}

namespace Rexmac\Zyndax\Filter {

use \Zend_Config,
    \Zend_Filter_Exception;

/**
 * Zend filter for making the first letter of a string upper cased
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Filter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UcFirst implements \Zend_Filter_Interface {
  /**
   * Encoding for the input string
   *
   * @var string
   */
  protected $_encoding = null;

  /**
   * Constructor
   *
   * @param string|array $options [Optional]
   * @return void
   */
  public function __construct($options = null) {
    if($options instanceof Zend_Config) {
      $options = $options->toArray();
    } else if(!is_array($options)) {
      $options = func_get_args();
      $temp    = array();
      if(!empty($options)) {
        $temp['encoding'] = array_shift($options);
      }
      $options = $temp;
    }

    if(!array_key_exists('encoding', $options) && function_exists('mb_internal_encoding')) {
      $options['encoding'] = mb_internal_encoding();
    }

    if(array_key_exists('encoding', $options)) {
      $this->setEncoding($options['encoding']);
    }
  }

  /**
   * Returns the set encoding
   *
   * @return string
   */
  public function getEncoding() {
    return $this->_encoding;
  }

  /**
   * Set the input encoding for the given string
   *
   * @param  string $encoding
   * @return Rexmac\Zyndax\Filter\UcFirst Provides a fluent interface
   * @throws Zend_Filter_Exception
   */
  public function setEncoding($encoding = null) {
    if($encoding !== null) {
      if(!function_exists('mb_ucfirst')) {
        throw new Zend_Filter_Exception('mbstring is required for this feature');
      }

      $encoding = (string) $encoding;
      if(!in_array(strtolower($encoding), array_map('strtolower', mb_list_encodings()))) {
        throw new Zend_Filter_Exception("The given encoding '$encoding' is not supported by mbstring");
      }
    }

    $this->_encoding = $encoding;
    return $this;
  }

  /**
   * Defined by Zend_Filter_Interface
   *
   * Returns the string $value, converting characters to uppercase as necessary
   *
   * @param  string $value
   * @return string
   */
  public function filter($value) {
    if($this->_encoding) {
       return mb_ucfirst((string) $value, $this->_encoding);
    }

    return ucfirst((string) $value);
  }
}

} // close namespace Rexmac\Zyndax\Filter
