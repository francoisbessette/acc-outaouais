# Changelog

## 1.3 Francois Bessette

- Add an unsubscribe link at the botton of emails sent when a new event
  is published. Add unsubscribe form and store user preference to meta
  variable called acc_email_on_new_events. Do not send email to users
  who have variable set to 0.

## 1.2 Francois Bessette

- The Brevo transactional email service accepts a maximum of 99 recipients.
  Fixed code to send emails (when a new event is published) in batch of 50 users.
  Put email addresses on BCC line for privacy.

## 1.1.0 Francois Bessette

- Modified the plugin code from a single flat file to a more extensible structure.
- Added settings.
- Now capable to add a new member to a Mailchimp list.
- Now capable to send emails to all members when a new activity is published.

## 1.0 Francois Bessette

- Hides organizer email and phone number from non-connected users
