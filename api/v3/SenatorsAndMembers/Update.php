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
  $spec['members_url']['api.required'] = 1;
  $spec['senators_url']['api.required'] = 1;
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
  $members_url = $params['members_url'];
  $handle = fopen($members_url, 'r');
  $headers = fgetcsv($handle);

  $result = array();

  $members_headers = array_map('_senators_and_members_strip_header', $headers);
  $parent_group = _senators_and_members_group_upsert('Australian Senators and Members');
  while ($row = fgetcsv($handle)) {
    $properties = array_combine($members_headers, array_map('trim', $row));
    $contact_id = _senators_and_members_member_upsert($properties);
    $political_party = $properties['Political Party'];
    _senators_and_members_groupcontact_upsert($political_party, $contact_id, $parent_group);
    $result[$contact_id] = $properties;
  }

  $senators_url = $params['senators_url'];
  $handle = fopen($senators_url, 'r');
  $headers = fgetcsv($handle);

  $senators_headers = array_map('_senators_and_members_strip_header', $headers);
  while ($row = fgetcsv($handle)) {
    $properties = array_combine($senators_headers, array_map('trim', $row));
    $contact_id = _senators_and_members_senator_upsert($properties);
    $political_party = $properties['Political Party'];
    _senators_and_members_groupcontact_upsert($political_party, $contact_id, $parent_group);
    $result[$contact_id] = $properties;
  }

  $returnValues = $result;
  // ALTERNATIVE: $returnValues = array(); // OK, success
  // ALTERNATIVE: $returnValues = array("Some value"); // OK, return a single value

  // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
  return civicrm_api3_create_success($returnValues, $params, 'SenatorsAndMembers', 'Update');
}

function _senators_and_members_strip_header($header) {
  return trim(preg_replace('/""/', '', $header));
}

function _senators_and_members_clear_group_once($group_id) {
  static $groups = array();
  if (!in_array($group_id, $groups)) {
    $group_contacts_result = civicrm_api3('GroupContact', 'get', array(
      'return' => 'id',
      'group_id' => $group_id,
      'options' => array('limit' => 0),
    ));
    if (!empty($group_contacts_result['is_error'])) {
      _senators_and_members_import_log("Could not retrieve contacts for group $group_id");
    }
    else {
      $group_contacts = array_keys($group_contacts_result['values']);
      foreach ($group_contacts as $group_contact) {
        $result = civicrm_api3('GroupContact', 'create', array(
          'id' => $group_contact,
          'status' => 'Removed',
        ));
      }
      $groups[] = $group_id;
    }
  }
}

function _senators_and_members_member_upsert($properties) {
  $first_name = $properties['First Name'];
  $other_names = $properties['Other Names'];
  $last_name = $properties['Surname'];
  $gender = ucfirst(strtolower($properties['Gender']));
  $title = $properties['Courtesy Title'];
  $parliamentary_title = $properties['Parliamentary Titles'];
  $phone = $properties['Electorate Office Phone'];

  $address_properties = array_filter(array(
    'name'                   => $properties['Electorate'],
    'street_address'         => $properties['Electorate Office Postal Address'],
    'city'                   => $properties['Electorate Office Postal Suburb'],
    'state_province_id'      => _senators_and_members_state_value($properties['Electorate Office Postal State']),
    'postal_code'            => $properties['Electorate Office Postal PostCode'],
    'country'                => 'Australia',
  ));

  $unique_properties = array(
    'first_name' => $first_name,
    'middle_name' => $other_names,
    'last_name' => $last_name,
    'contact_type' => 'Individual',
    'contact_sub_type' => 'MP',
  );

  _senators_and_members_import_log("Upserting MP: $first_name $other_names $last_name");

  $existing_result = civicrm_api3('Contact', 'get', array(
    'sequential' => 1,
    'is_deleted' => 0,
  ) + $unique_properties);

  if (!empty($existing_result['is_error'])) {
    _senators_and_members_import_log("Error retrieving contact $first_name $other_names $last_name");
  }

  $count = (int)$existing_result['count'];
  if ($count > 1) {
    $error_message = "$count contacts found for $first_name $other_names $last_name. ";
    $error_message .= "This contact will not be imported until this is fixed manually in CiviCRM.";
    _senators_and_members_import_log($error_message);
    return;
  }
  elseif ($count == 1) {
    // When there is only one result, the API response has a top-level 'id'
    // parameter corresponding to the one result Contact ID.
    $contact_id = $existing_result['id'];
    $result = civicrm_api3('Contact', 'create', array(
      'id' => $contact_id,
      'gender_id' => $gender,
      'formal_title' => $title,
      'job_title' => $parliamentary_title,
    ) + $unique_properties);
    if (!empty($result['is_error'])) {
      _senators_and_members_import_log("Could not update MP $first_name $other_names $last_name");
    }
    $contact_id = $result['id'];
    _senators_and_members_import_log("Updated MP $contact_id: $first_name $other_names $last_name");
  }
  else {
    // Create a new MP contact.
    $result = civicrm_api3('Contact', 'create', array(
      'gender_id' => $gender,
      'formal_title' => $title,
    ) + $unique_properties);
    if (!empty($result['is_error'])) {
      _senators_and_members_import_log("Could not create MP $first_name $other_names $last_name");
    }
    $contact_id = $result['id'];
    _senators_and_members_import_log("Created new MP $contact_id: $first_name $other_names $last_name");
  }

  if (!empty($address_properties)) {
    _senators_and_members_address_upsert($contact_id, $address_properties);
  }

  _senators_and_members_phone_upsert($contact_id, $phone);
  return $contact_id;
}

