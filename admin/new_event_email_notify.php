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

        //-------Get a list of recipients---------
        //Get the configured list of roles to send email to
        $roles = accou_get_new_event_roles();
        //From the user database, get a list of all members with those roles
        $users_with_role = get_users_by_roles($roles);
        accou_log("Sending to " . count($roles) . " roles, " . count($users_with_role) . " users");

        foreach($users_with_role as $user_id) {
            //Skip users who do not want to get emails
            $emailWanted = get_user_meta($user_id, 'acc_email_on_new_events', true);
            if ($emailWanted === '0') continue;

            $to = get_the_author_meta('user_email', $user_id );
            $unsubscribeUrl = acc_generate_unsubscribe_link( $user_id );
            $msgWithUnsubscribe = $message . '<p><a href="' . esc_url($unsubscribeUrl) . 
                '">Se désabonner ou changer mes préférences</a></p>';

            if(wp_mail($to, $subject, $msgWithUnsubscribe, $headers)) {
                accou_log("Mail sent successfully to $to");
            } else {
                accou_log("Mail sending failed to $to");
            }
        }
        //If needed, send to the configured admin email who also want to be notified
        $admin_email = accou_get_setting('accou_new_event_admin');
        if (!empty($admin_email)) {
            if(wp_mail($admin_email, $subject, $message, $headers)) {
                accou_log("Mail sent successfully to $admin_email");
            } else {
                accou_log("Mail sending failed to $admin_email");
            }
        }

    }
}

add_action('wp_after_insert_post', 'accou_send_email_on_event_creation', 995, 4);



function acc_generate_unsubscribe_link($user_id) {

    $data = $user_id . '|acc_email_prefs';
    $signature = hash_hmac('sha256', $data, AUTH_SALT);

    return add_query_arg([
        'acc_unsub' => 1,
        'uid'       => $user_id,
        'sig'       => $signature,
    ], site_url('/'));
}

/**
 * Login-free, non-expiring email preference management.
 * User meta acc_email_on_new_events:
 *      NULL or key not exists:  Send email to user (default when user joins)
 *      0       Do not send email to user 
 *      1       Send email to user
 */
add_action('init', function () {

    if (!isset($_GET['acc_unsub'])) {
        return;
    }

    $user_id = isset($_GET['uid']) ? absint($_GET['uid']) : 0;
    $sig     = isset($_GET['sig']) ? sanitize_text_field($_GET['sig']) : '';

    if (!$user_id || !$sig) {
        wp_die('Lien invalide.', 'Erreur', ['response' => 400]);
    }

    // Verify signature (non-expiring)
    $expected_sig = hash_hmac(
        'sha256',
        $user_id . '|acc_email_prefs',
        AUTH_SALT
    );

    if (!hash_equals($expected_sig, $sig)) {
        wp_die('Lien de désabonnement invalide.', 'Erreur de sécurité', ['response' => 403]);
    }

    $meta_key = 'acc_email_on_new_events';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action_sig = $_POST['action_sig'] ?? '';
        if (!hash_equals($expected_sig, $action_sig)) {
            wp_die('Formulaire invalide.', 'Erreur de sécurité', ['response' => 403]);
        }

        $new_value = isset($_POST['acc_email_on_new_events']) ? '1' : '0';
        update_user_meta($user_id, $meta_key, $new_value);

        $saved = true;
    }

    // Default preference = true if the user does not have such meta yet.
    $current_value = get_user_meta($user_id, $meta_key, true);
    $current_value = ($current_value === '' ? '1' : $current_value);

    $logo_url = plugin_dir_url(__FILE__) . 'cac_outaouais_logo.svg';

    // Minimal standalone HTML
    header('Content-Type: text/html; charset=utf-8');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Email Preferences</title>
<style>
    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        background: #f6f6f6;
        padding: 40px;
    }
    .card {
        max-width: 420px;
        margin: auto;
        background: #fff;
        padding: 24px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    button {
        margin-top: 16px;
        padding: 8px 14px;
        font-size: 15px;
        cursor: pointer;
    }
    .notice {
        background: #e7f7e7;
        border-left: 4px solid #46b450;
        padding: 10px;
        margin-bottom: 15px;
    }
    .acc-logo {
        width: 200px;
        height: auto;
        margin: 0 auto 20px;
        display: block;
    }
</style>
</head>
<body>

<div class="card">
    <img
        src="<?php echo esc_url($logo_url); ?>"
        alt="CAC logo"
        class="acc-logo"
    >
    <h2>Gérez les communications provenant du CAC section Outaouais</h2>

    <?php if (!empty($saved)): ?>
        <div class="notice">Préférences sauvegardées.</div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action_sig" value="<?php echo esc_attr($expected_sig); ?>">

        <label>
            <input type="checkbox" name="acc_email_on_new_events" value="1"
                <?php checked($current_value, '1'); ?>>
            Envoyez-moi un courriel lorsqu'une nouvelle activité est publiée dans le calendrier
        </label>

        <br>

        <button type="submit">Sauvegarder</button>
    </form>
</div>

</body>
</html>
<?php
    exit;
});
