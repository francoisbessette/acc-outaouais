<?php
/**
 * Code to add new members to a Mailchimp list ID.
 * It hooks to the acc_user_importer plugin to learn about new members, and 
 * calls a function of the mc4wp plugin to update a MailChimp list.
 *
 * @since 1.1
 * @function	accou_register_new_user_to_mailchimp()
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit; 
 

function accou_register_new_user_to_mailchimp($user_id) {

    if ( !is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' ) ) {
        accou_log("MailChimp plugin is not active, nop");
        return;
    }

    if (accou_get_setting('accou_add_to_mailchimp') !== '1') {
        accou_log("acc-outaouais not configured to add users to MailChimp");
        return;
    }

    $mailchimp_list_id = accou_get_setting('accou_mailchimp_list_id');
    if (empty($mailchimp_list_id)) {
        accou_log("acc-outaouais is missing the MailChimp list ID configuration");
        return;
    }

    $api = mc4wp_get_api_v3();
    $use_double_optin = false;
    $user = get_userdata($user_id);
    accou_log("acc-outaouais: Adding new user $user->first_name $user->last_name $user->user_email " .
              "to MailChimp listID $mailchimp_list_id");

    $merge_fields = array(
        'FNAME' => $user->first_name,
        'LNAME' => $user->last_name,
    );

    $subscribe_params = array(
        'email_address' => $user->user_email,
        'status' => 'subscribed',
        'merge_fields' => $merge_fields,
    );

    try {
        $subscriber = $api->add_list_member( $mailchimp_list_id, $subscribe_params);
    } catch( \MC4WP_API_Exception $e ) {
        // an error occured
        // you can handle it here by inspecting the expection object and removing the line bwlo
        throw $e;
    }
}

add_action('acc_member_welcome', 'accou_register_new_user_to_mailchimp');
