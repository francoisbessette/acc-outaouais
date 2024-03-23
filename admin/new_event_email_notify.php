<?php
/**
 * When a new event (eg outdoor activity) is created, this code gets triggered
 * and potentially notifies all members via email.
 *
 * @since 1.1
 * @function	accou_calculateSecondsBetweenDates()
 * @function	accou_send_email_on_event_creation()
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit; 
 

function accou_calculateSecondsBetweenDates($date1, $date2) {
    $datetime1 = DateTime::createFromFormat('Y-m-d H:i', $date1);
    $datetime2 = DateTime::createFromFormat('Y-m-d H:i', $date2);

    if ($datetime1 === false || $datetime2 === false) {
        return false;
    }

    $interval = $datetime2->getTimestamp() - $datetime1->getTimestamp();
    $seconds = round($interval);

    return $seconds;
}

function get_users_by_roles($roles) {
    if (!is_array($roles) || empty($roles)) {
        return []; // Return an empty array if the roles parameter is not an array or is empty
    }

    $users_query = new WP_User_Query(array(
        'role__in' => $roles // Use the 'role__in' parameter to specify the array of roles
    ));

    $users = $users_query->get_results();

    // If you want to return an array of user IDs only
    $user_ids = array_map(function($user) {
        return $user->ID;
    }, $users);

    return $user_ids;
}

/*
 * When an event goes to Publish state, send an email to all valid members.
 */
 function accou_send_email_on_event_creation($post_id, $post, $update, $post_before) {
   
    // If this is just a revision or auto-save, don't send the email.
    if (wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }

    $old_status = 'new';
    if (is_object($post_before)) {
        $old_status = $post_before->post_status;
    }

    accou_log( "[$post_id] $post->post_type $post->post_name $old_status->$post->post_status");
    
    if ($post->post_type == 'tribe_events' && 
        $old_status != 'publish' && 
        $post->post_status == 'publish') {

        if (accou_get_setting('accou_new_event_email') !== '1') {
            accou_log("Feature not configured to send email to new users");
            return;
        }
            
        $headers[] = 'From: CAC-Outaouais <info@cac-outaouais.org>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
                        
        //-------Set the recipients of the email---------
        //One email will be sent, to many email addresses.
        //Get the configured list of roles to send email to
        $roles = accou_get_new_event_roles();
        //From the user database, get a list of all members with those roles
        $users_with_role = get_users_by_roles($roles);
        accou_log("Sending to " . count($roles) . " roles, " . count($users_with_role) . " users");
        $to = array();
        foreach($users_with_role as $user_id) {
            $user = get_userdata($user_id);
            $to[] = $user->user_email;
            //accou_log("$user->display_name");
        }
        //If needed, add the configured admin email who also want to be notified
        $admin_email = accou_get_setting('accou_new_event_admin');
        if (!empty($admin_email)) {
            accou_log("Adding $admin_email to the to: list");
            $to[] = $admin_email;
        }
        // error_log("list of emails");
        // error_log(print_r($to, true));

        //------------Set the subject of the email--------------------
        $start_date = tribe_get_start_date($post_id, true, 'Y-m-d H:i');
        $subject = "Nouvelle activité: " . $post->post_title . ' (' . $start_date . ')';
        
        //----------------Set the content of the email------------------
        $post_url = get_permalink($post_id);
        $author_id = get_post_field('post_author', $post_id);
        if ($author_id === false) {
            $author_name = "Inconnu";
        } else {
            $author_name = get_the_author_meta('display_name', $author_id);
        }
        $organizer = tribe_get_organizer($post_id);
        if (empty($organizer)) {
            $org = "";      //Probably the meta info is not ready yet, oh well...
        } else {
            $org = "organisée par $organizer ";
        }
        $preamble = "Bonjour!<br>Une nouvelle activité $org a été publiée par $author_name.<br>";
        $preamble .= "Voici ci-bas une description sommaire.<br>";
        $preamble .= "Pour tous les détails et pour vous inscrire, consultez la page $post_url<br>";
        $preamble .= "Bonne journée!<br>-----------------------------------<br><br>";
        $message = $preamble . $post->post_content;

        // Send the email.
        $status = wp_mail($to, $subject, $message, $headers);
        accou_log("Email sent, status=$status");
    }
}

add_action('wp_after_insert_post', 'accou_send_email_on_event_creation', 995, 4);





