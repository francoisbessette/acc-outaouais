<?php
/**
 * Hide organizer information (email and phone number) from non-connected users.
 *
 * @since 1.1
 * @function	accou_filter_tribe_get_organizer_email()
 * @function	accou_filter_tribe_get_organizer_phone()
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit; 
 

/*
 * Display the organizer email only if user is connected
 */
function accou_filter_tribe_get_organizer_email($filtered_email, $unfiltered_email) {
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
    if (is_user_logged_in()) {
        return($phone);
    } else {
        return("");
    }
}

// Customize 'The Event Calendar' to prevent displaying the organizer info to non-connected users
add_filter('tribe_get_organizer_email', 'accou_filter_tribe_get_organizer_email', 10, 2);
add_filter('tribe_get_organizer_phone', 'accou_filter_tribe_get_organizer_phone');
