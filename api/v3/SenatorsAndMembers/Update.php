<?php

/**
 * SenatorsAndMembers.Update API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_senators_and_members_Update_spec(&$spec) {
  $spec['magicword']['api.required'] = 1;
  $spec['url']['api.required'] = 1;
}

/**
 * SenatorsAndMembers.Update API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_senators_and_members_Update($params) {
  if (array_key_exists('magicword', $params) && $params['magicword'] == 'sesame') {
    dpm($params);
    $url = $params['url'];
    $handle = fopen($url, 'r');
    $headers = fgetcsv($handle);
    $returnValues = $headers;
    // ALTERNATIVE: $returnValues = array(); // OK, success
    // ALTERNATIVE: $returnValues = array("Some value"); // OK, return a single value

    // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
    return civicrm_api3_create_success($returnValues, $params, 'SenatorsAndMembers', 'Update');
  } else {
    throw new API_Exception(/*errorMessage*/ 'Everyone knows that the magicword is "sesame"', /*errorCode*/ 1234);
  }
}

