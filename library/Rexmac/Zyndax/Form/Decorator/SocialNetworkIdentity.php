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

use Rexmac\Zyndax\Service\SocialNetworkService,
    \Zend_View_Interface;

/**
 * Displays form element for inputting a social network identity
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class SocialNetworkIdentity extends \Zend_Form_Decorator_Abstract {

  /**
   * Render a social network identity
   *
   * @param  string $content
   * @return string
   */
  public function render($content) {
    $element = $this->getElement();
    if(!$element instanceof \Rexmac\Zyndax\Form\Element\SocialNetworkIdentity) {
      return $content;
    }

    $view = $element->getView();
    if(null === $view || !$view instanceof Zend_View_Interface) {
      return $content;
    }

    $name = $element->getFullyQualifiedName();

    $options = array();
    $networks = SocialNetworkService::find();
    foreach($networks as $network) {
      $options[$network->getId()] = array(
        'label' => $network->getName(),
        'class' => 'icon-'.strtolower($network->getAbbrev())
      );
    }

    $identityName = $element->getIdentityName();
    $network      = $element->getNetwork();

    $markup = $view->formText($name . '[name]', $identityName, array())
            . '&nbsp;&nbsp;'
            . $view->formSelect($name . '[network]', $network, array('class' => 'socialNetworkMenu'), $options);

    if($this->getOption('link')) {
      $markup .= '<a href="javascript:;" class="addSocialNetworkFieldLink" title="Add another social network identity">[Add another]</a><br>';
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
