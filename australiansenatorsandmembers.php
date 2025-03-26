<?php

require_once 'australiansenatorsandmembers.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function australiansenatorsandmembers_civicrm_config(&$config) {
  _australiansenatorsandmembers_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function australiansenatorsandmembers_civicrm_install() {
  _australiansenatorsandmembers_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function australiansenatorsandmembers_civicrm_enable() {
  _australiansenatorsandmembers_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function australiansenatorsandmembers_civicrm_managed(&$entities) {
  $entities[] = array(
    'module' => 'au.com.agileware.australiansenatorsandmembers',
    'name' => 'MP Contact Type',
    'entity' => 'ContactType',
    'params' => array(
      'version' => 3,
      'name' => 'MP',
      'parent_id' => 1,
    ),
  );
  $entities[] = array(
    'module' => 'au.com.agileware.australiansenatorsandmembers',
    'name' => 'Senator Contact Type',
    'entity' => 'ContactType',
    'params' => array(
      'version' => 3,
      'name' => 'Senator',
      'parent_id' => 1,
    ),
  );
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *
 */

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 */
/*function australiansenatorsandmembers_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
 */
/*function australiansenatorsandmembers_civicrm_navigationMenu(&$menu) {
  _australiansenatorsandmembers_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'au.com.agileware.australiansenatorsandmembers')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _australiansenatorsandmembers_civix_navigationMenu($menu);
} // */
