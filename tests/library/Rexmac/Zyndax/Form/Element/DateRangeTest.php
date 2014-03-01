<?php

namespace Rexmac\Zyndax\Form\Element;

use \DateInterval,
    \DateTime,
    \DateTimeZone,
    Rexmac\Zyndax\Form\Element\DateRange as DateRangeElement,
    \Zend_Form_Element,
    \Zend_Form_Element_Xhtml,
    \Zend_View;

class DateRangeTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test DateRange element
   *
   * @var Rexmac\Zyndax\Form\Element\DateRange
   */
  private $element;

  public function setUp() {
    $this->element = new DateRangeElement('foo');
  }

  public function getView() {
    $view = new Zend_View();
    #$view->addHelperPath(dirname(__FILE__) . '/../../../../../../library/Rexmac/Zyndax/View/Helper', 'Rexmac\Zyndax\View\Helper\\');
    return $view;
  }

  public function testDateRangeElementSubclassesXhtmlElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
  }

  public function testDateRangeElementInstanceOfBaseElement() {
    $this->assertTrue($this->element instanceof Zend_Form_Element);
  }

  public function testGettingValue() {
    $this->assertEquals($this->element, $this->element->getValue());
  }

  public function testGettingRangedDates() {
    $rangedDates = $this->element->getRangedDates();

    // Test TODAY range
    $startDate = new DateTime();
    $startDate->setTime(0, 0, 0);
    $stopDate = new DateTime();
    $stopDate->setTime(23, 59, 59);
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::TODAY][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::TODAY][1]);

    // Test YESTERDAY range
    $startDate->sub(new DateInterval('P1D'));
    $stopDate->sub(new DateInterval('P1D'));
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::YESTERDAY][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::YESTERDAY][1]);

    // Test LAST_7_DAYS range
    $startDate = new DateTime();
    $startDate->setTime(0, 0, 0)->sub(new DateInterval('P6D'));
    $stopDate = new DateTime();
    $stopDate->setTime(23, 59, 59);
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::LAST_7_DAYS][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::LAST_7_DAYS][1]);

    // Test MTD range
    $startDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1);
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::MTD][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::MTD][1]);

    // Test LAST_MTD range
    $startDate->sub(new DateInterval('P1M'));
    $stopDate->sub(new DateInterval('P1M'));
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::LAST_MTD][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::LAST_MTD][1]);

    // Test LAST_MONTH range
    $stopDate = new DateTime();
    $stopDate->setTime(23, 59, 59)->setDate($stopDate->format('Y'), $stopDate->format('m'), 1)->sub(new DateInterval('P1D'));
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::LAST_MONTH][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::LAST_MONTH][1]);

    // Test YTD range
    $startDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), 1, 1);
    $stopDate = new DateTime();
    $stopDate->setTime(23, 59, 59);
    $this->assertEquals($startDate, $rangedDates[DateRangeElement::YTD][0]);
    $this->assertEquals($stopDate, $rangedDates[DateRangeElement::YTD][1]);
  }

  public function testSettingStartDate() {
    $startDate = new DateTime('2001-01-01 12:34:56');
    $returned = $this->element->setStartDate($startDate);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($startDate, $this->element->getStartDate());
  }

  public function testSettingStopDate() {
    $stopDate = new DateTime('2001-01-01 12:34:56');
    $returned = $this->element->setStopDate($stopDate);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingRange() {
    $returned = $this->element->setRange(DateRangeElement::CUSTOM);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals(DateRangeElement::CUSTOM, $this->element->getRange());
  }

  public function testSettingTimeZone() {
    $timeZone = new DateTimeZone('America/Los_Angeles');
    $returned = $this->element->setTimeZone($timeZone);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($timeZone, $this->element->getTimeZone());
  }

  public function testGettingStartDateString() {
    $startDate = new DateTime();
    $this->element->setStartDate($startDate);
    $this->assertEquals($startDate->format('Y-m-d'), $this->element->getStartDateString());
  }

  public function testGettingStopDateString() {
    $stopDate = new DateTime();
    $this->element->setStopDate($stopDate);
    $this->assertEquals($stopDate->format('Y-m-d'), $this->element->getStopDateString());
  }

  public function testGettingUtcStartDate() {
    $timeZone = new DateTimeZone('America/Los_Angeles');
    $this->element->setTimeZone($timeZone);
    $startDate = new DateTime('now', $timeZone);
    $utcStartDate = new DateTime('now', new DateTimeZone('UTC'));
    $this->element->setStartDate($startDate);
    $this->assertEquals($utcStartDate, $this->element->getUtcStartDate());
  }

  public function testGettingUtcStopDate() {
    $timeZone = new DateTimeZone('America/Los_Angeles');
    $this->element->setTimeZone($timeZone);
    $stopDate = new DateTime('now', $timeZone);
    $utcStopDate = new DateTime('now', new DateTimeZone('UTC'));
    $this->element->setStopDate($stopDate);
    $this->assertEquals($utcStopDate, $this->element->getUtcStopDate());
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Invalid date range
   */
  public function testSettingRangeWithInvalidValueThrowsInvalidArgumentException() {
    $this->element->setRange(-1);
  }

  public function testSettingStartDateAndStopDateSetsCustomRange() {
    $startDate = new DateTime('2001-01-01 12:34:56');
    $stopDate = new DateTime('2001-01-01 12:34:56');
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::CUSTOM, $this->element->getRange());
  }

  public function testSettingStartDateAndStopDateSetsTodayRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0);
    $stopDate->setTime(23, 59, 59);
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::TODAY, $this->element->getRange());
  }

  public function testSettingStartDateAndStopDateSetsYesterdayRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->sub(new DateInterval('P1D'));
    $stopDate ->setTime(23, 59, 59)->sub(new DateInterval('P1D'));
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::YESTERDAY, $this->element->getRange());
  }

  public function testSettingStartDateAndStopDateSetsLast7DaysRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->sub(new DateInterval('P6D'));
    $stopDate->setTime(23, 59, 59);
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    if('01' === $startDate->format('d')) {
      $this->assertEquals(DateRangeElement::MTD, $this->element->getRange());
    } else {
      $this->assertEquals(DateRangeElement::LAST_7_DAYS, $this->element->getRange());
    }
  }

  public function testSettingStartDateAndStopDateSetsMtdRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1);
    $stopDate->setTime(23, 59, 59);
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::MTD, $this->element->getRange());
  }

  public function testSettingStartDateAndStopDateSetsLastMtdRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1)->sub(new DateInterval('P1M'));
    $stopDate->setTime(23, 59, 59)->sub(new DateInterval('P1M'));
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::LAST_MTD, $this->element->getRange());
  }

  public function testSettingStartDateAndStopDateSetsLastMonthRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1)->sub(new DateInterval('P1M'));
    $stopDate->setTime(23, 59, 59)->setDate($stopDate->format('Y'), $stopDate->format('m'), 1)->sub(new DateInterval('P1D'));
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::LAST_MONTH, $this->element->getRange());
  }

  public function testSettingStartDateAndStopDateSetsYtdRange() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), 1, 1);
    $stopDate->setTime(23, 59, 59);
    $this->element->setStartDate($startDate)->setStopDate($stopDate);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
    $this->assertEquals(DateRangeElement::YTD, $this->element->getRange());
  }

  public function testSettingCustomRangeDoesNotSetDates() {
    $startDate = new DateTime('2001-01-01 12:34:56');
    $stopDate = new DateTime('2001-01-01 12:34:56');
    $this->element->setStartDate($startDate);
    $this->element->setStopDate($stopDate);
    $this->element->setRange(DateRangeElement::CUSTOM);
    $this->assertEquals(DateRangeElement::CUSTOM, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingTodayRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0);
    $stopDate->setTime(23, 59, 59);
    $this->element->setRange(DateRangeElement::TODAY);
    $this->assertEquals(DateRangeElement::TODAY, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingYesterdayRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->sub(new DateInterval('P1D'));
    $stopDate->setTime(23, 59, 59)->sub(new DateInterval('P1D'));
    $this->element->setRange(DateRangeElement::YESTERDAY);
    $this->assertEquals(DateRangeElement::YESTERDAY, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingLast7DaysRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->sub(new DateInterval('P6D'));
    $stopDate->setTime(23, 59, 59);
    $this->element->setRange(DateRangeElement::LAST_7_DAYS);
    $this->assertEquals(DateRangeElement::LAST_7_DAYS, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }
  public function testSettingMtdRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1);
    $stopDate->setTime(23, 59, 59);
    $this->element->setRange(DateRangeElement::MTD);
    $this->assertEquals(DateRangeElement::MTD, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingLastMtdRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1)->sub(new DateInterval('P1M'));
    $stopDate->setTime(23, 59, 59)->sub(new DateInterval('P1M'));
    $this->element->setRange(DateRangeElement::LAST_MTD);
    $this->assertEquals(DateRangeElement::LAST_MTD, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingLastMonthRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), $startDate->format('m'), 1)->sub(new DateInterval('P1M'));
    $stopDate->setTime(23, 59, 59)->setDate($stopDate->format('Y'), $stopDate->format('m'), 1)->sub(new DateInterval('P1D'));
    $this->element->setRange(DateRangeElement::LAST_MONTH);
    $this->assertEquals(DateRangeElement::LAST_MONTH, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingYtdRangeSetsDates() {
    $startDate = new DateTime();
    $stopDate = new DateTime();
    $startDate->setTime(0, 0, 0)->setDate($startDate->format('Y'), 1, 1);
    $stopDate->setTime(23, 59, 59);
    $this->element->setRange(DateRangeElement::YTD);
    $this->assertEquals(DateRangeElement::YTD, $this->element->getRange());
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Invalid date range provided
   */
  public function testSettingValueWithInvalidValueThrowsInvalidArgumentException() {
    $this->element->setValue(null);
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Invalid date range provided
   */
  public function testSettingValueWithInvalidArrayValueThrowsInvalidArgumentException() {
    $this->element->setValue(array(null));
  }

  public function testSettingValueWithNumericalArrayOfDates() {
    $value = array(
      new DateTime('2001-01-01 12:34:56'),
      new DateTime('2002-02-02 12:34:56')
    );
    $returned = $this->element->setValue($value);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($value[0], $this->element->getStartDate());
    $this->assertEquals($value[1], $this->element->getStopDate());
  }

  public function testSettingValueWithAssociativeArrayOfDateTimes() {
    $value = array(
      'start' => new DateTime('2001-01-01 12:34:56'),
      'stop'  => new DateTime('2002-02-02 12:34:56')
    );
    $returned = $this->element->setValue($value);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($value['start'], $this->element->getStartDate());
    $this->assertEquals($value['stop'], $this->element->getStopDate());
  }

  public function testSettingValueWithAssociativeArrayOfDates() {
    $value = array(
      'start' => '2001-01-01',
      'stop'  => '2002-02-02'
    );
    $startDate = new DateTime($value['start'] . ' 00:00:00');
    $stopDate  = new DateTime($value['stop'] . ' 23:59:59');
    $returned = $this->element->setValue($value);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($startDate, $this->element->getStartDate());
    $this->assertEquals($stopDate, $this->element->getStopDate());
  }

  public function testSettingValueWithAssociateArrayOfRange() {
    $value = array('range' => DateRangeElement::TODAY);
    $returned = $this->element->setValue($value);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals($value['range'], $this->element->getRange());
  }

  public function testSettingValueWithNumericalRange() {
    $returned = $this->element->setValue(DateRangeElement::TODAY);
    $this->assertEquals($this->element, $returned);
    $this->assertEquals(DateRangeElement::TODAY, $this->element->getRange());
  }

  public function testGettingRangedTimestamps() {
    $ranges = array(
      DateRangeElement::TODAY,
      DateRangeElement::YESTERDAY,
      DateRangeElement::LAST_7_DAYS,
      DateRangeElement::MTD,
      DateRangeElement::LAST_MTD,
      DateRangeElement::LAST_MONTH,
      DateRangeElement::YTD
    );

    $timeZone = new DateTimeZone('America/Los_Angeles');
    $rangedDates = $this->element->getRangedDates();
    $rangedTimestamps = $this->element->getRangedTimestamps($timeZone);

    foreach($ranges as $range) {
      $startDate = clone $rangedDates[$range][0];
      $stopDate  = clone $rangedDates[$range][1];
      $this->assertEquals($startDate->getTimestamp() + $timeZone->getOffset($startDate), $rangedTimestamps[$range][0]);
      $this->assertEquals($stopDate->getTimestamp() + $timeZone->getOffset($stopDate), $rangedTimestamps[$range][1]);
    }
  }
}
