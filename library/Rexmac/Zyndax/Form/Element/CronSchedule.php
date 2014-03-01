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

/**
 * Form element that provides fields for scheduling a cronjob.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class CronSchedule extends \Zend_Form_Element_Xhtml {

  /**
   * Minute field
   *
   * @var string
   */
  protected $_minute = '*';

  /**
   * Hour field
   *
   * @var string
   */
  protected $_hour = '*';

  /**
   * Day field
   *
   * @var string
   */
  protected $_day = '*';

  /**
   * Month field
   *
   * @var string
   */
  protected $_month = '*';

  /**
   * Day of week field
   *
   * @var string
   */
  protected $_weekday = '*';


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
    parent::__construct($spec, $options);
  }

  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators() {
    if($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if(empty($decorators)) {
      $this->addDecorator('CronSchedule')
           ->addDecorator('Errors')
           ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
           ->addDecorator('HtmlTag', array('tag' => 'dd', 'id' => $this->getName().'-element'))
           ->addDecorator('Label', array('tag' => 'dt'));
    }
  }

  /**
   * Getter for $_minute
   *
   * @return string
   */
  public function getMinute() {
    return $this->_minute;
  }

  /**
   * Getter for $_hour
   *
   * @return string
   */
  public function getHour() {
    return $this->_hour;
  }

  /**
   * Getter for $_day
   *
   * @return string
   */
  public function getDay() {
    return $this->_day;
  }

  /**
   * Getter for $_month
   *
   * @return string
   */
  public function getMonth() {
    return $this->_month;
  }

  /**
   * Getter for $_weekday
   *
   * @return string
   */
  public function getWeekday() {
    return $this->_weekday;
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
   * Setter for $_minute
   *
   * @param string $minute
   * @return Rexmac\Zyndax\Form\Element\CronSchedule Fluent-interface
   */
  public function setMinute($minute) {
    $this->_minute = $minute;
    return $this;
  }

  /**
   * Setter for $_hour
   *
   * @param string $hour
   * @return Rexmac\Zyndax\Form\Element\CronSchedule Fluent-interface
   */
  public function setHour($hour) {
    $this->_hour = $hour;
    return $this;
  }

  /**
   * Setter for $_day
   *
   * @param string $day
   * @return Rexmac\Zyndax\Form\Element\CronSchedule Fluent-interface
   */
  public function setDay($day) {
    $this->_day = $day;
    return $this;
  }

  /**
   * Setter for $_month
   *
   * @param string $month
   * @return Rexmac\Zyndax\Form\Element\CronSchedule Fluent-interface
   */
  public function setMonth($month) {
    $this->_month = $month;
    return $this;
  }

  /**
   * Setter for $_weekday
   *
   * @param string $weekday
   * @return Rexmac\Zyndax\Form\Element\CronSchedule Fluent-interface
   */
  public function setWeekday($weekday) {
    $this->_weekday = $weekday;
    return $this;
  }

  /**
   * Set element's value
   *
   * @param mixed $value
   * @return Rexmac\Zyndax\Form\Element\CronSchedule Fluent-interface
   */
  public function setValue($value) {
    #\Rexmac\Zyndax\Log\Logger::debug(__METHOD__."::\$value=".var_export($value, true));
    if(is_array($value)) {
      if(isset($value['minute']) && '' !== $value['minute']) {
        $this->setMinute($value['minute']);
        unset($value['minute']);
      }
      if(isset($value['hour']) && '' !== $value['hour']) {
        $this->setHour($value['hour']);
        unset($value['hour']);
      }
      if(isset($value['day']) && '' !== $value['day']) {
        $this->setDay($value['day']);
        unset($value['day']);
      }
      if(isset($value['month']) && '' !== $value['month']) {
        $this->setMonth($value['month']);
        unset($value['month']);
      }
      if(isset($value['weekday']) && '' !== $value['weekday']) {
        $this->setWeekday($value['weekday']);
        unset($value['weekday']);
      }
      if(!empty($value)) {
        throw new Exception('Invalid schedule provided');
      }
    } else {
      throw new Exception('Invalid schedule provided');
    }

    return $this;
  }
}