function _senators_and_members_senator_upsert($properties) {
  $first_name = $properties['First Name'];
  $other_names = $properties['Other Names'];
  $last_name = $properties['Surname'];
  $gender = $properties['Gender'];
  $title = $properties['Title'];
  $parliamentary_title = $properties['Parliamentary Titles'];
  $phone = $properties['Electorate Telephone'];

  $address_properties = array_filter(array(
    'street_address'         => $properties['Electorate AddressLine1'],
    'supplemental_address_1' => $properties['Electorate AddressLine2'],
    'city'                   => $properties['Electorate Suburb'],
    'state_province_id'      => _senators_and_members_state_value($properties['Electorate State']),
    'postal_code'            => $properties['Electorate Postcode'],
    'country'                => 'Australia',
  ));

  $unique_properties = array(
    'first_name' => $first_name,
    'middle_name' => $other_names,
    'last_name' => $last_name,
    'contact_type' => 'Individual',
    'contact_sub_type' => 'Senator',
  );

  _senators_and_members_import_log("Upserting Senator: $first_name $other_names $last_name");

  $existing_result = civicrm_api3('Contact', 'get', array(
    'sequential' => 1,
    'is_deleted' => 0,
  ) + $unique_properties);

  if (!empty($existing_result['is_error'])) {
    _senators_and_members_import_log("Error retrieving contact $first_name $other_names $last_name");
  }

  $count = (int)$existing_result['count'];
  if ($count > 1) {
    $error_message = "$count contacts found for $first_name $other_names $last_name. ";
    $error_message .= "This contact will not be imported until this is fixed manually in CiviCRM.";
    _senators_and_members_import_log($error_message);
    return;
  }
  elseif ($count == 1) {
    // When there is only one result, the API response has a top-level 'id'
    // parameter corresponding to the one result Contact ID.
    $contact_id = $existing_result['id'];
    $result = civicrm_api3('Contact', 'create', array(
      'id' => $contact_id,
      'gender_id' => $gender,
      'formal_title' => $title,
      'job_title' => $parliamentary_title,
    ) + $unique_properties);
    if (!empty($result['is_error'])) {
      _senators_and_members_import_log("Could not update Senator $first_name $other_names $last_name");
    }
    $contact_id = $result['id'];
    _senators_and_members_import_log("Updated Senator $contact_id: $first_name $other_names $last_name");
  }
  else {
    // Create a new Senator contact.
    $result = civicrm_api3('Contact', 'create', array(
      'formal_title' => $title,
      'gender_id' => $gender,
    ) + $unique_properties);
    if (!empty($result['is_error'])) {
      _senators_and_members_import_log("Could not create Senator $first_name $other_names $last_name");
    }
    $contact_id = $result['id'];
    _senators_and_members_import_log("Created new Senator $contact_id: $first_name $other_names $last_name");
  }
  if (!empty($address_properties)) {
    _senators_and_members_address_upsert($contact_id, $address_properties);
  }
  _senators_and_members_phone_upsert($contact_id, $phone);
  return $contact_id;
}

