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
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\View\Helper;

/**
 * Helper class for displaying pagination links
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class PaginationLinks extends \Zend_View_Helper_Abstract {

  /**
   * Helper method to display pagination links
   *
   * @return string
   */
  public function paginationLinks() {
    return call_user_func_array(array($this, 'direct'), func_get_args());
  }

  /**
   * Helper method to display pagination links
   *
   * @return string
   */
  public function direct() {
    list($paginator, $sortField, $sortOrder) = func_get_args();
    $currentPage  = $paginator->getCurrentPageNumber();
    $itemsPerPage = $paginator->getItemCountPerPage();
    $pages        = $paginator->getPages('Sliding');
    $pageLinks    = array();
    #$separator    = ' | ';

    #$pageLinks[] = getLink($pages->first, $itemsPerPage, $sortField, $sortOrder, '<<');
    if($pages->pageCount > 1) {
      if(isset($pages->previous)) {
        $pageLinks[] = $this->_getLink($pages->previous, $itemsPerPage, $sortField, $sortOrder, '«');
      }
      foreach($pages->pagesInRange as $x) {
        if($x == $pages->current) {
          $pageLinks[] = $x;
        } else {
          $pageLinks[] = $this->_getLink($x, $itemsPerPage, $sortField, $sortOrder, $x);
        }
      }
      if($pages->lastPageInRange > $pages->pageCount) {
        array_pop($pageLinks);
        $pageLinks[] = '&hellip;';
        $pageLinks[] = ($pages->last === $pages->current ? $pages->last : $this->_getLink($pages->last, $itemsPerPage, $sortField, $sortOrder, $pages->last));
      }
      if(isset($pages->next)) {
        $pageLinks[] = $this->_getLink($pages->next, $itemsPerPage, $sortField, $sortOrder, '»');
      }
    }

    $minItem = $pages->pageCount == 0 ? 0 : 1 + $itemsPerPage * ($currentPage - 1);
    $total = $paginator->getAdapter()->count();
    $maxItem = $itemsPerPage * $currentPage;
    $maxItem = ($maxItem > $total ? $total : $maxItem);
    $pageLinks[] = " Currently showing $minItem-$maxItem of $total.";

    return implode(' ', $pageLinks);
  }

  /**
   * Returns HTML link to the given page
   *
   * @param  stdClass Page to be linked to
   * @param  integer  Number of items per page
   * @param  string   Sort field
   * @param  string   Sort order
   * @param  string   Label
   * @return string   HTML link to the given page
   */
  private function _getLink($page, $itemsPerPage, $sortField, $sortOrder, $label) {
    $query = http_build_query(array(
      'sort'  => $sortField.('desc' == $sortOrder ? ':desc' : ''),
      'page'  => $page,
      'count' => $itemsPerPage
    ));
    return "<a href=\"?$query\">".$this->view->escape($label)."</a>";
  }
}
