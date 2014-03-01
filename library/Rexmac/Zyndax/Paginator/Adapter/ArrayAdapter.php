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
 * @subpackage Paginator_Adapter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Paginator\Adapter;

/**
 * Same as Zend's Array adapter except that keys are preserved.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Paginator_Adapter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class ArrayAdapter implements \Zend_Paginator_Adapter_Interface {
  /**
   * Array
   *
   * @var array
   */
  protected $_array = null;

  /**
   * Item count
   *
   * @var integer
   */
  protected $_count = null;

  /**
   * Constructor.
   *
   * @param array $array Array to paginate
   */
  public function __construct(array $array) {
    $this->_array = $array;
    $this->_count = count($array);
  }

  /**
   * Returns an array of items for a page.
   *
   * @param  integer $offset Page offset
   * @param  integer $itemCountPerPage Number of items per page
   * @return array
   */
  public function getItems($offset, $itemCountPerPage) {
    return array_slice($this->_array, $offset, $itemCountPerPage, true);
  }

  /**
   * Returns the total number of rows in the array.
   *
   * @return integer
   */
  public function count() {
    return $this->_count;
  }
}
