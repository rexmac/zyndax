<?php

namespace Rexmac\Zyndax\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 */
class TestEntity extends \Rexmac\Zyndax\Doctrine\Entity {
  /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue   */
  protected $id = null;

  /** @ORM\Column(length=50,unique=true) */
  protected $name = null;

  /** @ORM\ManyToOne(targetEntity="TestForeignEntity",cascade={"persist"}) */
  protected $testForeignEntity = null;

  /** @ORM\Column(type="integer", nullable=true) */
  protected $int = null;

  /** @ORM\Column(type="boolean", nullable=true) */
  protected $bool = null;

  public static function createTestEntity() {
    return new TestEntity(array(
      'id'                => 1,
      'name'              => substr(sha1(mt_rand()), 0, 8),
      'testForeignEntity' => TestForeignEntity::createTestForeignEntity(),
      'int'               => 0,
      'bool'              => false,
    ));
  }

  public static function createRandomTestEntity($int = 0, $bool = false) {
    return new TestEntity(array(
      'name'              => substr(sha1(mt_rand()), 0, 8),
      'testForeignEntity' => TestForeignEntity::createRandomTestForeignEntity($int),
      'int'               => $int,
      'bool'              => $bool,
    ));
  }
}
