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

use Rexmac\Zyndax\Form\Element\DateRange as DateRangeElement,
    \Zend_View_Interface;

/**
 * Displays form elements for selecting a date range.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class DateRange extends \Zend_Form_Decorator_Abstract {

  /**
   * Render date range element
   *
   * @param string $content
   * @return string
   */
  public function render($content) {
    $element = $this->getElement();
    if(!$element instanceof DateRangeElement) {
      return $content;
    }

    $view = $element->getView();
    if(!$view instanceof Zend_View_Interface) {
      return $content;
    }

    $name = $element->getFullyQualifiedName();

    $showSelect = $this->getOption('showSelect');
    if(null === $showSelect) $showSelect = true;

    $tableStyle = $this->getOption('tableStyle');
    if(null === $tableStyle) $tableStyle = false;

    $options = array(
      DateRangeElement::TODAY       => 'Today',
      DateRangeElement::YESTERDAY   => 'Yesterday',
      DateRangeElement::MTD         => 'Month-to-date',
      DateRangeElement::LAST_7_DAYS => 'Last 7 days',
      DateRangeElement::LAST_MTD    => 'Last MTD',
      DateRangeElement::LAST_MONTH  => 'Last month',
      DateRangeElement::YTD         => 'Year-to-date',
      DateRangeElement::CUSTOM      => 'Custom',
    );

    $id = $element->getId();
    $class = $element->isRequired() ? 'required' : 'optional';
    $labelAppendee = $element->isRequired() ? '&nbsp;*' : '&nbsp;&nbsp;';
    $startId = $id.'-start';
    $stopId  = $id.'-stop';
    $rangeId = $id.'-range';

    $startLabel = $view->formLabel(
      $name . '[start]',
      "From:<span class=\"{$class}\">{$labelAppendee}</span>",
      array('id' => $startId, 'class' => $class, 'escape' => false)
    );
    $startInput = $view->formText(
      $name . '[start]',
      $element->getStartDateString(),
      array('id' => $startId, 'size' => 12, 'class' => 'beside', 'readonly' => 'true')
    );
    $stopLabel = $view->formLabel(
      $name . '[stop]',
      "To:<span class=\"{$class}\">{$labelAppendee}</span>",
      array('id' => $stopId, 'class' => $class . ' beside l1', 'escape' => false)
    );
    $stopInput = $view->formText(
      $name . '[stop]',
      $element->getStopDateString(),
      array('id' => $stopId, 'size' => 12, 'class' => 'beside', 'readonly' => 'true')
    );

    if(!$tableStyle) {
      $markup = "<ol class=\"compact\"><li>\n{$startLabel}\n{$startInput}\n{$stopLabel}\n{$stopInput}\n</li>\n";
      if($showSelect) {
        $markup .= "<li class=\"rangeSelect\">\n"
          . $view->formLabel($name . '[range]', "Range:<span class=\"{$class}\">{$labelAppendee}</span>", array('id' => $rangeId, 'class' => $class, 'escape' => false)) . "\n"
          . $view->formSelect($name . '[range]', $element->getRange(), array('id' => $rangeId), $options) . "\n"
          . "</li>\n";
      }
      $markup .= "</ol>\n";
    } else {
      $markup = '<table cellpadding="0" cellspacing="0"><tbody><tr>'
        . "<td width=\"80\">{$startLabel}</td>"
        . "<td width=\"100\">{$startInput}</td>"
        . "<td width=\"40\">{$stopLabel}</td>"
        . "<td width=\"100\">{$stopInput}</td>"
        . '<td></td>'
        . '</tr>';
      if($showSelect) {
        $markup .= '<tr>'
          . '<td>' . $view->formLabel($name . '[range]', "Range:<span class=\"{$class}\">{$labelAppendee}</span>", array('id' => $rangeId, 'class' => $class)) . '</td>'
          . '<td>' . $view->formSelect($name . '[range]', $element->getRange(), array('id' => $rangeId), $options) . '</td>'
          . '<td colspan="3"></td>'
          . '</tr>';
      }
      $markup .= '</tbody></table>';
    }

    switch($this->getPlacement()) {
      case self::PREPEND:
        return $markup . $this->getSeparator() . $content;
      case self::APPEND:
      default:
        return $content . $this->getSeparator() . $markup;
    }
  }
}
