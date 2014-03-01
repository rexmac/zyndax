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
 * @subpackage Application_Module_Admin_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */

use \DateTime,
    \DateTimeZone,
    \Exception,
    Rexmac\Zyndax\Log\Logger,
    Rexmac\Zyndax\Entity\User,
    Rexmac\Zyndax\Service\AclRoleService,
    Rexmac\Zyndax\Service\TimeZoneService,
    Rexmac\Zyndax\Service\UserEditEventService,
    Rexmac\Zyndax\Service\UserLoginAsEventService,
    Rexmac\Zyndax\Service\UserProfileService,
    Rexmac\Zyndax\Service\UserService,
    Rexmac\Zyndax\View\Helper\Jquery as JqueryViewHelper,
    \Zend_Auth,
    \Zend_Paginator,
    \Zend_Registry,
    \Zend_Session,
    \Zend_Session_Namespace;

/**
 * User Administration Controller
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_UsersController extends \Rexmac\Zyndax\Controller\Action {

  /**
   * Initialization
   *
   * Initializes ajax contexts
   *
   * @return void
   */
  public function init() {
    parent::init();
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
      ->addActionContext('index', 'html')
      ->addActionContext('edit', 'json')
      ->initContext();
  }

  /**
   * Index action
   *
   * @return void
   */
  public function indexAction() {
    $timeZone = new DateTimeZone($this->_user->getProfile()->getTimeZone()->getName());
    $users = UserService::find();
    uasort($users, function($a, $b) {
      $a = strtolower($a->getUsername());
      $b = strtolower($b->getUsername());
      if($a == $b) return 0;
      return ($a < $b) ? -1 : 1;
    });

    $this->view->users = $users;
    $this->view->timeZone = $timeZone;
    if(Zend_Registry::get('acl')->isUserAllowed('mvc:admin:users:create', 'view')) {
      $this->view->contextLinks = '<a class="addUserLink zp-button" href="' . $this->view->url(array(), 'adminUserCreate') . '" title="Create User">+ Add</a>';
    }

    JqueryViewHelper::assignData(array(
      'userMayEdit' => Zend_Registry::get('acl')->isUserAllowed('mvc:admin:users:create', 'view')
    ));
  } // end indexAction

  /**
   * Create action
   *
   * @return void
   */
  public function createAction() {
    $request = $this->getRequest();
    $form    = new \Admin_Form_UserCreate();

    if($request->isPost()) {
      if($form->isValid($request->getParams())) {
        $data = $form->getValues();

        try {
          $user = UserService::create(array(
            'username'    => $data['username'],
            'email'       => $data['email'],
            'password'    => UserService::encryptPassword($data['password']),
            'role'        => AclRoleService::find($data['role']),
            'dateCreated' => new DateTime(),
            'active'      => true,
            'locked'      => false,
          ));

          UserProfileService::create(array(
            'user'      => $user,
            'firstName' => $data['firstName'],
            'lastName'  => $data['lastName'],
            'phone'     => $data['phone'],
            'website'   => $data['website'],
            'timeZone'  => TimeZoneService::findOneByName('America/Los_Angeles'),
          ));

          UserEditEventService::create(array(
            'user'        => $user,
            'editor'      => $this->_user,
            'ip'          => $this->getRequest()->getServer('REMOTE_ADDR'),
            'date'        => new DateTime(),
            'description' => 'Creation',
          ));

          $this->view->success = 1;
          $this->_helper->sessionMessenger('User created successfully.', 'success');
          return $this->_helper->getHelper('Redirector')->gotoRoute(array(), 'adminUsers');
        } catch(Exception $e) {
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: AUCCA001');
          $this->view->messages()->addMessage($message, 'error');
          Logger::err($e->getMessage());
        }
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    }

    $this->view->form = $form;
  } // end createAction

  /**
   * Edit action
   *
   * @return void
   */
  public function editAction() {
    $request = $this->getRequest();
    $timeZone  = new DateTimeZone($this->_user->getProfile()->getTimeZone()->getName());
    $params  = $request->getParams();

    // Get the user that is being updated
    $userId = $request->getParam('userId', null);
    if(null === ($user = UserService::findOneById($userId))) {
      throw new \Admin_UsersControllerException('No user found.');
    }

    $form = new \Admin_Form_UserEdit();
    $form->setDefaultsFromUser($user);

    // Pre-validate form; set conditional requirements
    $form->preValidate($request->getParams());

    if($request->isPost()) {
      // AJAX request with partial form submission? (probably an AJAX request to update a single property)
      if($request->isXmlHttpRequest() && $form->isValidPartial($params)) {
        #Logger::debug(__METHOD__.':: params-pre = '.var_export($params, true));
        $params = array_merge($params, $this->_prepFormData($form->getValues(), $user));
        #Logger::debug(__METHOD__.':: params-post = '.var_export($params, true));
      }

      if($form->isValid($params)) {
        $data = $form->getValues();
        #Logger::debug(__METHOD__.':: data = '.var_export($data, true));
        try {
          $changed = $this->_updateUser($user, $data);

          $this->view->success = 1;
          $this->view->userId = $userId;
          if($changed) {
            $message = 'User modified successfully.';
            $msgPriority = 'success';
          } else {
            $message = 'No changes were made.';
            $msgPriority = 'notice';
          }
          if(!$request->isXmlHttpRequest()) {
            $this->_helper->sessionMessenger($message, $msgPriority);
            return $this->_helper->getHelper('Redirector')->gotoRoute(array(), 'adminUsers');
          }
          $this->view->messages()->addMessage($message, $msgPriority);
        } catch(Exception $e) {
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: AUCEA001');
          $this->view->messages()->addMessage($message, 'error');
          Logger::err(__METHOD__.'::'.$e->getMessage());
        }
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
        Logger::debug(__METHOD__.':: formErrors = '.var_export($form->getMessages(), true));
        if($request->isXmlHttpRequest()) {
          $this->view->messages()->addMessage('Application error: AUCEA000', 'error');
          #$this->view->messages()->addMessage($form->getMessages(), 'error');
        }
      }
    } else { // Not a POST request
      $form->setDefaultsFromUser($user);
    }

    $this->view->assign(array(
      'editHistory' => UserEditEventService::findByUser($user->getId()),
      'timeZone'    => $timeZone,
      'form'        => $form,
      'userId'      => $userId,
    ));
    JqueryViewHelper::assignData(array(
      'userId' => $userId
    ));
  } // end editAction

  /**
   * Login-as action
   *
   * @return void
   */
  public function loginasAction() {
    $request = $this->getRequest();

    if(null === ($user = UserService::findOneById($request->getParam('userId')))) {
      throw new Exception('Invalid user ID');
    }

    // Need to avoid admin users
    if($user->isAdmin()) {
      #throw new Exception('Invalid user ID');
      throw new Exception('Cannot login-as admin user');
    }

    // "Authenticate" as user
    $authCookieName = Zend_Registry::get('config')->session->auth->name;
    $ssa = new Zend_Session_Namespace($authCookieName);
    $ssa->loginAsUser = $user->getId();

    // Track login-as event
    UserLoginAsEventService::create(array(
      'user'    => $this->_user,
      'account' => $user,
      'date'    => new DateTime(),
      'ip'      => $this->getRequest()->getServer('REMOTE_ADDR')
    ));

    $siteDomain = str_replace('admin.', '', Zend_Registry::get('siteDomain'));
    return $this->_helper->getHelper('Redirector')->gotoUrl('http://' . $siteDomain . '/home');
  } // close loginAsAction

  /**
   * Prepare form data for processing
   *
   * @param array $data Form data
   * @param User $user
   * @return array
   */
  private function _prepFormData(array $data, User $user) {
    #Logger::debug(__METHOD__.':: data ='.var_export($data, true));
    $profile = $user->getProfile();
    foreach($data as $key => $value) {
      if($value === '' || $value === null) {
        if(in_array($key, array('firstName', 'lastName', 'phone', 'website', 'timeZone'))) {
          $data[$key] = $profile->{'get'.ucfirst($key)}();
          if($key === 'timeZone') {
            $data[$key] = $data[$key]->getId();
          }
        } else {
          $data[$key] = $user->{'get'.ucfirst($key)}();
          if($key === 'role') {
            $data[$key] = $data[$key]->getId();
          }
        }
      }
    }
    return $data;
  }

  /**
   * Update User entity
   *
   * @param User $user
   * @param array $data
   * @return void
   */
  private function _updateUser(User $user, array $data) {
    if(isset($data['newPassword']) && '' != $data['newPassword']) {
      // Verify old password
      #if(!UserService::verifyPassword($this->_user, $data['password'])) {
      #  throw new Exception('Current password is invalid');
      #}
      $data['password'] = UserService::encryptPassword($data['newPassword']);
    } else {
      $data['password'] = $user->getPassword();
    }
    unset($data['newPassword']);
    unset($data['newPasswordConfirm']);

    if(isset($data['role'])) {
      $data['role'] = AclRoleService::findOneById($data['role']);
    }
    if(isset($data['timeZone'])) {
      $data['timeZone'] = TimeZoneService::findOneById($data['timeZone']);
    }

    // Track changes
    $changes = array();
    foreach($data as $key => $newValue) {
      if($key === 'userId') continue;
      $oldValue = $user->{'get'.ucfirst($key)}();
      Logger::debug(__METHOD__.":: $key");
      Logger::debug(__METHOD__.":: OLD => ".(is_object($oldValue) ? get_class($oldValue) : var_export($oldValue, true)));
      Logger::debug(__METHOD__.":: NEW => ".(is_object($newValue) ? get_class($newValue) : var_export($newValue, true)));
      // Only update changed properties, and keep track of the changes as well
      if($this->_valueChanged($oldValue, $newValue)) {
        Logger::debug(__METHOD__.":: $key has changed");
        Logger::debug(__METHOD__.":: OLD => ".(is_object($oldValue) ? get_class($oldValue) : var_export($oldValue, true)));
        Logger::debug(__METHOD__.":: NEW => ".(is_object($newValue) ? get_class($newValue) : var_export($newValue, true)));
        $oldVal = $oldValue;
        $newVal = $newValue;
        if(is_object($newValue)) {
          if(isset($oldValue)) $oldVal = $oldValue->getName();
          else $oldVal = '';
          $newVal = $newValue->getName();
        } elseif(is_object($oldValue)) {
          $oldVal = $oldValue->getName();
        }
        $changes[] = array(
          'item'     => $key,
          'oldValue' => $oldVal,
          'newValue' => $newVal
        );
        // Set new value
        $user->{'set'.ucfirst($key)}($newValue);
      }
    }
    UserService::update();

    // Any changes to record?
    if(count($changes) > 0) {
      $description = '';
      foreach($changes as $change) {
        $description .= sprintf('%s changed from "%s" to "%s".',
          $change['item'],
          $change['oldValue'] === 0 ? '0' : $change['oldValue'],
          $change['newValue']
        ) . PHP_EOL;
      }
      UserEditEventService::create(array(
        'user'        => $user,
        'editor'      => $this->_user,
        'ip'          => $this->getRequest()->getServer('REMOTE_ADDR'),
        'date'        => new DateTime(),
        'description' => rtrim($description),
      ));

      return true;
    }

    return false;
  }

  /**
   * Return TRUE if values are equal
   *
   * @param mixed $oldValue
   * @param mixed $newValue
   * @return bool
   */
  private function _valueChanged($oldValue, $newValue) {
    if(is_object($newValue)) {
      if($newValue instanceof DateTime && $oldValue instanceof DateTime) {
        if($oldValue->format('Y-m-d') != $newValue->format('Y-m-d')) return true;
        else return false;
      } else {
        return $oldValue !== $newValue;
      }
    }

    return $oldValue != $newValue;
  }
}


/**
 * Admin users controller exception class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Controller_Exception
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_UsersControllerException extends Exception{
}
