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
 * @subpackage Session_SaveHandler
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
 */
namespace Rexmac\Zyndax\Session\SaveHandler;

use Rexmac\Zyndax\Service\SessionService,
    \Zend_Config,
    \Zend_Registry,
    \Zend_Session,
    \Zend_Session_SaveHandler_Exception as SaveHandlerException;

/**
 * Custom session_save_handler that uses Doctrine ORM to store session data
 * to database.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Session_SaveHandler
 * @copyright  Copyright (c) 2011-2012 Rex McConnell (http://rexmac.com/)
 * @license    http://rexmac.com/license/bsd2c.txt Modified BSD License
 * @author     Rex McConnell <rex@rexmac.com>
*/
class DoctrineSaveHandler implements \Zend_Session_SaveHandler_Interface {

  /**
   * Doctrine entity class that represents a PHP session
   *
   * @var string
   */
  private $entityClass = 'Rexmac\Zyndax\Entity\Session';

  /**
   * Session lifetime
   *
   * @var integer
   */
  private $lifetime = null;

  /**
   * Constructor
   *
   * @param mixed $config
   * @return void
   */
  public function __construct($config) {
    $this->lifetime = ini_get('session.gc_maxlifetime');

    if($config instanceof Zend_Config) {
      $config = $config->toArray();
    } elseif(!is_array($config)) {
      throw new SaveHandlerException(
        '$config must be an instance of Zend_Config or array of key/value pairs containing '.
        'configuration options for '.get_class().'.'
      );
    }

    foreach($config as $key => $value) {
      if($key == 'entityClass') {
        $this->entityClass = $value;
      }
    }
  }

  /**
   * Destructor
   *
   * @return void
   */
  public function __destruct() {
    Zend_Session::writeClose();
  }

  /**
   * Open Session - retrieve resources
   *
   * @codeCoverageIgnore
   * @param string $save_path Session save path
   * @param string $name Session name
   * @return bool
   */
  public function open($save_path, $name) {
    // Force use of main domain, i.e. no sub-domain, for session cookies
    ini_set('session.cookie_domain', str_replace('admin.', '.', Zend_Registry::get('siteDomain')));
    $this->_sessionSavePath = $save_path;
    $this->_sessionName     = $name;
    return true;
  }

  /**
   * Close Session - free resources
   *
   * @codeCoverageIgnore
   * @return bool
   */
  public function close() {
    return true;
  }

  /**
   * Read session data
   *
   * @param string $id
   * @return string
   */
  public function read($id) {
    $data = '';
    if(null !== ($session = SessionService::find($id))) {
      if((int)($session->getModified() + $session->getLifetime()) > time()) {
        $data = $session->getData();
      } else {
        return $this->destroy($id);
      }
    }

    return $data;
  }

  /**
   * Write Session - commit data to resource
   *
   * @param string $id
   * @param string $data
   * @return bool
   */
  public function write($id, $data) {
    $data = (string) $data;
    if(null !== ($session = SessionService::find($id))) {
      $session->setModified(time())->setData($data);
      SessionService::update();
      return true;
    }
    SessionService::create(new $this->entityClass(array(
      'id'       => $id,
      'modified' => time(),
      'lifetime' => $this->lifetime,
      'data'     => $data,
    )));
    return true;
  }

  /**
   * Destroy Session - remove data from resource for given session ID
   *
   * @param string $id Session ID
   * @return bool
   */
  public function destroy($id) {
    if(null !== ($session = SessionService::find($id))) {
      SessionService::delete($session);
      return true;
    }
    return false;
  }

  /**
   * Garbage Collection - remove session data older than $maxlifetime
   *
   * @param  int $maxlifetime Lifetime (in seconds)
   * @return bool
   */
  public function gc($maxlifetime) {
    SessionService::collectGarbage($maxlifetime);
    return true;
  }

  /**
   * Set lifetime
   *
   * @param int $lifetime Lifetime (in seconds)
   * @return Rexmac\Zyndax\Session\SaveHandler\DoctrineSaveHandler Provides fluent interface
   */
  public function setLifetime($lifetime) {
    $this->lifetime = $lifetime;
  }

  /**
   * Get lifetime
   *
   * @return int Lifetime (in seconds)
   */
  public function getLifetime() {
    return $this->lifetime;
  }
}
