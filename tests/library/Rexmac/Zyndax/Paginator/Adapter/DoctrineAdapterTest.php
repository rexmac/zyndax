<?php

namespace Rexmac\Zyndax\Paginator\Adapter;

use Rexmac\Zyndax\Doctrine\Entity\TestEntity;

class DoctrineAdapterTest extends \Rexmac\Zyndax\Test\PHPUnit\DoctrineTestCase {

  public function setUp() {
    parent::setUp();

    for($i = 0; $i < 100; ++$i) {
      self::$entityManager->persist(TestEntity::createRandomTestEntity($i, $i % 2));
    }
    self::$entityManager->flush();
    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(100, count($entities));

    $this->adapter = new DoctrineAdapter('Rexmac\Zyndax\Doctrine\Entity\TestEntity');
  }

  public function tearDown() {
    self::$entityManager->createQuery('DELETE FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity')->execute();
    $entities = self::$entityManager->createQuery('SELECT e FROM Rexmac\Zyndax\Doctrine\Entity\TestEntity e')->execute();
    $this->assertEquals(0, count($entities));
    $this->adapter = null;
    parent::tearDown();
  }

  public function testCount() {
    $this->assertEquals(100, $this->adapter->count());
  }

  public function testGetsItemsAtOffsetZero() {
    $actual = $this->adapter->getItems(0, 10);
    $this->assertEquals(10, count($actual));

    $i = 0;
    foreach($actual as $item) {
      $this->assertEquals($i++, $item->getInt());
    }
  }

  public function testGetsItemsAtOffsetTen() {
    $actual = $this->adapter->getItems(10, 10);
    $this->assertEquals(10, count($actual));

    $i = 10;
    foreach($actual as $item) {
      $this->assertEquals($i++, $item->getInt());
    }
  }

  public function testSortOrder() {
    $this->adapter = new DoctrineAdapter('Rexmac\Zyndax\Doctrine\Entity\TestEntity', 'id', 'DESC');
    $actual = $this->adapter->getItems(0, 10);
    $this->assertEquals(10, count($actual));

    $i = 99;
    foreach($actual as $item) {
      $this->assertEquals($i--, $item->getInt());
    }
  }

  public function testSortOrderOnForeignKey() {
    $this->adapter = new DoctrineAdapter('Rexmac\Zyndax\Doctrine\Entity\TestEntity', 'Rexmac\Zyndax\Doctrine\Entity\TestForeignEntity.id');
    $actual = $this->adapter->getItems(0, 10);
    $this->assertEquals(10, count($actual));

    $i = 0;
    foreach($actual as $item) {
      $this->assertEquals($i++, $item->getTestForeignEntity()->getInt());
    }

    $this->adapter = new DoctrineAdapter('Rexmac\Zyndax\Doctrine\Entity\TestEntity', 'Rexmac\Zyndax\Doctrine\Entity\TestForeignEntity.id', 'DESC');
    $actual = $this->adapter->getItems(0, 10);
    $this->assertEquals(10, count($actual));

    $i = 99;
    foreach($actual as $item) {
      $this->assertEquals($i--, $item->getTestForeignEntity()->getInt());
    }
  }

  public function testWhere() {
    $where = 'e.bool = 1';
    $actual = $this->adapter = new DoctrineAdapter('Rexmac\Zyndax\Doctrine\Entity\TestEntity', 'Rexmac\Zyndax\Doctrine\Entity\TestForeignEntity.id', 'ASC', $where);

    $this->assertEquals(50, count($actual));
  }
}
