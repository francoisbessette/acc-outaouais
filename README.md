# ACC Outaouais

Contributors: Francois Bessette

Tags:

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Repository: https://github.com/francoisbessette/acc-outaouais


## Description
Wordpress plugin used to customize the operation of the
Alpine Club of Canada (ACC) section Outaouais Wordpress-based website.

The plugin assumes that 2 other plugins are installed on the web site:
acc_user_importer and mc4wp.  The plugin provides the following:

- Hides organizer email and phone number from non-connected users.
- If configured to do so, adds new members to a mailchimp list
- If configured to do so, sends an email to all members when a new event is published.
  We used to use a Publishpress plugin notification for that, however PublishPress 
  had limitations.  This plugin:
  - Only sends 1 email for a bunch of recurring events
  - Prints the event start date in the email subject
  - Can eventually send only to members who are interested.


## Configuration
(to be added)

## Installation
1. install "acc-outaouais.zip".
2. Activate the plugin.

## User Guide
(to be added)


## Hooks
None

## Caveats

## Road Map
Here are some future ideas that could be implemented, sorted by likelihood.
- add interest categories (ex: hiking, climbing) using Ultimate Member, and 
  only send new event notification emails to the interested members.

## Contact
* https://github.com/francoisbessette

## Acknowledgements

## Test Cases
### Filtering of organizer personal info on event page
- Verify that organizer email and phone is displayed for connected users
- Verify that organizer email and phone is not displayed for non-connected users
### Notification email when a new event is created
- Change an event from draft to publish, verify an email is triggered only
  if configured to do so.
- Re-publish a published email should not sent an email. Only when status
  changes from non-published to published.
- email sent only to specified roles
- email is sent to the right set of people according to their roles 
- when specifying to send to multiple roles, the email is sent to the merged members list.
- A member which has 2 roles should receive only 1 email.
- Verify the email is also sent to the specified admin email (if configured).
- Verify that the event publisher, organizer and event start_date are correct.
### Mailchimp integration
Main test method: use a development or staging web site. Disable sending of emails.
Delete a user account. Run the acc_user_importer plugin so that it re-adds that user.
This will execute the hook in acc-outaouais for adding the user to mailchimp.
- Verify nothing bad happens if the Mailchimp plugin is missing or deactivated.
- Verify logging.
- Verify nothing gets done if the "Add new members to Mailchimp" configuration
  is disabled.
- Assuming the configured mailchimp list ID option is valid, verify that the user
  appears in the mailchimp list on the web interface.
- verified user is moved to Subscribed if he was not.


