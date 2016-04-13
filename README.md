au.com.agileware.australiansenatorsandmembers
=============================================

This CiviCRM extension provides an API action (SenatorsAndMembers update) that
fetches 'All Senators by Party' and 'All Members by Party' CSVs (currently
available at http://www.aph.gov.au/Senators_and_Members/Guidelines_for_Contacting_Senators_and_Members/Address_labels_and_CSV_files)
and imports them into CiviCRM, assigning each senator or member to a
party-specific group. The Electorate office address and phone number are also
imported. The source data currently contains no email addresses which is why
there is no attempt to import these.

See Agileware Projects Issue #20907

# Configuration

1. Copy this extension into your extensions directory and enable in CiviCRM (a
   cache clear may be required).
2. Two contact types will be created: Senator and MP, which we use for our
   import process to avoid overwriting existing contacts with the same
   identifying fields.
3. Configure a scheduled job with the SenatorsAndMembers update API call using
   the parameters 'members_url' and 'senators_url'. Both these parameters are
   required. Our long-term plan is to automatically create a scheduled job when the
   extension is installed so that this step is unnecessary.
4. Alternatively, you can run the API action from the CiviCRM API Explorer
   whenever a manual import needs to be done.
5. Example command to execute and URLs
drush cvapi SenatorsAndMembers.update sequential=1 members_url="http://www.aph.gov.au/~/media/03%20Senators%20and%20Members/Address%20Labels%20and%20CSV%20files/SurnameRepsCSV.csv?la=en" senators_url="http://www.aph.gov.au/~/media/03%20Senators%20and%20Members/Address%20Labels%20and%20CSV%20files/allsenel.csv?la=en"