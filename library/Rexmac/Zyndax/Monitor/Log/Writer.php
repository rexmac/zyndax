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
 * @subpackage Monitor_Log
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Monitor\Log;

use \DateTime,
    Rexmac\Zyndax\Monitor\Service\ErrorService;

/**
 * Custom DB log writer for Monitor

 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Monitor_Log
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Writer extends \Zend_Log_Writer_Abstract {
  /**
   * Class constructor
   *
   * @return void
   */
  public function __construct() {
  }

  /**
   * Create a new instance of Writer
   *
   * @param array|Zend_Config $config
   * @return Writer
   */
  static public function factory($config) {
    return new self();
  }

  /**
   * Write a message to the log
   *
   * @param array $event event data
   * @return void
   */
  protected function _write($event) {
    $dataToInsert = $event;

    // Replace timestamp string
    $dataToInsert['date'] = new DateTime($dataToInsert['timestamp']);
    unset($dataToInsert['timestamp']);

    // Insert entry into DB
    ErrorService::create($dataToInsert);
  }
}
