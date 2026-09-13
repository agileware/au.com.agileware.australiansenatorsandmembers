# Import Australian Senators and Members (au.com.agileware.australiansenatorsandmembers)

This is a [CiviCRM](https://civicrm.org) extension that populates and keeps up to date the names
and contact details for each currently elected Senator and Member (MP) in the
[Australian Parliament](https://en.wikipedia.org/wiki/Parliament_of_Australia).

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Purpose

Manually maintaining contact records for every sitting Senator and Member of the Australian House
of Representatives is tedious and quickly goes out of date as seats change hands. This extension
automates that job: it fetches the official "All Senators by Party" and "All Members by Party" CSV
files published on the
[Parliament of Australia website](https://www.aph.gov.au/Senators_and_Members/Guidelines_for_Contacting_Senators_and_Members/Address_labels_and_CSV_files)
and imports them into CiviCRM as Contacts, keeping names, titles, electorate office address,
electorate office phone number, and party affiliation up to date.

The source CSVs do not contain email addresses, so no attempt is made to import or update contact
email addresses.

## Usage

### Contact Types

On install, the extension creates two Contact Sub-types under **Individual**:

* **MP** — used for Members of the House of Representatives
* **Senator** — used for Senators

These sub-types are used to identify existing contacts during import so that re-running the import
updates the correct records instead of creating duplicates.

### API

The extension provides a single API action, **SenatorsAndMembers.Update**, which performs the
import. It requires two parameters, both mandatory:

* `members_url` — URL of the "All Members by Party" CSV file
* `senators_url` — URL of the "All Senators by Party" CSV file

For each row in the CSVs, the API will:

* Find an existing Individual contact of sub-type `MP` or `Senator` matching on first name, middle
  name (Other Names) and last name, or create a new one if no match is found.
* Set/update the contact's gender, formal title, and job title (parliamentary title).
* Set/update the primary "Main" address with the electorate office street address, suburb, state
  and postcode (country is always set to Australia).
* Set/update the primary phone number with the electorate office telephone number.
* Add the contact to a parent group, **Australian Senators and Members**, and to a group named
  after their political party, nested under that parent group. Existing members of a party group
  are marked "Removed" the first time that group is touched during a run, so each run's group
  membership reflects only the contacts present in the current CSV data.

The API can be run directly, e.g. from the CiviCRM API Explorer, or via `cv`/`drush`/`wp`:

Using Drupal and drush:
```
drush cvapi SenatorsAndMembers.update sequential=1 members_url="https://www.aph.gov.au/~/media/03%20Senators%20and%20Members/Address%20Labels%20and%20CSV%20files/SurnameRepsCSV.csv?la=en" senators_url="https://www.aph.gov.au/~/media/03%20Senators%20and%20Members/Address%20Labels%20and%20CSV%20files/allsenel.csv?la=en"
```
Using WordPress and wp:
```
wp civicrm api SenatorsAndMembers.update sequential=1 members_url="https://www.aph.gov.au/~/media/03%20Senators%20and%20Members/Address%20Labels%20and%20CSV%20files/SurnameRepsCSV.csv?la=en" senators_url="https://www.aph.gov.au/~/media/03%20Senators%20and%20Members/Address%20Labels%20and%20CSV%20files/allsenel.csv?la=en"
```

Import activity is written to the CiviCRM debug log under the `senators-members-import` name,
which is useful for troubleshooting failed or partial imports.

### Scheduled Job

The extension installs a Scheduled Job, **Call SenatorsAndMembers.Update API**, set to run Daily.
It is created without the required `members_url`/`senators_url` parameters, so it will not run
successfully until an administrator edits the job (Administer / System Settings / Scheduled Jobs)
and supplies both URL parameters.

### Settings page

The extension adds an **Import Australian Senators and Members Settings** page under
**Administer**, at `civicrm/aus_senators_members/settings`, restricted to users with the
"access CiviCRM" permission. It defines one setting, **Source CSV URL**, described as "Absolute URL
for the source file". _Note: this page is a stub inherited from the extension scaffold — the form
does not currently save the setting value or trigger an import, and the setting is not read
anywhere else in the codebase. Use the Scheduled Job or a direct API call (above) to configure and
run imports; do not rely on this settings page._

## Special configuration requirements

* No API keys, OAuth credentials, or dependent extensions are required.
* Before the import will run automatically, an administrator must edit the **Call
  SenatorsAndMembers.Update API** Scheduled Job to add the `members_url` and `senators_url`
  parameters (see [Scheduled Job](#scheduled-job) above), and enable the job.
* Users running the import manually (API Explorer, `cv`, `drush`, `wp`) must supply the same two
  URL parameters.

## Requirements

* CiviCRM 5.51+

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin
Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/). Once installed, enable
"Import Australian Senators and Members (au.com.agileware.australiansenatorsandmembers)" from
Administer / System Settings / Extensions.

## About the Authors

This CiviCRM extension was developed by the team at
[Agileware](https://agileware.com.au).

[Agileware](https://agileware.com.au) provide a range of CiviCRM
services including:

* CiviCRM migration
* CiviCRM integration
* CiviCRM extension development
* CiviCRM support
* CiviCRM hosting
* CiviCRM remote training services

Support your Australian [CiviCRM](https://civicrm.org) developers,
[contact Agileware](https://agileware.com.au/contact) today!

![Agileware](logo/agileware-logo.png)
