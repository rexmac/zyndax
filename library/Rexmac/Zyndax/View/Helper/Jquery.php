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
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */

namespace Rexmac\Zyndax\View\Helper;

use \Zend_Controller_Front as FrontController,
    \Zend_Loader_PluginLoader_Exception,
    \Zend_View_Exception,
    \Zend_View_Interface;

/**
 * Helper class for including jQuery scripts in views scripts
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Jquery extends \Rexmac\Zyndax\View\Helper\Jquery\HelperAbstract {

  /**
   * Contains references to proxied helpers
   *
   * @var array
   */
  protected $_helpers = array();

  /**
   * Has this helper rendered yet?
   *
   * @var boolean
   */
  private $_hasRendered = false;

  /**
   * Overrides setView defined in {@link Zend_View_Helper_Abstract}
   *
   * @param  Zend_View_Interface $view
   * @return Zend_View_Helper_Abstract
   */
  public function setView(Zend_View_Interface $view) {
    $this->view = $view;
    $this->view->headLink()->appendStylesheet('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/cupertino/jquery-ui.css');
    $this->view->headScript()->appendFile('/js/libs/modernizr-2.5.3.min.js');

    return $this;
  }

  /**
   * Strategy pattern
   *
   * @return Rexmac\View\Helper\Jquery
   */
  public function direct() {
    return $this;
  }

  /**
   * Magic overload: Proxy to other navigation helpers or the container
   *
   * @param  string Helper name or method name in container
   * @param  array  [optional] arguments to pass
   * @return mixed  What the proxied call returns
   * @throws Zend_View_Exception if proxying to a helper, and the helper is
   *                             not an instance of the interface specified
   *                             in {@link findHelper()}
   */
  public function __call($method, array $arguments = array()) {
    // check if call should proxy to another helper
    if($helper = $this->findHelper($method, false)) {
      if(method_exists($helper, $method)) {
        return call_user_func_array(array($helper, $method), $arguments);
      } else {
        return call_user_func_array(array($helper, 'direct'), $arguments);
      }
    }
    // default behaviour: proxy call to container
    #return parent::__call($method, $arguments);
  }

  /**
   * Returns the helper matching $proxy
   *
   * @param string Helper name
   * @param bool   [optional] Whether exceptions should be thrown if something
   *                          goes wrong. Default is true.
   * @return Rexmac\View\Helper\Jquery\Helper Helper instance
   * @throws Zend_Loader_PluginLoader_Exception  if $strict is true and helper
   *                                             cannot be found
   * @throws Zend_View_Exception                 if $strict is true and helper
   *                                             does not implement the
   *                                             specified interface
   */
  public function findHelper($proxy, $strict = true) {
    if(isset($this->_helpers[$proxy])) {
      return $this->_helpers[$proxy];
    }

    if(!$this->view->getPluginLoader('helper')->getPaths(__NAMESPACE__)) {
      $this->view->addHelperPath(str_replace('\\', '/', __NAMESPACE__), __NAMESPACE__);
    }

    if($strict) {
      $helper = $this->view->getHelper($proxy);
    } else {
      try {
        $helper = $this->view->getHelper($proxy);
      } catch(Zend_Loader_PluginLoader_Exception $e) {
        return null;
      }
    }

    if(!$helper instanceof \Rexmac\Zyndax\View\Helper\Jquery\Helper) {
      if($strict) {
        $e = new Zend_View_Exception(sprintf(
          'Proxy helper "%s" is not an instance of \Rexmac\Zyndax\View\Helper\Jquery\Helper',
          get_class($helper)
        ));
        $e->setView($this->view);
        throw $e;
      }
      return null;
    }

    #$this->_inject($helper);
    $this->_helpers[$proxy] = $helper;

    return $helper;
  }

  /**
   * Renders helper
   *
   * @return string Helper output
   * @throws Zend_Loader_PluginLoader_Exception if helper cannot be found
   * @throws Zend_View_Exception                if helper doesn't implement
   *                                            the interface specified in
   *                                            {@link findHelper()}
   */
  public function render() {
    if('testing' !== APPLICATION_ENV && $this->_hasRendered) return;
    #$helper = $this->findHelper($this->getDefaultProxy());
    #return $helper->render($container);
    #$request = FrontController::getInstance()->getRequest();
    #$module  = $request->getModuleName();
    #$action  = $request->getActionName();
    #$uri = ('default' == $module ? '' : $module.'/').$request->getControllerName().('index' == $action ? '' : '/'.$action);
    $files = self::getFiles();

/*
    $this->view->inlineScript()
      ->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
      ->appendFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');

    foreach($files as $file) {
      $this->view->inlineScript()->appendFile($file);
    }
*/

    $out  = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>';
    #$out .= '<script src="/js/libs/jquery.dataTables.js"></script>';

    foreach($files as $file) {
      $out .= "<script src=\"{$file}\"></script>\n";
    }

    $out .= '
<script>
$(function(){
' . parent::getScript() . "

  $('#ui-datepicker-div').removeClass('ui-helper-hidden-accessible'); // should not be necessary?
  });
";
/*
    if(isset($this->view->csrf)) {
      #$out .= '  var csrf = \''.$this->view->csrf."';\n";
    }

*/
    $out .= '</script>';
    $this->_hasRendered = true;
    return $out;
  }
}
