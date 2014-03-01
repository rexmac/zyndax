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
 * @subpackage Cron
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Cron;

/**
 * Class for managing cron jobs
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Cron
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Cron {

  /**
   * Add cronjob
   *
   * @param array $job Associate array in the form of ('interval' => '<INTERVAL_TO_RUN_COMMAND_AT>', command' => '<COMMAND_TO_BE_RUN>')
   * @param bool $duplicate Set to FALSE to prevent addition of duplicate jobs. Default FALSE,
   * @return void
   */
  public static function addJob(array $job, $duplicate = false) {
    // Create temp file
    $tmpCronFilePath = tempnam(sys_get_temp_dir(), 'zyndaxcron_');
    echo $tmpCronFilePath."\n";

    // Write existing crontab to temp file
    #$crontab = self::_writeCronTabToFile($tmpCronFilePath);
    self::_writeCronTabToFile($tmpCronFilePath);

    // Remove existing job
    if(!$duplicate) {
      #exec('sed /' .addslashes(addcslashes($job['command'], '/')) . '/d ' . $tmpCronFilePath);
      exec('sed /' . preg_replace('/\//', '\\\\\\\\/', $job['command']) . '/d ' . $tmpCronFilePath);
    }

    // Add job
    // */5 * * * *
    exec("echo '{$job['interval']} {$job['command']}' >> {$tmpCronFilePath}");

    // Install new crontab
    exec("crontab {$tmpCronFilePath}");

    // Remove temporary file
    unlink($tmpCronFilePath);
  }

  /**
   * Retrieve existing crontab
   *
   * @return string Existing crontab
   */
  public static function getCronTab() {
    exec('crontab -l 2> /dev/null');
  }

  /**
   * Retrieve schedule for given process
   *
   * @param string $process
   * @return array
   */
  public static function getScheduleForProcess($process) {
    $predefinedFrequencies = array(
      '@yearly'   => array('min' => 0, 'hour' => 0,   'day' => 1,   'month' => 1,   'dow' => '*'),
      '@annually' => array('min' => 0, 'hour' => 0,   'day' => 1,   'month' => 1,   'dow' => '*'),
      '@monthly'  => array('min' => 0, 'hour' => 0,   'day' => 1,   'month' => '*', 'dow' => '*'),
      '@weekly'   => array('min' => 0, 'hour' => 0,   'day' => '*', 'month' => '*', 'dow' => 0),
      '@daily'    => array('min' => 0, 'hour' => 0,   'day' => '*', 'month' => '*', 'dow' => '*'),
      '@midnight' => array('min' => 0, 'hour' => 0,   'day' => '*', 'month' => '*', 'dow' => '*'),
      '@hourly'   => array('min' => 0, 'hour' => '*', 'day' => '*', 'month' => '*', 'dow' => '*'),
    );
    $numberRanges = array(
      'min'   => '[0-5]?\d',
      'hour'  => '[01]?\d|2[0-3]',
      'day'   => '0?[1-9]|[12]\d|3[01]',
      'month' => '[1-9]|1[012]',
      'dow'   => '[0-7]'
    );

    foreach($numberRanges as $field => $numberRange) {
      $range = "($numberRange)(-($numberRange)(\/\d+)?)?";
      $fieldRegex[$field] = "\*(\/\d+)?|$range(,$range)*";
    }

    $fieldRegex['month'] .= '|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';
    $fieldRegex['dow'] .= '|mon|tue|wed|thu|fri|sat|sun';

    $fieldRegex = '(' . implode(')\s+(', $fieldRegex) . ')';

    #$replacements= '@reboot|' . implode('|', $predefinedFrequencies);
    $replacements= implode('|', $predefinedFrequencies);

    #$regex = "^\s*($|#|\w+\s*=|$fieldRegex\s+\S|($replacements)\s+\S)";
    $frequencyRegex = "^\s*($fieldRegex|($replacements))";
    if(preg_match("/$frequencyRegex\s+" . preg_quote($process) . '$/', self::getCronTab(), $matches)) {
      error_log(var_export($matches, true));
      #$frequency = $matches[1];
      if(isset($matches[2])) {
        return $predefinedFrequencies[$matches[2]];
      }

    }
    return null;
  }

  /**
   * Write existing crontab to file
   *
   * @param string $path Path to write existing crontab to
   * @return void
   */
  private static function _writeCrontabToFile($path) {
    exec("crontab -l > {$path}");
  }
}

/**
 * Cron exception class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Cron
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class CronException extends \Zend_Exception {
}
