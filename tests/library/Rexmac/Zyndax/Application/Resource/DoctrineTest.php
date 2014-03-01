<?php

namespace Rexmac\Zyndax\Application\Resource;

use Doctrine\ORM\EntityManager,
    \Zend_Application,
    \Zend_Loader_Autoloader;

class DoctrineTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->application = new Zend_Application('testing');
    require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
    $this->bootstrap = new \ZfAppBootstrap($this->application);
  }

  public function tearDown() {
  }

  public function testPassingDatabaseConfigurationSetsObjectState() {
    $config = array(
      'connection' => array(
        'driver' => 'pdo_sqlite',
        'path'   => ':memory:'
      ),
    );
    $resource = new Doctrine($config);
    $em = $resource->init();
    $this->assertTrue($em instanceof EntityManager);
  }
}