// Create a group or retrieve an existing one with the given name.
function _senators_and_members_group_upsert($group, $parent = NULL) {
  // Get the existing group ID or create a new group and get its ID.
  $existing_group = civicrm_api3('Group', 'get', array(
    'title' => $group,
    'sequential' => 1,
  ));
  if (!empty($existing_group['is_error'])) {
    _senators_and_members_import_log("Could not retrieve group $group", 'fatal');
  }
  if ($existing_group['count']!= '0') {
    $group_id = $existing_group['values']['0']['id'];
    _senators_and_members_import_log("Retrieved ID $group_id for existing group $group");
  }
  else {
    $new_group = civicrm_api3('Group', 'create', array(
      'title'      => $group,
      'sequential' => 1,
      'parents' => $parent,
    ));
    if (!empty($new_group['is_error'])) {
      _senators_and_members_import_log("Could not create new group $group", 'fatal');
    }
    $group_id = $new_group['values']['0']['id'];
    _senators_and_members_import_log("Created group ID $group_id for new group $group");
  }
  return $group_id;
}

function _senators_and_members_groupcontact_upsert($political_party, $contact_id, $parent = NULL) {
  $party_group = _senators_and_members_group_upsert($political_party, $parent);
  _senators_and_members_clear_group_once($party_group);
  $result = civicrm_api3('GroupContact', 'create', array(
    'contact_id' => $contact_id,
    'group_id' => $party_group,
  ));
  _senators_and_members_import_log("Added contact $contact_id to group $party_group ($political_party)");
}

// Takes street_address, supplemental_address_1, city, state_province_name,
// postal_code, and country keys
function _senators_and_members_address_upsert($contact_id, $address_params) {
  $result = civicrm_api3('Address', 'get', array(
    'sequential' => 1,
    'contact_id' => $contact_id,
  ));
  if (!empty($result['is_error'])) {
    _senators_and_members_import_log("Could not retrieve address details for contact id $contact_id", 'error');
  }
  else {
    $api_params = array(
      'contact_id'       => $contact_id,
      'location_type_id' => 'Main',
      'is_primary'       => 1,
    );
    $count = (int)$result['count'];
    if ($count>0) {
      $addresses = $result['values'];
      foreach ($addresses as $address) {
        if ($address['is_primary'] == '1') {
          $address_id = $address['id'];
          $address_params['id'] = $address_id;
          _senators_and_members_import_log("Updating Address entity $address_id");
          break;
        }
      }
    }
    $params = $api_params + $address_params;
    $address_create = civicrm_api3('Address', 'create', $params);
    if (!empty($address_create['is_error'])) {
      _senators_and_members_import_log("Could not create address for contact $contact_id", 'error');
    }
  }
}

function _senators_and_members_state_value($state_abbreviation) {
  $state_abbreviation = strtolower($state_abbreviation);
  $values = array(
    'act' => 'Australian Capital Territory',
    'nt'  => 'Northern Territory',
    'nsw' => 'New South Wales',
    'qld' => 'Queensland',
    'sa'  => 'South Australia',
    'tas' => 'Tasmania',
    'vic' => 'Victoria',
    'wa'  => 'Western Australia',
  );
  return $values[$state_abbreviation];
}

// Makes a phone number the primary phone number of a contact
function _senators_and_members_phone_upsert($contact_id, $phone) {
  $number_params = array('phone' => $phone);
  $result = civicrm_api3('Phone', 'get', array(
    'sequential' => 1,
    'contact_id' => $contact_id,
  ));
  if (!empty($result['is_error'])) {
    _senators_and_members_import_log("Could not retrieve phone details for contact id $contact_id", 'error');
  }
  else {
    $api_params = array(
      'contact_id'    => $contact_id,
      'phone_type_id' => 'Phone',
      'is_primary'    => 1,
    );
    $count = (int)$result['count'];
    if ($count>0) {
      $numbers = $result['values'];
      foreach ($numbers as $number) {
        if ($number['is_primary'] == '1') {
          $number_id = $number['id'];
          $number_params['id'] = $number_id;
          break;
        }
      }
      _senators_and_members_import_log("Updated Phone entity $number_id");
    }
    $params = $api_params + $number_params;
    $number_create = civicrm_api3('Phone', 'create', $params);
    if (!empty($number_create['is_error'])) {
      _senators_and_members_import_log("Could not create phone for contact $contact_id");
    }
  }
}

// Used for logging and to exit if error is fatal.
function _senators_and_members_import_log($message, $priority = NULL) {
  CRM_Core_Error::debug_log_message($message, FALSE, 'senators-members-import', $priority);
}
