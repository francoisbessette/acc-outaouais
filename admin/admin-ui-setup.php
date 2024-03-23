<?php
/**
 * Admin setup for the plugin
 *
 * @since 1.1
 * @function	accou_add_menu_links()		Add admin menu pages
 * @function	accou_register_settings	Register Settings
 * @function	accou_validater_and_sanitizer()	Validate And Sanitize User Input Before Its Saved To Database
 * @function	accou_get_settings()		Get settings from database
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit; 
 
/**
 * Add admin menu pages
 *
 * @since 1.1
 * @refer https://developer.wordpress.org/plugins/administration-menus/
 */
function accou_add_menu_links() {
	add_options_page ( __('ACC Outaouais','acc-outaouais'), 	//Title
	                   __('Settings for ACC Outaouais','acc-outaouais'),    //Menu Title
					   'update_core', 						    //Capability
					   'acc-outaouais',						    //slug
					   'accou_admin_interface_render'  );       //Callback
}
add_action( 'admin_menu', 'accou_add_menu_links' );

/**
 * Register Settings
 *
 * @since 1.1
 */
function accou_register_settings() {

	// Register Setting
	register_setting( 
		'accou_settings_group', 			// Group name
		'accou_settings', 					// Setting name = html form <input> name on settings form
		'accou_validater_and_sanitizer'		// Input sanitizer
	);
	
	// Register A New Section
    add_settings_section(
        'accou_general_settings_section',							// ID
        __('General Settings', 'acc-outaouais'),					// Title
        'accou_general_settings_section_callback',					// Callback Function
        'acc-outaouais'												// Page slug
    );
	
    add_settings_field(
        'accou_new_event_email',									// ID
        __('Send email when an event gets published?', 'acc-outaouais'),	// Title
        'accou_chkbox_render',										// Callback function
        'acc-outaouais',											// Page slug
        'accou_general_settings_section',							// Settings Section ID
		array(
			'name' => 'accou_new_event_email',
		)
    );
	
	$roles = wp_roles();
	$roles_keys = array_keys($roles->roles);
    add_settings_field(
        'accou_new_event_roles',									// ID
        __('Send to which roles?', 'acc-outaouais'),				// Title
        'accou_chkboxes_render',									// Callback function
        'acc-outaouais',											// Page slug
        'accou_general_settings_section',							// Settings Section ID
		array(
			'name' => 'accou_new_event_roles',
			'choices' => $roles_keys,
			)
    );
	
    add_settings_field(
        'accou_new_event_admin',									// ID
        __('Also notify this admin email (can be blank)', 'acc-outaouais'),	// Title
        'accou_text_render',										// Callback function
        'acc-outaouais',											// Page slug
        'accou_general_settings_section',							// Settings Section ID
		array(
			'type' => 'text',
			'name' => 'accou_new_event_admin',
		)
    );
	
    add_settings_field(
        'accou_add_to_mailchimp',									// ID
        __('Add new members to a Mailchimp e-bulletin list?', 'acc-outaouais'),	// Title
        'accou_chkbox_render',										// Callback function
        'acc-outaouais',											// Page slug
        'accou_general_settings_section',							// Settings Section ID
		array(
			'name' => 'accou_add_to_mailchimp',
		)
	);
	
    add_settings_field(
        'accou_mailchimp_list_id',									// ID
        __('Enter MailChimp List ID', 'acc-outaouais'),				// Title
        'accou_text_render',										// Callback function
        'acc-outaouais',											// Page slug
        'accou_general_settings_section',							// Settings Section ID
		array(
			'type' => 'text',
			'name' => 'accou_mailchimp_list_id',
		)
    );
}
add_action( 'admin_init', 'accou_register_settings' );

/**
 * Validate and sanitize user input before its saved to database
 *
 * @since 1.1
 */
function accou_validater_and_sanitizer ( $settings ) {

	// foreach($settings as $key => $value) {
	// 	$settings[$key] = sanitize_text_field($value);
	// }

	return $settings;
}
		
/**
 * Get the list of roles to send an email to.
 *
 * @return		An array of role.
 *
 * @since 1.1
 */
function accou_get_new_event_roles() {
	$roles_configuration = accou_get_setting('accou_new_event_roles');
	return array_keys($roles_configuration);
}


/**
 * Get settings from database
 *
 * @return		A merged array of default and settings saved in database. 
 *
 * @since 1.1
 */
function accou_get_setting($setting_name) {

	$defaults = array(
				//should the plugin send an email when a new event is published? (checkbox)
				'accou_new_event_email' 			=> '0',
				//Which roles should receive the email?
				'accou_new_event_roles' 			=> array(),
				//Should an admin also receive the email? (textbox for email address)
				'accou_new_event_admin'     		=> '',
				//Add new members to a Mailchimp list? (checkbox)
				'accou_add_to_mailchimp' 			=> '0',
				//if so, which MailChimp list ID? (textbox)
				'accou_mailchimp_list_id'			=> '',
			);

	$settings = get_option('accou_settings', $defaults);
	//For an unkonw reason get_option does not return the default values for
	//options not stored in the database. The following line is a workaround.
	$settings = is_array($settings) ? array_merge($defaults, $settings) : $defaults;

	return $settings[$setting_name];
}

/**
 * Enqueue Admin CSS and JS
 *
 * @since 1.1
 */
function accou_enqueue_css_js( $hook ) {
	
    // Load only on ACC outaouais plugin pages
	if ( $hook != "settings_page_acc-outaouais" ) {
		return;
	}
	
	// Main CSS
	// wp_enqueue_style( 'accou-admin-main-css', ACCOU_PLUGIN_URL . 'admin/css/main.css', '', ACCOU_VERSION_NUM );
	
	// Main JS
    // wp_enqueue_script( 'accou-admin-main-js', ACCOU_PLUGIN_URL . 'admin/js/main.js', array( 'jquery' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'accou_enqueue_css_js' );