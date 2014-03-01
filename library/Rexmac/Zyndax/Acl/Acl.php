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
 * @subpackage Acl
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Acl;

use Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Service\AclPermissionService,
    Rexmac\Zyndax\Service\AclResourceService,
    Rexmac\Zyndax\Service\AclRoleService,
    Rexmac\Zyndax\Service\UserService,
    \Zend_Acl_Resource,
    \Zend_Acl_Role,
    \Zend_Exception,
    \Zend_Registry;

/**
 * ACL class that pulls roles, resources, and permissions from a database.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Acl
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Acl extends \Zend_Acl {

  /**
   * Current session user
   *
   * @var Rexmac\Zyndax\Entity\User
   */
  private $_user = null;

  /**
   * Guest user role
   *
   * @var Rexmac\Zyndax\Entity\AclRole
   */
  private $_guestRole = null;

  /**
   * Constructs an Acl object
   *
   * @return void
   */
  public function __construct() {
    $this->_initRoles();
    $this->_initResources();
    $this->_initPermissions();
    $this->_initUser();
  }

  /**
   * Initializes roles
   *
   * @return void
   */
  private function _initRoles() {
    $this->_guestRole = AclRoleService::findOneByName('Guest');
    $this->addRole(new Zend_Acl_Role($this->_guestRole->getName()));
    $roles = AclRoleService::find();
    #unset($roles[$this->_guestRole->getId()]);
    foreach($roles as $role) {
      if($role->getId() === $this->_guestRole->getId()) continue;
      $this->addRole(new Zend_Acl_Role($role->getName()), $this->_guestRole->getName());
    }
  }

  /**
   * Initializes resources
   *
   * @return void
   */
  private function _initResources() {
    $resources = AclResourceService::find();
    foreach($resources as $resource) {
      if(!$this->has($resource->getIdentifier())) {
        $this->addResource(new Zend_Acl_Resource($resource->getIdentifier()));
      }
    }
    if(!$this->has('default')) {
      $this->addResource('default');
    }
  }

  /**
   * Initializes permissions
   *
   * @return void
   */
  private function _initPermissions() {
    $acl = AclPermissionService::find();
    #$this->allow($this->_guestRole->getName(), 'default', 'view');
    foreach($acl as $permission) {
      $this->allow(
        $permission->getRole()->getName(),
        $permission->getResource()->getIdentifier(),
        $permission->getName()
      );
    }
  }

  /**
   * Initializes user (after removing existing one).
   *
   * @param User $user
   * @return void
   */
  public function initUser(User $user = null) {
    if($this->_user !== null) {
      // Remove existing user and role
      $this->removeRole($this->_user->getUsername());
      $this->_user = null;
    }
    $this->_initUser($user);
  }

  /**
   * Initializes user
   *
   * @param User $user
   * @return void
   */
  private function _initUser(User $user = null) {
    if($user === null) {
      // Attempt to retrieve user from registry (may have been placed there by Auth controller plugin)
      try {
        $user = Zend_Registry::get('user');
      } catch(Zend_Exception $e) {
      }

      if(null !== $user) {
        $this->_user = $user;
      } else { // No user found so default to 'guest'
        $this->_user = new User(array(
          'id'       => 0,
          'role'     => $this->_guestRole,
          'username' => 'Anonymous'
        ));
      }
    } else {
      $this->_user = $user;
    }

    $this->addRole(new Zend_Acl_Role($this->_user->getUsername()), $this->_user->getRole()->getName());
  }

  /**
   * Returns TRUE if user is has the given permission related to the given resource
   *
   * @param string $resource Requested resource
   * @param string $permission Requested permission
   * @return bool TRUE if current user is allowed, FALSE if not.
   */
  public function isUserAllowed($resource = null, $permission = null) {
    $result = $this->has($resource) && $this->isAllowed($this->getUser()->getUsername(), $resource, $permission);
    if(false === $result) {
      if(preg_match('/^(mvc:[^:]+):/', $resource, $matches)) {
        $resource = $matches[1] . ':all';
        \Rexmac\Zyndax\Log\Logger::debug(__METHOD__.':: Testing resource: '. $resource);
        $result = $this->has($resource) && $this->isAllowed($this->getUser()->getUsername(), $resource, $permission);
      }
    }
    return $result;
  }

  /**
   * Returns the curernt user
   *
   * @return Rexmac\Zyndax\Entity\User The current user
   */
  public function getUser() {
    return $this->_user;
  }
}
