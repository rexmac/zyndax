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
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Service;

use \DateInterval,
    \DateTime;

/**
 * Service layer to ease the use and management of UserEmailVerification entities.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Service
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class UserEmailVerificationService extends \Rexmac\Zyndax\Doctrine\Service {

  /**
   * Remove all records older than 24 hours
   *
   * @return void
   */
  public static function collectGarbage() {
    $date = new DateTime();
    $date->sub(new DateInterval('PT24H'));
    $queryBuilder = self::getEntityManager()->createQueryBuilder();
    $queryBuilder->delete(self::getEntityClass(), 'e')
      ->where('e.requestDate < ?1')
      ->setParameter(1, $date->format('Y-m-d h:i:s'))
      ->getQuery()
      ->execute();
  }
}
