<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * Zyndax is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zyndax is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zyndax.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */
use \ApiControllerException,
    Rexmac\Zyndax\Log\Logger,
    \Zend_Controller_Plugin_ErrorHandler as ErrorHandler,
    \Zend_Registry;

/**
 * Error controller
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class ErrorController extends \Zend_Controller_Action {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    $this->_helper->contextSwitch()
      ->addContext('html', array())
      ->addActionContext('error', array('json', 'html', 'xml'))
      ->initContext();

    $acl  = Zend_Registry::get('acl');
    if($acl !== null && $acl->isUserAllowed('mvc:admin:all', 'view')) {
      Zend_Layout::getMvcInstance()->setLayoutPath(realpath(APPLICATION_PATH.'/modules/admin/layouts'));
    }
  }

  /**
   * Pre-dispatch
   *
   * @return void
   */
  public function preDispatch() {
    if('xml' === $this->getRequest()->getParam('format')) {
      $this->view->xmlWrapperTag = mb_strtolower(sanitize_string_for_xml_tag(Zend_Registry::get('siteName'))) . 'Response';
    }
  }

  /**
   * Post-dispatch
   *
   * @return void
   */
  public function postDispatch() {
    unset($this->view->now);
    if('xml' === $this->getRequest()->getParam('format')) {
      $this->render();
      $response = $this->getResponse();
      $response->setBody(rtrim(preg_replace('/>\s+</', '><', $response->getBody())));
    }
  }

  /**
   * Index action
   *
   * @return void
   */
  public function indexAction() {
    $this->_forward('error');
  }

  /**
   * Error action
   *
   * @return void
   */
  public function errorAction() {
    $errors = $this->_getParam('error_handler');
    switch ($errors->type) {
      case ErrorHandler::EXCEPTION_NO_ROUTE:
      case ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case ErrorHandler::EXCEPTION_NO_ACTION:
        // 404 error -- controller or action not found
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->message = 404;
        break;
      default:
        // application error
        $this->getResponse()->setHttpResponseCode(500);
        if($errors->exception instanceof ApiControllerException) {
          $this->view->message = $errors->exception->getMessage();
        } elseif($errors->exception) {
          $this->view->message = 'Application error: '.$errors->exception->getMessage();
        } else {
          $this->view->message = 'Application error: Unknown error';
        }
    }

    // Log exception, if logger available
    Logger::crit(__METHOD__.':: '.$this->view->message.' - '.$errors->exception);

    // Conditionally display exceptions
    if($this->getInvokeArg('displayExceptions') == true) {
      $this->view->exception = $errors->exception;
    }

    if(null === $this->getRequest()->getParam('format')) {
      $this->view->request = $errors->request;
    }

    if($this->view->message === 404) {
      $this->view->pageTitle(' - Page not found');
    } else {
      $this->view->pageTitle(' - An error occurred');
    }

    $this->getRequest()->setParams(array(
      'controller' => 'error',
      'action'     => 'error'
    ));
  }

  /**
   * Forbidden action
   *
   * @return void
   */
  public function forbiddenAction() {
  }
}
