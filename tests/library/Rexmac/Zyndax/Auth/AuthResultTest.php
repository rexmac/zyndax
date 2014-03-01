<?php

namespace Rexmac\Zyndax\Auth;

class AuthResultTest extends \PHPUnit_Framework_TestCase {

  public function testConstructor() {
    $this->assertInstanceOf('Rexmac\Zyndax\Auth\AuthResult', new AuthResult(0, ''));
  }

  public function testIsValidReturnsFalseForInvalidCode() {
    $authResult = new AuthResult(AuthResult::FAILURE, '', array());
    $this->assertFalse($authResult->isValid());
  }

  public function testIsValidReturnsTrueForValidCode() {
    $authResult = new AuthResult(AuthResult::SUCCESS, '', array());
    $this->assertTrue($authResult->isValid());
  }

  public function testGetCode() {
    $authResult = new AuthResult(AuthResult::SUCCESS, '', array());
    $this->assertEquals($authResult->getCode(), AuthResult::SUCCESS);
  }

  public function testGetIdentity() {
    $identity = 'identity';
    $authResult = new AuthResult(AuthResult::SUCCESS, $identity, array());
    $this->assertEquals($authResult->getIdentity(), $identity);
  }

  public function testGetMessages() {
    $messages = array('one', 'two');
    $authResult = new AuthResult(AuthResult::SUCCESS, '', $messages);
    $this->assertEquals($authResult->getMessages(), $messages);
  }

  public function testConstructWithNegativeCodeResultsInFailureCode() {
    $authResult = new AuthResult(-99, '', array());
    $this->assertEquals($authResult->getCode(), AuthResult::FAILURE);
  }

  public function testConstructWithPositiveCodeGreaterThanSiuccessCodeResultsInSuccessCode() {
    $authResult = new AuthResult(99, '', array());
    $this->assertEquals($authResult->getCode(), AuthResult::SUCCESS);
  }
}
