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

/**
 * Index controller
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class IndexController extends \Rexmac\Zyndax\Controller\Action {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
  }

  /**
   * Index action
   *
   * @return void
   */
  public function indexAction() {
    if(Zend_Registry::get('acl')->isUserAllowed('mvc:admin', 'view')) {
      return $this->_forward('home', 'index', 'admin');
    } else {
      return $this->_forward('home');
    }
  }

  /**
   * Home action
   *
   * @return void
   */
  public function homeAction() {
  }
}
