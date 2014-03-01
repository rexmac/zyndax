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

use Rexmac\Zyndax\Doctrine\Service as DoctrineService;

/**
 * Adapter for Zend_Paginator that uses Doctrine ORM to pull items
 * from a database.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Paginator_Adapter
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class DoctrineAdapter implements \Zend_Paginator_Adapter_Interface {

  /**
   * Name of Doctrine entity to be paginated
   *
   * @var string
   */
  private $entityClass = null;

  /**
   * Doctrine ORM QueryBuilder
   *
   * @var Doctrine\ORM\QueryBuilder
   */
  private $query = null;

  /**
   * Name of Doctrine entity property to sort results by
   *
   * @var string
   */
  private $sortField = null;

  /**
   * Sort order for results
   *
   * @var string
   */
  private $sortOrder = null;

  /**
   * Where clause
   *
   * @var string
   */
  private $where = null;

  /**
   * Constructor
   *
   * @param string $entityClass Name of Doctrine entity to be paginated
   * @param string $sortField Name of Doctrine entity's property to sort results by
   * @param string [Optional] $sortOrder Sort order (i.e. 'ASC' or 'DESC'). Defaults to 'ASC'.
   * @param string [Optional] $where Where clause
   * @return void
   */
  public function __construct($entityClass, $sortField = null, $sortOrder = 'ASC', $where = null) {
    $this->entityClass = $entityClass;
    $this->sortField = $sortField;
    $this->sortOrder = $sortOrder;
    $this->where = $where;
    $queryBuilder = DoctrineService::getEntityManager()->createQueryBuilder();
    $queryBuilder->select('e')->from($this->entityClass, 'e');
    if(null !== $this->sortField) {
      $part = explode('.', $this->sortField);
      if(isset($part[1])) {
        $foreignColName     = $part[1];
        $foreignEntityName  = lcfirst(array_pop(explode('\\', $part[0])));
        $queryBuilder->leftJoin('e.'.$foreignEntityName, 'j')
                     ->orderBy('j.'.$foreignColName, $this->sortOrder);
      } else {
        $queryBuilder->orderBy('e.'.$this->sortField, $this->sortOrder);
      }
    }
    if(null !== $this->where && '' !== $this->where) {
      $queryBuilder->where($this->where);
    }
    $this->query = $queryBuilder;
    $this->countQuery = clone $this->query;
  }

  /**
   * Returns items matching the given criteria
   *
   * @param  int $offset Page offset
   * @param  int $itemCountPerPage Number of items per page
   * @return array
   */
  public function getItems($offset, $itemCountPerPage) {
    if($offset > 0) {
      $this->query->setFirstResult($offset);
    }
    $this->query->setMaxResults($itemCountPerPage);
    return $this->query->select('e')->getQuery()->getResult();
  }

  /**
   * Returns the total number of rows in the result set.
   *
   * @return int Number of rows in the result set
   */
  public function count() {
    $count = $this->countQuery->select('COUNT(e)')->getQuery()->getSingleScalarResult();
    return $count;
  }
}
