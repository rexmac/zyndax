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
 * @subpackage View_Helper_Jquery
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\View\Helper\Jquery;

/**
 * Helper class to create sortable table headers
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper_Jquery
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class TableHeader extends \Rexmac\Zyndax\View\Helper\Jquery\HelperAbstract {

  /**
   * Table header's HTML ID
   *
   * @var string
   */
  private $_id;

  /**
   * Table header's label
   *
   * @var string
   */
  private $_label;

  /**
   * Table header's CSS class
   *
   * @var string
   */
  private $_thClass;

  /**
   * Table header's anchor link CSS class
   *
   * @var string
   */
  private $_aClass;

  /**
   * View helper entry point: Retrieves helper
   *
   * -param  string Table header's ID
   * -param  string [optional] Table header's label
   * -param  string [optional] Table header's CSS class
   * @return Rexmac\Zyndax\View\Helper\Jquery\TableHeader Provides fluent interface
   */
  public function direct() {
    $args = func_get_args();
    $this->_thClass = 'sortable' . (isset($args[2]) ? ' ' . $args[2] : '');

    $this->_id = $args[0];
    $this->_label = isset($args[1]) ? $args[1] : ucfirst($args[0]);

    $isSorted = (isset($this->view->sortField) && $this->_id === $this->view->sortField);
    $this->_aClass = ($isSorted ? ' class="ss_right ss_sprite ss_bullet_arrow_' . ('asc' === $this->view->sortOrder ? 'down' : 'up') . '"' : '');

    return $this;
  }

  /**
   * Renders helper
   *
   * Implements {@link \Rexmac\Zyndax\View\Helper\Jquery\Helper::render()}.
   *
   * @return string helper output
   */
  public function render() {
    return "<th class=\"$this->_thClass\" title=\"Sort by $this->_label\" sortfield=\"$this->_id\">"
         . "<a$this->_aClass href=\"javascript:;\">$this->_label</a></th>";
  }
}
