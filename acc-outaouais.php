<?php

/**
 * Plugin Name:       ACC Outaouais
 * Description:       Plugin used to customize operation of the ACC Outaouais web site.
 * Version:           1.0
 * Author:            François Bessette
 * Author URI:        https://github.com/francoisbessette
 * License:           GPL v2 or later
 * Text Domain:       acc-outaouais
 */

//if ( ! defined( 'ABSPATH' ) ) {
//	exit;
//}

/*
 * Display the organizer email only if user is connected
 */
function accou_filter_tribe_get_organizer_email($filtered_email, $unfiltered_email) {
    error_log("--------------Inside accou_filter_tribe_get_organizer_email--------------");
    if (is_user_logged_in()) {
        return($filtered_email);
    } else {
        return("");
    }
}

/*
 * Display the organizer phone only if user is connected
 */
function accou_filter_tribe_get_organizer_phone($phone) {
    error_log("--------------Inside accou_filter_tribe_get_organizer_phone--------------");
    if (is_user_logged_in()) {
        return($phone);
    } else {
        return("");
    }
}



// /**
//  * Activate the plugin.
//  */
// function accou_activate_plugin() {
//     error_log("--------------Inside accou_activate_plugin--------------");

//     // Customize 'The Event Calendar' to prevent displaying the organizer info to non-connected users
//     add_filter('tribe_get_organizer_email', 'accou_filter_tribe_get_organizer_email', 10, 2);
//     add_filter('tribe_get_organizer_phone', 'accou_filter_tribe_get_organizer_phone');
// }

// /**
//  * Deactivate the plugin.
//  */
// function accou_deactivate_plugin() {
//     error_log("--------------Inside accou_deactivate_plugin--------------");

//     remove_filter('tribe_get_organizer_email', 'accou_filter_tribe_get_organizer_email', 10);
//     remove_filter('tribe_get_organizer_phone', 'accou_filter_tribe_get_organizer_phone', 10);
// }

// register_activation_hook( __FILE__, 'accou_activate_plugin' );
// register_deactivation_hook( __FILE__, 'accou_deactivate_plugin' );


// Customize 'The Event Calendar' to prevent displaying the organizer info to non-connected users
add_filter('tribe_get_organizer_email', 'accou_filter_tribe_get_organizer_email', 10, 2);
add_filter('tribe_get_organizer_phone', 'accou_filter_tribe_get_organizer_phone');
