<?php

namespace Rexmac\Zyndax\Filter;

use Rexmac\Zyndax\Filter\UcFirst as UcFirstFilter,
    \Zend_Config,
    \Zend_Filter_Exception;

class UcFirstTest extends \PHPUnit_Framework_TestCase {
  /**
   * Rexmac\Zyndax\Filter\UcFirst object
   *
   * @var Rexmac\Zyndax\Filter\UcFirst
   */
  protected $_filter;

  /**
   * Creates a new Zend_Filter_StringToUpper object for each test method
   *
   * @return void
   */
  public function setUp() {
    $this->_filter = new UcFirstFilter();
  }

  /**
   * Ensures that the filter follows expected behavior
   *
   * @return void
   */
  public function testBasic() {
    $valuesExpected = array(
      'string' => 'String',
      'abC1@3' => 'AbC1@3',
      'a b C'  => 'A b C'
    );

    foreach($valuesExpected as $input => $output) {
      $this->assertEquals($output, $this->_filter->filter($input));
    }
  }

  /**
   * Ensures that the filter follows expected behavior with
   * specified encoding
   *
   * @return void
   */
  public function testWithEncoding() {
    $valuesExpected = array(
      'üa'    => 'Üa',
      'ña'    => 'Ña',
      'üñ123' => 'Üñ123'
    );

    try {
      $this->_filter->setEncoding('UTF-8');
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $this->_filter->filter($input));
      }
    } catch(Zend_Filter_Exception $e) {
      $this->assertContains('mbstring is required', $e->getMessage());
    }
  }

  /**
   * @return void
   */
  public function testFalseEncoding() {
    if(!function_exists('mb_strtolower')) {
      $this->markTestSkipped('mbstring required');
    }

    try {
      $this->_filter->setEncoding('aaaaa');
      $this->fail();
    } catch(Zend_Filter_Exception $e) {
      $this->assertContains('is not supported', $e->getMessage());
    }
  }

  public function testInitiationWithEncoding() {
    $valuesExpected = array(
      'üa'    => 'Üa',
      'ña'    => 'Ña',
      'üñ123' => 'Üñ123'
    );

    try {
      $filter = new UcFirstFilter(array('encoding' => 'UTF-8'));
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $filter->filter($input));
      }
    } catch(Zend_Filter_Exception $e) {
      $this->assertContains('mbstring is required', $e->getMessage());
    }

    try {
      $filter = new UcFirstFilter(new Zend_Config(array('encoding' => 'UTF-8')));
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $filter->filter($input));
      }
    } catch(Zend_Filter_Exception $e) {
      $this->assertContains('mbstring is required', $e->getMessage());
    }

    try {
      $filter = new UcFirstFilter('UTF-8');
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $filter->filter($input));
      }
    } catch(Zend_Filter_Exception $e) {
      $this->assertContains('mbstring is required', $e->getMessage());
    }
  }

  public function testCaseInsensitiveEncoding() {
    $valuesExpected = array(
      'üa'    => 'Üa',
      'ña'    => 'Ña',
      'üñ123' => 'Üñ123'
    );

    try {
      $this->_filter->setEncoding('UTF-8');
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $this->_filter->filter($input));
      }

      $this->_filter->setEncoding('utf-8');
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $this->_filter->filter($input));
      }

      $this->_filter->setEncoding('UtF-8');
      foreach($valuesExpected as $input => $output) {
        $this->assertEquals($output, $this->_filter->filter($input));
      }
    } catch(Zend_Filter_Exception $e) {
      $this->assertContains('mbstring is required', $e->getMessage());
    }
  }

  public function testDetectMbInternalEncoding() {
    if(!function_exists('mb_internal_encoding')) {
      $this->markTestSkipped("Function 'mb_internal_encoding' not available");
    }

    $this->assertEquals(mb_internal_encoding(), $this->_filter->getEncoding());
  }
}
