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
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Form\Element;

use \DateInterval,
    \DateTime,
    \DateTimeZone,
    \InvalidArgumentException;

/**
 * Form element that provides fields for selecting a date range.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class DateRange extends \Zend_Form_Element_Xhtml {

  /**
   * Date range ID; one of the predefined date range constants
   *
   * @var int
   */
  protected $_range       = null;

  /**
   * Array of date ranges. Key is date range ID. Value is a two-element
   * array containing DateTime objects for the beginning and ending
   * dates.
   *
   * @var array
   */
  protected $_rangedDates = null;

  /**
   * Beginning date
   *
   * @var DateTime
   */
  protected $_startDate   = null;

  /**
   * Ending date
   *
   * @var DateTime
   */
  protected $_stopDate    = null;

  /**
   * Timezone to be used when calculating dates
   *
   * @var DateTimeZone
   */
  protected $_timeZone    = null;

  /**
   * Date range constant representing today's date
   */
  const TODAY = 1;

  /**
   * Date range constant representing yesteray's date
   */
  const YESTERDAY = 2;

  /**
   * Date range constant representing the last 7 days (including today)
   */
  const LAST_7_DAYS = 3;

  /**
   * Date range constant representing the current month to date
   */
  const MTD = 4;

  /**
   * Date range constant representing the last month to date
   */
  const LAST_MTD = 5;

  /**
   * Date range constant representing the entirety of last month
   */
  const LAST_MONTH = 6;

  /**
   * Date range constant representing the current year to date
   */
  const YTD = 7;

  /**
   * Date range constant representing a custom date range
   */
  const CUSTOM = 8;

  /**
   * Constructor
   *
   * @param string $spec
   * @param array $options
   * @return void
   */
  public function __construct($spec, $options = null) {
    $this->addPrefixPath(
      'Rexmac\Zyndax\Form\Decorator\\',
      'Rexmac/Zyndax/Form/Decorator',
      'decorator'
    );
    $this->setTimeZone(isset($options['timeZone']) ? $options['timeZone'] : new DateTimeZone('UTC'));
    $this->setRangedDates(self::_calculateRangedDates($this->getTimeZone()));
    $this->setRange(self::TODAY);
    parent::__construct($spec, $options);
  }

  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators() {
    if($this->loadDefaultDecoratorsIsDisabled()) return;

    $decorators = $this->getDecorators();
    if(empty($decorators)) {
      $this->addDecorator('DateRange')
           ->addDecorator('Errors')
           ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
           ->addDecorator('HtmlTag', array('tag' => 'dd', 'id' => $this->getName().'-element'))
           ->addDecorator('Label', array('tag' => 'dt'));
    }
  }

  /**
   * Getter for range
   *
   * @return int
   */
  public function getRange() {
    return $this->_range;
  }

  /**
   * Getter for rangeDates
   *
   * @return array
   */
  public function getRangedDates() {
    return $this->_rangedDates;
  }

  /**
   * Getter for startDate
   *
   * @return DateTime
   */
  public function getStartDate() {
    return $this->_startDate;
  }

  /**
   * Getter for startDate as string
   *
   * @return string
   */
  public function getStartDateString() {
    return $this->_startDate->format('Y-m-d');
  }

  /**
   * Getter for stopDate
   *
   * @return DateTime
   */
  public function getStopDate() {
    return $this->_stopDate;
  }

  /**
   * Getter for stopDate as string
   *
   * @return string
   */
  public function getStopDateString() {
    return $this->_stopDate->format('Y-m-d');
  }

  /**
   * Getter for timezone
   *
   * @return DateTimeZone
   */
  public function getTimeZone() {
    return $this->_timeZone;
  }

  /**
   * Return element's value
   *
   * @return string
   */
  public function getValue() {
    return $this;
  }

  /**
   * Setter for range
   *
   * @param int $range
   * @param bool $setDates [Optional] Set to false to NOT set start and stop dates. Default is true.
   * @return DateRange Fluent-interface
   */
  public function setRange($range, $setDates = true) {
    if($setDates) {
      switch($range) {
        case self::YESTERDAY:
        case self::LAST_7_DAYS:
        case self::MTD:
        case self::LAST_MTD:
        case self::LAST_MONTH:
        case self::YTD:
        case self::TODAY:
          $this->setStartDate($this->_rangedDates[$range][0]);
          $this->setStopDate($this->_rangedDates[$range][1]);
          break;
        case self::CUSTOM:
          break;
        default:
          throw new InvalidArgumentException('Invalid date range');
      }
    }
    $this->_range = $range;
    return $this;
  }

  /**
   * Setter for ranged dates
   *
   * @param array $rangedDates
   * @return Rexmac\Zyndax\Form\Element\DateRange Fluent-interface
   */
  public function setRangedDates(array $rangedDates) {
    $this->_rangedDates = $rangedDates;
    return $this;
  }

  /**
   * Setter for startDate
   *
   * @param DateTime $date
   * @return Rexmac\Zyndax\Form\Element\DateRange Fluent-interface
   */
  public function setStartDate(DateTime $date) {
    $this->_startDate = $date;

    foreach($this->_rangedDates as $range => $dates) {
      if($dates[0] == $date && $dates[1] == $this->_stopDate) {
        $this->setRange($range, false);
        return $this;
      }
    }
    $this->setRange(self::CUSTOM, false);

    return $this;
  }

  /**
   * Setter for stopDate
   *
   * @param DateTime $date
   * @return Rexmac\Zyndax\Form\Element\DateRange Fluent-interface
   */
  public function setStopDate(DateTime $date) {
    $this->_stopDate = $date;

    foreach($this->_rangedDates as $range => $dates) {
      if($dates[1] == $date && $dates[0] == $this->_startDate) {
        $this->setRange($range, false);
        return $this;
      }
    }
    $this->setRange(self::CUSTOM, false);

    return $this;
  }

  /**
   * Setter for timezone
   *
   * @param DateTimeZone $tz
   * @return Rexmac\Zyndax\Form\Element\DateRange Fluent-interface
   */
  public function setTimeZone(DateTimeZone $tz) {
    $this->_timeZone = $tz;
    return $this;
  }

  /**
   * Get startDate as UTC string
   *
   * @return string
   */
  public function getUtcStartDate() {
    $startDate = clone $this->_startDate;
    $startDate->setTimezone(new DateTimeZone('UTC'));
    return $startDate;
  }

  /**
   * Get stopDate as UTC string
   *
   * @return string
   */
  public function getUtcStopDate() {
    $stopDate = clone $this->_stopDate;
    $stopDate->setTimezone(new DateTimeZone('UTC'));
    return $stopDate;
  }

  /**
   * Set element's value
   *
   * @param mixed $value
   * @return Rexmac\Zyndax\Form\Element\DateRange Fluent-interface
   */
  public function setValue($value) {
    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__."::\$value=".var_export($value, true));
    if(is_array($value)) {
      if((isset($value[0]) && $value[0] instanceof DateTime) && (isset($value[1]) && $value[1] instanceof DateTime)) {
        $this->setStartDate($value[0])->setStopDate($value[1]);
      } elseif((isset($value['start']) && $value['start'] instanceof DateTime) && (isset($value['stop']) && $value['stop'] instanceof DateTime)) {
        $this->setStartDate($value['start'])->setStopDate($value['stop']);
      } elseif((isset($value['start']) && '' != $value['start']) && (isset($value['stop']) && '' != $value['stop'])) {
        $start = new DateTime($value['start'], $this->_timeZone);
        $start->setTime(0, 0, 0);
        $stop = new DateTime($value['stop'], $this->_timeZone);
        $stop->setTime(23, 59, 59);
        $this->setStartDate($start)->setStopDate($stop);
      } elseif(isset($value['range'])) {
        $this->setRange($value['range']);
      } else {
        throw new InvalidArgumentException('Invalid date range provided');
      }
    } elseif(is_numeric($value)) {
      $this->setRange((int)$value);
    } else {
      throw new InvalidArgumentException('Invalid date range provided');
    }

    return $this;
  }

  /**
   * Calculate date ranges
   *
   * @param DateTimeZone $tz Timezone to be used in calculations
   * @return array Array of date ranges
   */
  private static function _calculateRangedDates(DateTimeZone $tz) {
    $ranges = array(
      self::TODAY       => array(),
      self::YESTERDAY   => array(),
      self::MTD         => array(),
      self::YTD         => array(),
      self::LAST_7_DAYS => array(),
      self::LAST_MTD    => array(),
      self::LAST_MONTH  => array()
    );

    foreach(array_keys($ranges) as $key) {
      $start = new DateTime('now', $tz);
      $start->setTime(0, 0, 0);
      $stop = new DateTime('now', $tz);
      $stop->setTime(23, 59, 59);
      switch($key) {
        case self::YESTERDAY:
          $start->sub(new DateInterval('P1D'));
          $stop->sub(new DateInterval('P1D'));
          break;
        case self::LAST_7_DAYS:
          $start->sub(new DateInterval('P6D'));
          break;
        case self::MTD:
          $start->setDate($start->format('Y'), $start->format('m'), 1);
          break;
        case self::LAST_MTD:
          $start->setDate($start->format('Y'), $start->format('m'), 1)->sub(new DateInterval('P1M'));
          $stop->sub(new DateInterval('P1M'));
          break;
        case self::LAST_MONTH:
          $start->setDate($start->format('Y'), $start->format('m'), 1)->sub(new DateInterval('P1M'));
          $stop->setDate($stop->format('Y'), $stop->format('m'), 1)->sub(new DateInterval('P1D'));
          break;
        case self::YTD:
          $start->setDate($start->format('Y'), 1, 1);
          break;
        case self::TODAY:
      }

      $ranges[$key] = array($start, $stop);
    }

    return $ranges;
  }

  /**
   * Return array of date range timestamps
   *
   * @param DateTimeZone Timezone to be used in calculations
   * @return array Array of date range timestamps
   */
  public function getRangedTimestamps(DateTimeZone $tz) {
    $gmt = new DateTimeZone('GMT');
    $timestamps = $this->getRangedDates();
    foreach(array_keys($timestamps) as $key) {
      #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__.':: '.$key.' :: '.$timestamps[$key][0].' - '.$timestamps[$key][1]);
      $s1 = clone $timestamps[$key][0];
      $s2 = clone $timestamps[$key][1];
      $s1->setTimeZone($gmt);
      $s2->setTimeZone($gmt);
      $timestamps[$key][0] = $timestamps[$key][0]->getTimestamp() + $tz->getOffset($s1);
      $timestamps[$key][1] = $timestamps[$key][1]->getTimestamp() + $tz->getOffset($s2);
    }
    return $timestamps;
  }
}

