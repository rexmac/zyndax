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

use Doctrine\ORM\EntityManager,
    \InvalidArgumentException,
    Rexmac\Zyndax\Doctrine\Entity;

/**
 * Service layer to ease the use and management of Doctrine entities.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Doctrine
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
abstract class Service {
  /**
   * Doctrine entity manager
   *
   * @var EntityManager
   */
  private static $_entityManager = null;

  /**
   * Setter for entityManager
   *
   * @param EntityManager $entityManager
   * @return void
   */
  public static function setEntityManager(EntityManager $entityManager) {
    self::$_entityManager = $entityManager;
  }

  /**
   * Getter for entityManager
   *
   * @return EntityManager
   */
  public static function getEntityManager() {
    #if(null === self::$_entityManager) {
    #  self::$_entityManager = \Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('entityManager');
    #}

    // Check if entitymanager has been closed (most likely due to a previously thrown PDOException)
    if(!self::$_entityManager->isOpen()) {
      // Entitymanger has closed, create a new one
      $connection = self::$_entityManager->getConnection();
      $config     = self::$_entityManager->getConfiguration();
      self::$_entityManager = EntityManager::create($connection, $config);
    }

    return self::$_entityManager;
  }

  /**
   * Return name of entity's class
   *
   * @return string
   */
  public static function getEntityClass() {
    return preg_replace('/^((?:[a-z]+\\\)+)Service\\\([a-z]+)Service$/i', '$1Entity\\\$2', get_called_class());
  }

  /**
   * Create new entity
   *
   * @param array|Entity $data Entity or associative array of entity properties and values
   * @param bool $flush Whether or not to immediately flush after persisting. Default is TRUE.
   * @return Entity
   * @throws InvalidArgumentException If passed an Entity not handled by this service.
   */
  public static function create($data, $flush = true) {
    $class = self::getEntityClass();

    if(is_array($data)) {
      $data = new $class($data);
    }

    if(!($data instanceof $class)) {
      throw new InvalidArgumentException('Entity must be an instance of ' . $class);
    }

    self::getEntityManager()->persist($data);
    if($flush) self::getEntityManager()->flush();
    return $data;
  }

  /**
   * Search for an entity and update if found. If not found, attempt to create.
   *
   * @param array|Entity $data Entity or associative array of entity properties and values
   * @param array $id Array of field-value pairs to be used to locate an existing entity
   * @return Entity
   */
  public static function createOrUpdate($data, $id = array()) {
    $class = self::getEntityClass();
    if(is_array($data)) {
      if(is_array($id) && !empty($id)) $findByData = $id;
      else $findByData = $data;
      foreach($findByData as $k => $v) {
        if($v instanceof Entity) $findByData[$k] = $v->getId();
      }
      $entity = self::findOneBy($findByData);
      if($entity instanceof $class) {
        foreach($data as $k => $v) {
          $entity->{'set'.ucfirst($k)}($v);
        }
        return $entity;
      } else return self::create($data);
    } else if($data instanceof $class) {
    }
  }

  /**
   * Return one or more entities. Returns NULL if none found.
   *
   * @return mixed Array of entities or NULL if none found
   */
  public static function find() {
    $class = self::getEntityClass();
    $args = func_get_args();
    if(isset($args[0])) {
      return self::getEntityManager()->find($class, $args[0]);
    } else {
      return self::getEntityManager()->getRepository($class)->findAll();
    }
  }

  /**
   * Return one or more entities indexed by their IDs.
   *
   * @return array Array of entities. May be empty.
   */
  public static function findIndexedById() {
    $class = self::getEntityClass();
    $args = func_get_args();
    if(isset($args[0])) {
      $results = self::getEntityManager()->find($class, $args[0]);
    } else {
      $results = self::getEntityManager()->getRepository($class)->findAll();
    }

    if(!is_array($results)) {
      if($results instanceof Entity) return array($results->getId() => $results);
      else return array();
    }

    $entities = array();
    foreach($results as $result) {
      $entities[$result->getId()] = $result;
    }
    return $entities;
  }

  /**
   * Return one entity. Returns NULL if none found.
   *
   * @return mixed Entity or NULL if none found
   */
  public static function findOne() {
    #return self::getEntityManager()->createQuery('SELECT e FROM ' . self::getEntityClass() . ' e LIMIT 1')->getResult();
    $result = self::getEntityManager()->createQuery('SELECT e FROM ' . self::getEntityClass() . ' e')->setMaxResults(1)->getResult();
    return $result[0];
  }

  /**
   * Search for an entity. If not found, attempt to create
   *
   * @param array $data Associative array of entity properties and their values
   * @return Entity
   */
  public static function findOrCreate(array $data) {
    $class = self::getEntityClass();
    if(is_array($data)) {
      $findByData = $data;
      foreach($findByData as $k => $v) {
        if($v instanceof Entity) $findByData[$k] = $v->getId();
      }
      $entity = self::findBy($findByData);
      if($entity[0] instanceof $class) return $entity[0];
      else return self::create($data);
    } else if($data instanceof $class) {
    }
  }

  /**
   * Delete the given entity
   *
   * @param Entity $entity
   * @return void
   */
  public static function delete(Entity $entity) {
    self::getEntityManager()->remove($entity);
    self::getEntityManager()->flush();
  }

  /**
   * Update all modified entities
   *
   * @return void
   */
  public static function update() {
    self::getEntityManager()->flush();
  }

  /**
   * Search for entity by id
   *
   * @param string $id ID of entity
   * @return mixed Entity or NULL if not found
   */
  public static function findById($id) {
    return self::find($id);
  }

  /**
   * Special method to map findBy and findOneBy commands
   *
   * @param string $name Method name
   * @param array $args Method parameters
   * @return mixed Array of entities or NULL if none found
   * @throws InvalidArgumentException If command is not found
   */
  public static function __callstatic($name, $args) {
    if(preg_match('/^find(?:One)?By/', $name)) {
      if(isset($args[0])) {
        return self::getEntityManager()->getRepository(self::getEntityClass())->$name($args[0]);
      } else {
        return null;
      }
    }
    throw new InvalidArgumentException("Unknown method: '{$name}'");
  }

  /**
   * Get all entities sorted by name
   *
   * @return array Results of query
   */
  public static function getAllSortedByName() {
    $dql = 'SELECT e FROM ' . self::getEntityClass() . ' e ORDER BY e.name';
    $query = self::getEntityManager()->createQuery($dql);
    return $query->getResult();
  }

  /**
   * Return associative array of options for use with Zend_Form_Element_Multiselect
   *
   * @param bool $prompt Whether or not to include a "---Select---" option to prompt user to make a selection
   * @param bool $all Whether or not to include an "All" option
   * @param array $entities [Optional] Array of entities. Default is to return all entities.
   * @param array $options [Optional] Array of options.
   * @param bool $showIds Whether or not to display entity IDs beside names
   * @return array Options array for use with Zend_Form_Element_Multiselect
   */
  public static function getOptionsForSelect($prompt = false, $all = false, $entities = null, $options = array(), $showIds = false) {
    $entities = $entities === null ? self::find() : $entities;
    foreach($entities as $entity) {
      $options[$entity->getId()] = htmlspecialchars(ucfirst($entity->getName()) . ($showIds ? ' ('.$entity->getId().')' : ''));
      #$options[$entity->getId()] = htmlspecialchars(ucfirst($entity->getName()) . ($showIds ? ' <span class="entity-id">('.$entity->getId().')</span>' : ''));
    }
    if($prompt) $options[''] = '---Select---';
    if($all) $options[0] = 'All';
    uasort($options, function($a,$b) {
      if($a === '---Select---') return -1;
      if($b === '---Select---') return 1;
      if($a === 'All') return -1;
      if($b === 'All') return 1;
      if($a === $b) return 0;
      return ($a < $b) ? -1 : 1;
    });
    return $options;
  }

  /**
   * Returns total number of persisted entities.
   *
   * @param string $where [Optional] Doctrine QueryBuilder WHERE clause
   * @return int Number of persisted entities
   */
  public static function getTotal($where = null) {
    $queryBuilder = self::getEntityManager()->createQueryBuilder();
    $queryBuilder
      ->select('COUNT(e)')
      ->from(self::getEntityClass(), 'e');

    if(null !== $where) $queryBuilder->where($where);

    return $queryBuilder->getQuery()->getSingleScalarResult();
  }
}
