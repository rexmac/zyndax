<?php

namespace Rexmac\Zyndax\Log;

use \Zend_Log;

/**
 * @runTestsInSeparateProcesses
 */
class LoggerTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    Logger::$logStream = APPLICATION_PATH . '/../tests/log/test.log';
    #@unlink(Logger::$logStream);
  }

  public function tearDown() {
    @unlink(Logger::$logStream);
  }

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Log\Logger', Logger::getInstance());
  }

  public function testConstructorWithNullLogstream() {
    $path = '/tmp/log';
    @unlink($path);
    Logger::$logStream = null;
    $this->assertInstanceOf('Rexmac\Zyndax\Log\Logger', Logger::getInstance());
    $this->assertTrue(file_exists($path));
    @unlink($path);
  }

  public function testGetLog() {
    $this->assertInstanceOf('Zend_Log', Logger::getInstance()->getLog());
  }

  public function testEmerg() {
    Logger::emerg('foo');
    $this->assertEquals(1, preg_match('/EMERG \(' . Zend_Log::EMERG . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testAlert() {
    Logger::alert('foo');
    $this->assertEquals(1, preg_match('/ALERT \(' . Zend_Log::ALERT . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testCrit() {
    Logger::crit('foo');
    $this->assertEquals(1, preg_match('/CRIT \(' . Zend_Log::CRIT . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testError() {
    Logger::err('foo');
    $this->assertEquals(1, preg_match('/ERR \(' . Zend_Log::ERR . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testWarn() {
    Logger::warn('foo');
    $this->assertEquals(1, preg_match('/WARN \(' . Zend_Log::WARN . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testNotice() {
    Logger::notice('foo');
    $this->assertEquals(1, preg_match('/NOTICE \(' . Zend_Log::NOTICE . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testInfo() {
    Logger::info('foo');
    $this->assertEquals(1, preg_match('/INFO \(' . Zend_Log::INFO . '\): foo/', file_get_contents(Logger::$logStream)));
  }

  public function testDebug() {
    Logger::debug('foo');
    $this->assertEquals(1, preg_match('/DEBUG \(' . Zend_Log::DEBUG . '\): foo/', file_get_contents(Logger::$logStream)));
  }
}
