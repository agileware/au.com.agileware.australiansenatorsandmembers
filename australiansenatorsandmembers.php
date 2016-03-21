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
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function australiansenatorsandmembers_civicrm_xmlMenu(&$files) {
  _australiansenatorsandmembers_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function australiansenatorsandmembers_civicrm_uninstall() {
  _australiansenatorsandmembers_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function australiansenatorsandmembers_civicrm_disable() {
  _australiansenatorsandmembers_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function australiansenatorsandmembers_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _australiansenatorsandmembers_civix_civicrm_upgrade($op, $queue);
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
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function australiansenatorsandmembers_civicrm_caseTypes(&$caseTypes) {
  _australiansenatorsandmembers_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function australiansenatorsandmembers_civicrm_angularModules(&$angularModules) {
_australiansenatorsandmembers_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function australiansenatorsandmembers_civicrm_alterSettingsFolders(&$metaDataFolders) {
  static $configured = FALSE;
  if ($configured) return;
  $configured = TRUE;

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'settings';
  if(!in_array($extDir, $metaDataFolders)){
    $metaDataFolders[] = $extDir;
  }
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
