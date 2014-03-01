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
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Form\Decorator;

/**
 * Displays form elements for scheduling a cronjob.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class CronSchedule extends \Zend_Form_Decorator_Abstract {

  /**
   * Render CronSchedule form element
   *
   * @param string $content
   * @return string
   */
  public function render($content) {
    $element = $this->getElement();
    if(!$element instanceof \Rexmac\Zyndax\Form\Element\CronSchedule) {
      return $content;
    }

    $view = $element->getView();
    if(!$view instanceof \Zend_View_Interface) {
      return $content;
    }

    $name = $element->getFullyQualifiedName();

    #$tableStyle = $this->getOption('tableStyle');
    #if(null === $tableStyle) $tableStyle = false;

    $id = $element->getId();
    $class = $element->isRequired() ? 'required' : 'optional';
    $labelAppendee = $element->isRequired() ? '*' : '&nbsp;';

    $options = array(
      'minute'  => array('*' => 'every minute'),
      'hour'    => array('*' => 'every hour'),
      'day'     => array('*' => 'every day'),
      'month'   => array('*' => 'every month'),
      'weekday' => array(
        '*' => 'any weekday',
        0   => 'Sunday',
        1   => 'Monday',
        2   => 'Tuesday',
        3   => 'Wednesday',
        4   => 'Thursday',
        5   => 'Friday',
        6   => 'Saturday'
      )
    );
    // Minute options
    for($i = 5; $i < 60; $i += 5) {
      $options['minute']["*/$i"] = "every $i minutes";
    }
    for($i = 0; $i < 60; ++$i) {
      $options['minute']["$i"] = "$i";
    }
    // Hour options
    for($i = 2; $i < 14; $i += 2) {
      $options['hour']["*/$i"] = "every $i hours";
    }
    for($i = 0; $i < 24; ++$i) {
      $options['hour']["$i"] = "$i";
    }
    // Day options
    for($i = 1; $i < 32; ++$i) {
      $options['day']["$i"] = "$i";
    }
    // Month options
    for($i = 1; $i < 13; ++$i) {
      $options['month']["$i"] = "$i";
    }

    $markup = '<table cellpadding="0" cellspacing="0" class="list striped cronSchedule"><thead><tr><th>'
      . $view->formLabel(
        $name . '[minute]',
        'Minute' . $labelAppendee,
        array('id' => $id . '-minute', 'class' => $class, 'escape' => false)
      ) . '</th><th>'
      . $view->formLabel(
        $name . '[hour]',
        'Hour' . $labelAppendee,
        array('id' => $id . '-hour', 'class' => $class, 'escape' => false)
      ) . '</th><th>'
      . $view->formLabel(
        $name . '[day]',
        'Day' . $labelAppendee,
        array('id' => $id . '-day', 'class' => $class, 'escape' => false)
      ) . '</th><th>'
      . $view->formLabel(
        $name . '[month]',
        'Month' . $labelAppendee,
        array('id' => $id . '-month', 'class' => $class, 'escape' => false)
      ) . '</th><th>'
      . $view->formLabel(
        $name . '[weekday]',
        'Day of Week' . $labelAppendee,
        array('id' => $id . '-weekday', 'class' => $class, 'escape' => false)
      ) . '</th></tr></thead><tbody><tr><td>'
      . $view->formSelect($name . '[minute]', $element->getMinute(), array('id' => $id . '-minute'), $options['minute']) . '</td><td>'
      . $view->formSelect($name . '[hour]', $element->getHour(), array('id' => $id . '-hour'), $options['hour']) . '</td><td>'
      . $view->formSelect($name . '[day]', $element->getDay(), array('id' => $id . '-day'), $options['day']) . '</td><td>'
      . $view->formSelect($name . '[month]', $element->getMonth(), array('id' => $id . '-month'), $options['month']) . '</td><td>'
      . $view->formSelect($name . '[weekday]', $element->getWeekday(), array('id' => $id . '-weekday'), $options['weekday'])
      . '</td></tr></tbody></table>';

    switch($this->getPlacement()) {
      case self::PREPEND:
        return $markup . $this->getSeparator() . $content;
      case self::APPEND:
      default:
        return $content . $this->getSeparator() . $markup;
    }
  }
}
