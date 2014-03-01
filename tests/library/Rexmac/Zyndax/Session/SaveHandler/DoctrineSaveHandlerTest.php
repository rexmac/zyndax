<?php

namespace Rexmac\Zyndax\Session\SaveHandler;

use \Zend_Config,
    \Zend_Session,
    \Zend_Session_Namespace,
    \Zend_Session_SaveHandler_Exception;

class DoctrineSaveHandlerTest extends \Rexmac\Zyndax\Test\PHPUnit\SessionTestCase {

  private $testArray = array(
    'a' => 0,
    'b' => true,
    'c' => 'foo'
  );

  private $testConfig = array(
    'entityClass' => 'Rexmac\Zyndax\Entity\Session'
  );

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testConstructorWithZendConfig() {
    $config = new Zend_Config($this->testArray);
    $saveHandler = new DoctrineSaveHandler($config);
    $this->assertTrue($saveHandler instanceof DoctrineSaveHandler);
  }

  public function testConstructorThrowsExceptionGivenConfigAsNull() {
    try {
      $saveHandler = new DoctrineSaveHandler(null);
      $this->fail('Expected Zend_Session_SaveHandler_Exception not thrown');
    } catch(Zend_Session_SaveHandler_Exception $e) {
      $this->assertContains('$config must be', $e->getMessage());
    }
  }

  public function testLifetime() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $saveHandler->setLifetime(5);
    $this->assertEquals(5, $saveHandler->getLifetime());
  }

  public function testSessionSaving() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    Zend_Session::setSaveHandler($saveHandler);
    Zend_Session::start();

    $session = new Zend_Session_Namespace('SaveHandler');
    $session->testArray = $this->testArray;

    $tmp = array('SaveHandler' => serialize(array('testArray' => $this->testArray)));
    $testAgainst = '';
    foreach($tmp as $key => $val) {
      $testAgainst .= $key.'|'.$val;
    }

    session_write_close();

    $sessions = self::$entityManager->createQuery('SELECT s FROM '.$this->testConfig['entityClass'].' s')->execute();
    foreach($sessions as $session) {
      $this->assertSame($testAgainst, $session->getData(), 'Data was not saved properly');
    }
  }

  public function testReadWrite() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $id = '242';

    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $this->assertSame($this->testArray, unserialize($saveHandler->read($id)));
  }

  public function testReadWriteTwice() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $id = '242';

    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $this->assertSame($this->testArray, unserialize($saveHandler->read($id)));

    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $this->assertSame($this->testArray, unserialize($saveHandler->read($id)));
  }

  public function testAttemptToReadExpiredSessionCausesSessionToBeDestroyed() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $saveHandler->setLifetime(1);
    $id = '242';
    $this->assertTrue($saveHandler->destroy($id));
    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $query = self::$entityManager->createQuery('SELECT s FROM '.$this->testConfig['entityClass'].' s');
    $sessions = $query->execute();
    $this->assertEquals(1, count($sessions));

    sleep(2);
    $this->assertTrue($saveHandler->read('242'));
    $sessions = $query->execute();
    $this->assertEquals(0, count($sessions));
  }

  public function testDestroy() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $id = '242';
    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $query = self::$entityManager->createQuery('SELECT s FROM '.$this->testConfig['entityClass'].' s');
    $sessions = $query->execute();
    $this->assertEquals(1, count($sessions));
    $this->assertTrue($saveHandler->destroy($id));
    $sessions = $query->execute();
    $this->assertEquals(0, count($sessions));
  }

  public function testDestroyNonExistent() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $id = '242';
    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $query = self::$entityManager->createQuery('SELECT s FROM '.$this->testConfig['entityClass'].' s');
    $sessions = $query->execute();
    $this->assertEquals(1, count($sessions));
    $this->assertFalse($saveHandler->destroy('131'));
    $sessions = $query->execute();
    $this->assertEquals(1, count($sessions));
  }

  public function testGarbageCollection() {
    $saveHandler = new DoctrineSaveHandler($this->testConfig);
    $id = '242';
    $this->assertTrue($saveHandler->write($id, serialize($this->testArray)));
    $query = self::$entityManager->createQuery('SELECT s FROM '.$this->testConfig['entityClass'].' s');
    $sessions = $query->execute();
    $this->assertEquals(1, count($sessions));
    sleep(2);
    $this->assertTrue($saveHandler->gc(1));
    $sessions = $query->execute();
    $this->assertEquals(0, count($sessions));
  }
}

