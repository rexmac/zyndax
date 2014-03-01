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
 * @subpackage Doctrine
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */

namespace Rexmac\Zyndax\Doctrine;

use Doctrine\ORM\PersistentCollection;

/**
 * Abastract class representing an entity manager by Doctrine ORM
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Doctrine
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
abstract class Entity {

  /**
   * Constructs entity
   *
   * @param  array $data Associative array of entity's properties and their values
   * @return void
   */
  public function __construct(array $data = null) {
    $this->populate($data);
  }

  /**
   * Populates the entity's properties with the provided values
   *
   * @param  array $data Associative array of entity's properties and their values
   * @return Rexmac\Zyndax\Doctrine\Entity
   */
  public function populate(array $data = null) {
    if($data === null) return;
    foreach($data as $k=>$v) {
      $this->{$k} = $v;
    }
    return $this;
  }

  /**
   * Returns associative array representation fo entity
   *
   * @return array Associative array of entity's properties and their values
   */
  public function toArray() {
    $vars = get_object_vars($this);
    foreach($vars as $k => $v) {
      if($v instanceof PersistentCollection) {
        $vars[$k] = $v->unwrap();
      }
    }
    return $vars;
  }

  /**
   * Magic function to provide mapping of non-defined getter/setter methods
   * for entity's properties.
   *
   * @param  string $name Name of non-exitent method
   * @param  array  $args Array of arguments to be passed to the method
   * @return mixed Value of property for getters, Entity for setters (fluent
   *               interface), NULL for everything else
   */
  public function __call($name, $args) {
    if(strpos($name, 'get') === 0) {
      return $this->{lcfirst(substr($name, 3))};
    } elseif(strpos($name, 'set') === 0) {
      $this->{lcfirst(substr($name, 3))} = $args[0];
      return $this;
    }
  }
}
