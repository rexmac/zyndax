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
 * @subpackage Test_PHPUnit
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Test\PHPUnit;

use Doctrine\ORM\Query\ResultSetMapping,
    Doctrine\ORM\Tools\SchemaTool,
    Zend_Application;

/**
 * Setup DB for testing Doctrine
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Test_PHPUnit
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 * @codeCoverageIgnore
 */
class DoctrineTestCase extends \PHPUnit_Framework_TestCase {

  /**
   * Doctrine entity manager
   *
   * @var Doctrine\ORM\EntityManager
   */
  protected static $entityManager = null;

  /**
   * Entity class metadata
   *
   * @var array
   */
  protected static $metadata = array();

  /**
   * Drop schema
   *
   * @param array $params
   * @return void
   */
  public static function dropSchema($params) {
    if(file_exists($params['path'])) unlink($params['path']);
  }

  /**
   * Return Doctrine ClassMetadata for entity
   *
   * @param string $class
   * @return Doctrine\ORM\Mapping\ClassMetadata
   */
  public static function getClassMetadata($class) {
    $metadata = self::$entityManager->getClassMetadata($class);
    self::$metadata[] = $metadata;
    return $metadata;
  }

  /**
   * Return Doctrine ClassMetadata for entities in the given path.
   *
   * @param string $path
   * @param string $namespace
   * @return Doctrine\ORM\Mapping\ClassMetadata
   */
  public static function getClassMetas($path, $namespace) {
    $metadata = array();
    if($handle = opendir($path)) {
      while(false !== ($file = readdir($handle))) {
        if(preg_match('/\.php$/', $file)) {
          list($class) = explode('.', $file);
          $metadata[] = self::$entityManager->getClassMetadata($namespace.$class);
        }
      }
    }
    self::$metadata = array_merge(self::$metadata, $metadata);
    return $metadata;
  }

  /**
   * Set up DB for use with Doctrine tests
   *
   * @return void
   */
  public function setUp() {
    // Truncate all tables
    $tables = array();
    #$metadata = self::$entityManager->getMetadataFactory()->getAllMetadata();
    foreach(self::$metadata as $metadatum) {
      $tables[] = $metadatum->getTableName();
    }

    foreach($tables as $table) {
      #self::$entityManager->createNativeQuery('DELETE FROM ' . $table, new ResultSetMapping())->getResult();
      self::$entityManager->getConnection()->executeUpdate('DELETE FROM ' . $table);
      #self::$entityManager->createNativeQuery('UPDATE sqlite_sequence SET seq=0 WHERE name="'.$table.'"', new ResultSetMapping())->getResult();
      #self::$entityManager->createNativeQuery('DELETE FROM sqlite_sequence WHERE name=\''.$table.'\'', new ResultSetMapping())->getResult();
    }
    self::$entityManager->clear();
  }

  /**
   * Setup before class method
   *
   * @return void
   */
  public static function setUpBeforeClass() {
    $app = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
    $app->bootstrap('doctrine');
    self::$entityManager = $app->getBootstrap()->getResource('doctrine');
    self::$metadata = array();

    // Drop existing test schema
    self::dropSchema(self::$entityManager->getConnection()->getParams());

    // Create test schema
    $tool = new SchemaTool(self::$entityManager);
    $tool->createSchema(self::getClassMetas(APPLICATION_PATH . '/../tests/library/Rexmac/Zyndax/Doctrine/Entity', 'Rexmac\Zyndax\Doctrine\Entity\\'));
  }
}
