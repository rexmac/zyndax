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
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */
use Rexmac\Zyndax\Acl\Service\GroupService;

/**
 * Display filters for managing groups
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Form
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_Form_GroupsFilters extends \Rexmac\Zyndax\Form\Form {

  /**
   * Initialization
   *
   * Initialize form for filters on "Groups" page
   *
   * @return void
   */
  public function init() {
    $this->setName('groups_filters_form');
    $this->setMethod('post');
    $this->setAttr('class', 'filters');

    $this->addElement('select', 'status', array(
      'required' => false,
      'label'    => 'Status:',
    ));
    $this->getElement('status')->addMultiOptions(array(
      'all'    => 'all ('.GroupService::getTotal().')',
      'locked' => 'locked ('.GroupService::getTotal('e.locked = 1').')',
    ));

    $this->setElementDecorators(array(
      'ViewHelper',
      'Label',
    ));

    $this->addElement('submit', 'apply', array(
      'required'   => false,
      'ignore'     => true,
      'label'      => 'Apply',
      'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('status', 'apply'), 'groups_filters', array(
      'legend'     => 'Filters',
      'decorators' => array(
        'FormElements',
        'Fieldset'
      )
    ));
  }
}
