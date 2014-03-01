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
 * @subpackage Doctrine_Log
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Doctrine\Log;

use \Exception;

/**
 * SQL logger for use with Doctrine 2.0
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Doctrine_Log
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
*/
class SqlLogger implements \Doctrine\DBAL\Logging\SQLLogger {

  /**
   * Array of executed queries
   *
   * @var array
   */
  private $_queries = array();

  /**
   * Start time of most recent query
   *
   * @var float
   */
  private $_start = null;

  /**
   * Stream to which log messages will be written
   *
   * @var mixed
   */
  private $_stream = null;

  /**
   * Constructor
   *
   * @param  string $path Path or URL of stream
   * @return void
   */
  public function __construct($path) {
    if(!$this->_stream = @fopen($path, 'a', false)) {
      throw new Exception('"'.$path.'" could not be opened');
    }
  }

  /**
   * Destructor
   *
   * @return void
   */
  public function __destruct() {
    @fclose($this->_stream);
  }

  /**
   * Getter for _queries property
   *
   * @return array
   */
  public function getQueries() {
    return $this->_queries;
  }

  /**
   * Getter for _stream property
   *
   * @return mixed
   */
  public function getStream() {
    return $this->_stream;
  }

  /**
   * Logs a SQL statement somewhere.
   *
   * @param string $sql The SQL to be executed.
   * @param array  $params The SQL parameters.
   * @param array  $types Types?
   * @return void
   */
  public function startQuery($sql, array $params = null, array $types = null) {
    $this->_start = microtime(true);
    $this->_queries[count($this->_queries)] = array(
      'query'       => $sql,
      'params'      => $params,
      'types'       => $types,
      'executionMs' => 0
    );

    for($i = 0; $i < count($params); ++$i) {
      if(isset($params[$i]) && $params[$i] instanceof \DateTime) {
        $params[$i] = $params[$i]->format('Y-m-d');
      }
    }
    #\Rexmac\Log\Logger::debug('SQL::'.$sql.'::PARAMS::'.implode(',', $params).'::');
    #$msg = 'SQL::'.$sql.PHP_EOL.'PARAMS::'.implode(',', $params).PHP_EOL;
    $msg = 'SQL::'.$sql.PHP_EOL.'PARAMS::'.serialize($params).PHP_EOL;
    if(false === @fwrite($this->_stream, $msg)) {
      throw new Exception('Failed to write to stream');
    }
  }

  /**
   * Mark the last started query as stopped. This can be used for timing of queries.
   *
   * -codeCoverageIgnore
   * @return void
   */
  public function stopQuery() {
    $this->_queries[count($this->_queries) - 1]['executionMs'] = microtime(true) - $this->_start;
  }
}
