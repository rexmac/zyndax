<?php

namespace Rexmac\Zyndax\Doctrine;

use Rexmac\Zyndax\Doctrine\Entity\TestEntity,
    Rexmac\Zyndax\Doctrine\Service\TestEntityService;

class ServiceTest extends \Rexmac\Zyndax\Test\PHPUnit\DoctrineTestCase {

  public function setUp() {
    parent::setUp();
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Unknown method: 'thisIsNotARealMethod'
   */
  public function testInvalidMethodName() {
    $this->assertEquals(null, TestEntityService::thisIsNotARealMethod());
  }

  public function testInvalidFindByMethodName() {
    $this->assertEquals(null, TestEntityService::findByThisIsNotARealMethod());
  }

  public function testCreateWithArray() {
    $testEntity = TestEntity::createTestEntity();
    TestEntityService::create($testEntity->toArray());

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  public function testCreateWithObject() {
    $testEntity = TestEntity::createTestEntity();
    TestEntityService::create($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($testEntity->toArray(), $entities[0]->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidParameterThrowsException() {
    TestEntityService::create('abc');
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testCreateWithInvalidObjectParameterThrowsException() {
    TestEntityService::create(new \stdClass());
  }

  public function testCreateOrUpdateCreate() {
    $testEntity = TestEntity::createTestEntity();
    $data = $testEntity->toArray();
    unset($data['id']);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(0, count($entities));

    $e = TestEntityService::createOrUpdate($data);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $d = $entities[0]->toArray();
    unset($d['id']);
    $this->assertEquals($data, $d);
  }

  public function testCreateOrUpdateUpdate() {
    $testEntity = TestEntity::createTestEntity();
    TestEntityService::create($testEntity);
    $data = $testEntity->toArray();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($data, $entities[0]->toArray());

    $data['name'] = 'notrandom';
    $this->assertNotEquals($data, $entities[0]->toArray());

    $e = TestEntityService::createOrUpdate($data, array('id' => $entities[0]->getId()));

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($data, $entities[0]->toArray());
  }

  public function testFindWithNoParametersReturnsAllEntities() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity());
    }
    self::$entityManager->flush();

    $entities = TestEntityService::find();
    $this->assertEquals(5, count($entities));
  }

  public function testFindWithParameter() {
    $testEntity = TestEntity::createTestEntity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = TestEntityService::find($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testFindById() {
    $testEntity = TestEntity::createTestEntity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = TestEntityService::findById($testEntity->getId());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testFindOneBy() {
    $testEntity = TestEntity::createTestEntity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entity = TestEntityService::findOneByName($testEntity->getName());
    $this->assertEquals($testEntity->toArray(), $entity->toArray());
  }

  public function testFindIndexedById() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity());
    }
    self::$entityManager->flush();

    $entities = TestEntityService::findIndexedById();
    foreach($entities as $id => $entity) {
      $this->assertEquals($id, $entity->getId());
    }
  }

  public function testFindIndexedByIdWithInvalidWhereReturnsEmptyArray() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity());
    }
    self::$entityManager->flush();

    $entities = TestEntityService::findIndexedById('id < 4');
    $this->assertEquals(0, count($entities));
  }

  public function testFindIndexedByIdWithValidWhere() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity());
    }
    self::$entityManager->flush();

    $entities = TestEntityService::findIndexedById();
    foreach($entities as $id => $entity) {
      $this->assertEquals($id, $entity->getId());
    }
  }

  public function testFindOrCreateCreates() {
    $testEntity = TestEntity::createTestEntity();
    $data = $testEntity->toArray();

    $e = TestEntityService::findOrCreate($data);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($e->toArray(), $entities[0]->toArray());
  }

  public function testFindOrCreateFinds() {
    $testEntity = TestEntity::createTestEntity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $e = TestEntityService::findOrCreate(array('id' => $testEntity->getId()));

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));
    $this->assertEquals($e->toArray(), $entities[0]->toArray());

  }

  public function testDelete() {
    $testEntity = TestEntity::createTestEntity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(1, count($entities));

    TestEntityService::delete($testEntity);

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(0, count($entities));
  }

  public function testUpdate() {
    $testEntity = TestEntity::createTestEntity();
    self::$entityManager->persist($testEntity);
    self::$entityManager->flush();

    $this->assertNotEquals('fubar', $testEntity->getName());
    $testEntity->setName('fubar');
    TestEntityService::update();

    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals('fubar', $entities[0]->getName());
  }

  public function testGetAllSortedByName() {
    $a = array('d', 'b', 'e', 'a', 'c');
    for($i = 0; $i < 5; ++$i) {
      $e = TestEntity::createRandomTestEntity();
      $e->setName($a[$i]);
      self::$entityManager->persist($e);
    }
    self::$entityManager->flush();

    sort($a);
    $es = TestEntityService::getAllSortedByName();
    $b = array();
    foreach($es as $e) $b[] = $e->getName();
    $this->assertTrue($a === $b);
  }

  public function testGetOptionsForSelect() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity());
    }
    self::$entityManager->flush();

    $options = TestEntityService::getOptionsForSelect();
    $this->assertEquals(5, count($options));
  }

  public function testGetTotal() {
    for($i = 0; $i < 5; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity());
    }
    self::$entityManager->flush();
    $this->assertEquals(5, TestEntityService::getTotal());
  }

  public function testGetTotalWhere() {
    for($i = 0; $i < 10; ++$i) {
      $e = TestEntity::createRandomTestEntity();
      $e->setBool($i % 2);
      self::$entityManager->persist($e);
    }
    self::$entityManager->flush();
    $this->assertEquals(5, TestEntityService::getTotal('e.bool = 1'));
  }

  public function testCanGetNewEntityManagerAfterPreviousIsClosed() {
    TestEntityService::getEntityManager()->close();
    $this->assertInstanceOf('Doctrine\ORM\EntityManager', TestEntityService::getEntityManager());
  }
}
