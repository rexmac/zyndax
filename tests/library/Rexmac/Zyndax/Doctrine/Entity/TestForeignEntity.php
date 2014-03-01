<?php

namespace Rexmac\Zyndax\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 */
class TestForeignEntity extends \Rexmac\Zyndax\Doctrine\Entity {
  /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
  protected $id = null;

  /** @ORM\Column(length=50,unique=true) */
  protected $name = null;

  /** @ORM\Column(type="integer", nullable=true) */
  protected $int = null;

  public static function createTestForeignEntity() {
    return new TestForeignEntity(array(
      'id'   => 1,
      'name' => substr(sha1(mt_rand()), 0, 8)
    ));
  }
  public static function createRandomTestForeignEntity($int = 0) {
    return new TestForeignEntity(array(
      'name' => substr(sha1(mt_rand()), 0, 8),
      'int'  => $int,
    ));
  }
}
