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
 * @subpackage Application_Resource
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Application\Resource;

use Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Annotations\CachedReader,
    Doctrine\Common\Annotations\IndexedReader,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Types\Type,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping\Driver\AnnotationDriver,
    Rexmac\Zyndax\Doctrine\Log\SqlLogger,
    Rexmac\Zyndax\Doctrine\Service,
    \Zend_Registry;

/**
 * Zend application resource for configuring and creating a Doctrine
 * entity manager.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Resource
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Doctrine extends \Zend_Application_Resource_ResourceAbstract {

  /**
   * Associative array of config options
   *
   * @var array
   */
  protected $_options = array(
    'connection'     => array(
      'driver' => 'pdo_sqlite',
      'host'   => 'localhost',
      'dbname' => 'db',
    ),
    'logPath'        => null,
    'modelDirectory' => '/models',
    'proxyDirectory' => '/proxies',
    'proxyNamespace' => 'Proxy',
    'autoGenerateProxyClasses' => true
  );

  /**
   * Initializes resource
   *
   * @return Doctrine\ORM\EntityManager
   */
  public function init() {
    $options = $this->getOptions();
    $config = new Configuration();

    if(APPLICATION_ENV == 'production' && function_exists('apc_fetch')) { // @codeCoverageIgnoreStart
      #extension_loaded() memcache, xcache, redis
      $cache = new ApcCache();
    } else {  // @codeCoverageIgnoreEnd
      $cache = new ArrayCache();
    }

    #$driverImpl = $config->newDefaultAnnotationDriver($options['modelDirectory']);
    // @todo Temporary(?) fix for using new AnnotationReader
    $reader = new AnnotationReader();
    #$reader->setEnableParsePhpImports(true);
    $reader = new IndexedReader($reader);
    $reader = new CachedReader($reader, $cache);
    $driverImpl = new AnnotationDriver($reader, $options['modelDirectory']);
    class_exists('Doctrine\ORM\Mapping\Driver\DoctrineAnnotations');

    // Beware cache slams: http://doctrine-orm.readthedocs.org/en/2.0.x/reference/caching.html#cache-slams
    $config->setMetadataCacheImpl($cache);
    $config->setQueryCacheImpl($cache);
    $config->setResultCacheImpl($cache);
    $config->setProxyDir($options['proxyDirectory']);
    $config->setProxyNamespace($options['proxyNamespace']);
    $config->setAutoGenerateProxyClasses($options['autoGenerateProxyClasses']);
    $config->setMetadataDriverImpl($driverImpl);

    // @codeCoverageIgnoreStart
    if(null !== $options['logPath']) {
      $sqlLogger = new SqlLogger($options['logPath']);
      $config->setSQLLogger($sqlLogger);
      Zend_Registry::set('sqlLogger', $sqlLogger);
    }
    // @codeCoverageIgnoreEnd

    $entityManager = EntityManager::create($options['connection'], $config);
    Service::setEntityManager($entityManager);

    // Add BLOB data type mapping
    if(!Type::hasType('gzblob')) {
      Type::addType('gzblob', 'Rexmac\Zyndax\Doctrine\DBAL\Type\GzBlob');
      $entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('BLOB', 'gzblob');
    }
    // Add IP data type mapping
    if(!Type::hasType('ip')) {
      Type::addType('ip', 'Rexmac\Zyndax\Doctrine\DBAL\Type\Ip');
      $entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('IP', 'ip');
    }

    return $entityManager;
  }
}
