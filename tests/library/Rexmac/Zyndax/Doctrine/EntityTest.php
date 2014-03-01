<?php

namespace Rexmac\Zyndax\Doctrine;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\PersistentCollection,
    Mockery,
    Rexmac\Zyndax\Doctrine\Entity\TestEntity,
    Rexmac\Zyndax\Doctrine\Entity\TestForeignEntity;

class EntityTest extends \PHPUnit_Framework_TestCase {

  public static $testCollection = array(true);

  public static $testData = array(
    'id'   => 1,
    'name' => 'name',
    'testForeignEntity' => null,
    'collection' => null,
    'int'  => 0,
    'bool' => false
  );

  public function setUp() {
    self::$testData['testForeignEntity'] = TestForeignEntity::createTestForeignEntity();
    $mockEntityManager = Mockery::mock('Doctrine\ORM\EntityManager');
    self::$testCollection = new ArrayCollection(array(true));
    // "Pretend" to have a persistent collection
    self::$testData['collection'] = new PersistentCollection($mockEntityManager, 'TestEntity', self::$testCollection);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Doctrine\Entity\TestEntity', new TestEntity());
  }

  public function testConstructorInjectionOfProperties() {
    $testEntity = new TestEntity(self::$testData);
    $testData = self::$testData;
    $testData['collection'] = self::$testCollection;
    $this->assertEquals($testData, $testEntity->toArray());
  }

  public function testNonExistentMethod() {
    $testEntity = TestEntity::createTestEntity();
    $testEntity->foobar();
  }

  public function testGetters(TestEntity $testEntity = null) {
    if(null === $testEntity) $testEntity = new TestEntity(self::$testData);
    $this->assertEquals(self::$testData['id'], $testEntity->getId());
    $this->assertEquals(self::$testData['name'], $testEntity->getName());
    $this->assertEquals(self::$testData['testForeignEntity'], $testEntity->getTestForeignEntity());
    $this->assertEquals(self::$testData['collection'], $testEntity->getCollection());
    $this->assertEquals(self::$testData['int'], $testEntity->getInt());
    $this->assertEquals(self::$testData['bool'], $testEntity->getBool());
  }

  public function testSetters() {
    $testEntity = new TestEntity();
    $testEntity->setId(self::$testData['id']);
    $testEntity->setName(self::$testData['name']);
    $testEntity->setTestForeignEntity(self::$testData['testForeignEntity']);
    $testEntity->setCollection(self::$testData['collection']);
    $testEntity->setInt(self::$testData['int']);
    $testEntity->setBool(self::$testData['bool']);

    $this->testGetters($testEntity);
  }
}
