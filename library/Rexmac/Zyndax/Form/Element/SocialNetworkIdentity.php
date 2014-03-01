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

use \InvalidArgumentException;

/**
 * Form element that provides text input and select input to allow user to
 * input IM names or other social contact info.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class SocialNetworkIdentity extends \Zend_Form_Element_Xhtml {

  /**
   * Social network identity name
   *
   * @var string
   */
  private $_identityName;

  /**
   * Social network ID
   *
   * @var int
   */
  private $_network;

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
   * @return Rexmac\Zyndax\Form\Element\SocialNetworkIdentity
   */
  public function loadDefaultDecorators() {
    if($this->loadDefaultDecoratorsIsDisabled()) return $this;

    $decorators = $this->getDecorators();
    if(empty($decorators)) {
      $this->addDecorator('SocialNetworkIdentity')
        ->addDecorator('Errors')
        ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
        ->addDecorator('HtmlTag', array('tag' => 'dd', 'id' => $this->getName().'-element'))
        ->addDecorator('Label', array('tag' => 'dt'));
    }
    return $this;
  }

  /**
   * Get name of identity
   *
   * @return string
   */
  public function getIdentityName() {
    return $this->_identityName;
  }

  /**
   * Get network id
   *
   * @return int
   */
  public function getNetwork() {
    return $this->_network;
  }

  /**
   * Get form element's value
   *
   * @return Rexmac\Zyndax\Form\Element\SocialNetworkIdentity Provides fluent interface
   */
  public function getValue() {
    return $this;
  }

  /**
   * Return true if empty
   *
   * @return bool
   */
  public function isEmpty() {
    return !isset($this->_identityName) || empty($this->_identityName) || !isset($this->_network) || $this->_network === 0;
  }

  /**
   * Set form element's value
   *
   * @param array $value
   * @return Rexmac\Zyndax\Form\Element\SocialNetworkIdentity Provides fluent interface
   */
  public function setValue($value = null) {
    if(is_array($value) && (isset($value['name']) && isset($value['network']))) {
      $this->_identityName  = $value['name'];
      $this->_network       = $value['network'];
    } elseif($value !== null) {
      throw new InvalidArgumentException('Invalid value provided');
    }
    return $this;
  }
}
