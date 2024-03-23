<?php 
/**
 * Basic setup functions for the plugin
 *
 * @since 1.1
 * @function	accou_activate_plugin()		Plugin activatation todo list
 * @function	accou_load_plugin_textdomain()	Load plugin text domain
 * @function	accou_settings_link()			Print direct link to plugin settings in plugins list in admin
 * @function	accou_plugin_row_meta()		Add donate and other links to plugins list
 * @function	accou_footer_text()			Admin footer text
 * @function	accou_footer_version()			Admin footer version
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Plugin activatation todo list
 *
 * This function runs when user activates the plugin. Used in register_activation_hook in the main plugin file. 
 * @since 1.1
 */
function accou_activate_plugin() {
	
}

/**
 * Load plugin text domain
 *
 * @since 1.1
 */
function accou_load_plugin_textdomain() {
    load_plugin_textdomain( 'acc-outaouais', false, '/acc-outaouais/languages/' );
}
add_action( 'plugins_loaded', 'accou_load_plugin_textdomain' );

/**
 * Print direct link to plugin settings in plugins list in admin
 *
 * @since 1.1
 */
function accou_settings_link( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=acc-outaouais' ) . '">' . __( 'Settings', 'acc-outaouais' ) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . ACCOU_PLUGIN . '/acc-outaouais.php', 'accou_settings_link' );

/**
 * Add donate or other similar links to plugins list
 *
 * @since 1.1
 */
function accou_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'acc-outaouais.php' ) !== false ) {
		$new_links = array(
				'emailme' 	=> '<a href="mailto:joeblo@gmail.com" target="_blank">Email me</a>',
				);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
//add_filter( 'plugin_row_meta', 'accou_plugin_row_meta', 10, 2 );

/**
 * Admin footer text
 *
 * A function to add footer text to the settings page of the plugin. 
 * Footer text could contain plugin rating and donation links.
 * Note: Remove the rating link if the plugin doesn't have a WordPress.org directory listing yet. (i.e. before initial approval)
 *
 * @since 1.1
 * @refer https://codex.wordpress.org/Function_Reference/get_current_screen
 */
function accou_footer_text($default) {
    
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== "settings_page_acc-outaouais" ) {
		return $default;
	}
	
    $accou_footer_text = sprintf( __( 'If you like this plugin, please <a href="%s" target="_blank">make a donation</a> or leave me a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating to support continued development. Thanks a bunch!', 'acc-outaouais' ), 
								'http://millionclues.com/donate/',
								'https://wordpress.org/support/plugin/acc-outaouais/reviews/?rate=5#new-post' 
						);
	
	return $accou_footer_text;
}
//add_filter('admin_footer_text', 'accou_footer_text');

/**
 * Admin footer version
 *
 * @since 1.1
 */
function accou_footer_version($default) {
	
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( $screen->id !== 'settings_page_acc-outaouais' ) {
		return $default;
	}
	
	return 'Plugin version ' . ACCOU_VERSION_NUM;
}
//add_filter( 'update_footer', 'accou_footer_version', 11 );