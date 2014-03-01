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
 * Service layer to ease the use and management of AclPermission entities
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class AclPermissionService extends \Rexmac\Zyndax\Doctrine\Service {

  /**
   * Retrieve all permissions in an associative array of format [role_id][resource_id]
   *
   * @return array
   */
  public static function findPermissionsAsArray() {
    $results = array();
    $permissions = self::find();
    foreach($permissions as $permission) {
      $roleId = $permission->getRole()->getId();
      if(!array_key_exists($roleId, $results)) $results[$roleId] = array();
      $results[$roleId][$permission->getResource()->getId()] = $permission;
    }

    return $results;
  }
}
