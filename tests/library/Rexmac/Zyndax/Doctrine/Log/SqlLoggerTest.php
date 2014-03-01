<?php

namespace Rexmac\Zyndax\Doctrine\Log;

class SqlLoggerTest extends \PHPUnit_Framework_TestCase {

  public function testConstructor() {
    $log = new SqlLogger('php://memory');
    $this->assertTrue($log instanceof SqlLogger);
  }

  public function testConstructWithBadPathThrowsException() {
    try {
  $log = new SqlLogger('');
  $this->fail();
    } catch(\Exception $e) {
  $this->assertEquals('"" could not be opened', $e->getMessage());
    }
  }

  public function testStartQuery() {
    $log = new SqlLogger('php://memory');
    $stream = $log->getStream();

    $sql = 'SELECT * FROM test WHERE a1 = ? AND a2 = ?';
    $params = array('one', 'two');
    $log->startQuery($sql, $params);

    $msg = 'SQL::'.$sql.PHP_EOL.'PARAMS::'.serialize($params).PHP_EOL;
    rewind($stream);
    $logged = stream_get_contents($stream);
    $this->assertEquals($msg, $logged);
  }

  public function testStartQueryWithDateTimeParameter() {
    $log = new SqlLogger('php://memory');
    $stream = $log->getStream();

    $sql = 'SELECT * FROM test WHERE a1 = ?';
    $params = array(new \DateTime());
    $log->startQuery($sql, $params);

    $params[0] = $params[0]->format('Y-m-d');
    $msg = 'SQL::'.$sql.PHP_EOL.'PARAMS::'.serialize($params).PHP_EOL;
    rewind($stream);
    $logged = stream_get_contents($stream);
    $this->assertEquals($msg, $logged);
  }

  public function testStartQueryThrowsExceptionWhenStreamWriteFails() {
    $log = new SqlLogger('php://memory');
    fclose($log->getStream()); // Prematurely close the logger's stream

    // Now have logger attempt to write to its stream
    $sql = 'SELECT * FROM test WHERE a1 = ? AND a2 = ?';
    $params = array('one', 'two');

    try {
  $log->startQuery($sql, $params);
  $this->fail();
    } catch(\Exception $e) {
  $this->assertEquals('Failed to write to stream', $e->getMessage());
    }
  }
}
