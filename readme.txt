=== acc-outaouais ===
Contributors: Francois Bessette
Tags: 
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Repository: https://github.com/francoisbessette/acc-outaouais


== Description ==
Wordpress plugin used to customize the operation of the Alpine Club of Canada
section Outaouais web site.  What goes here is code which is specific to
the section web site.  For instance, hooks and filters that modify operation
of other publicly available plugins.

More specifically
    -modify The Events Calendar to display the organizer email and phone
     only if the user is logged in.



== Installation ==
1. install "acc-outaouais-x-y-z.zip".
2. Activate the plugin.

== User Guide ==
There are no settings to configure



== Road Map ==
Here are some features that could be implemented in the future:
-filter notifications to send emails only to those interested


== Contact ==
https://github.com/francoisbessette

== Acknowledgements ==

== Test Cases ==
After making changes, here are tests to perform to verify proper operation.
Test on a staging or development site with email transmission disabled.
When not connected to the web site:
    -Open an existing activity and verify that
     the organizer email and phone are not displayed.
    -click on the organizer name, and verify that on the organizer sheet,
     the email and phone are not displayed
When connected to the web site:
    -Open an existing activity and verify that
     the organizer email and phone are displayed.
    -click on the organizer name, and verify that on the organizer sheet,
     the email and phone are displayed
 

== Changelog ==
1.0.0 Francois Bessette
    Creation
