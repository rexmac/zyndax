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
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Service;

/**
 * Service layer to ease the use and management of AclRole entities
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class AclRoleService extends \Rexmac\Zyndax\Doctrine\Service {

  /**
   * Return all ACL roles excluding the given roles
   *
   * @params array $exclude Array of role names to be excluded from results
   * @return array
   */
  public static function findAllExcluding(array $exclude) {
    $dql = sprintf(
      'SELECT e FROM %s e WHERE e.name NOT IN ("%s")',
      self::getEntityClass(),
      implode('","', $exclude)
    );
    $results = self::getEntityManager()->createQuery(sprintf(
      'SELECT e FROM %s e WHERE e.name NOT IN (\'%s\')',
      self::getEntityClass(),
      implode('\',\'', $exclude)
    ))->getResult();

    if(!is_array($results)) {
      return array();
    }

    $rolees = array();
    foreach($results as $result) {
      $roles[$result->getId()] = $result;
    }

    return $roles;
  }

  /**
   * Return all ACL roles exluding the guest role
   *
   * @return array
   */
  public static function findAllExcludingGuest() {
    $roles = self::find();
    $results = array();
    foreach($roles as $role) {
      if($role->getName() !== 'Guest') $results[] = $role;
    }
    return $results;
  }
}
