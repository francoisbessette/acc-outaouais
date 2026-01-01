<?php
/**
 * Plugin Name:       ACC Outaouais
 * Plugin URI:        https://github.com/francoisbessette/acc-outaouais
 * Description:       Plugin used to customize operation of the ACC Outaouais web site.
 * Version:           1.3
 * Author:            François Bessette
 * Author URI:        https://github.com/francoisbessette
 * License:           GPL v2 or later
 * Text Domain:       acc-outaouais
 * Domain Path:       /languages
 */

/**
 * This plugin was developed using the WordPress starter plugin template by Arun Basil Lal <arunbasillal@gmail.com>
 * Please leave this credit and the directory structure intact for future developers who might read the code.
 * @GitHub https://github.com/arunbasillal/WordPress-Starter-Plugin
 */

/**
 * ~ Directory Structure ~
 *
 * /admin/ 					- Plugin backend stuff.
 * /functions/				- Functions and plugin operations.
 * /includes/				- External third party classes and libraries.
 * /languages/				- Translation files go here.
 * /public/					- Front end files and functions that matter on the front end go here.
 * index.php				- Dummy file.
 * license.txt				- GPL v2
 * acc-outaouais.php		- Main plugin file containing plugin name and other version info for WordPress.
 * readme.txt				- Readme for WordPress plugin repository. https://wordpress.org/plugins/files/2018/01/readme.txt
 * uninstall.php			- Fired when the plugin is uninstalled.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define constants
 *
 * @since 1.1
 */
if ( ! defined( 'ACCOU_VERSION_NUM' ) ) 		define( 'ACCOU_VERSION_NUM'		, '1.3' );
if ( ! defined( 'ACCOU_PLUGIN' ) )		define( 'ACCOU_PLUGIN'		, trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
if ( ! defined( 'ACCOU_PLUGIN_DIR' ) )	define( 'ACCOU_PLUGIN_DIR'	, plugin_dir_path( __FILE__ ) ); // Plugin directory absolute path with the trailing slash. Useful for using with includes eg - /var/www/html/wp-content/plugins/acc-outaouais/
if ( ! defined( 'ACCOU_PLUGIN_URL' ) )	define( 'ACCOU_PLUGIN_URL'	, plugin_dir_url( __FILE__ ) ); // URL to the plugin folder with the trailing slash. Useful for referencing src eg - http://localhost/wp/wp-content/plugins/acc-outaouais/
if ( ! defined( 'ACCOU_LOG_DIR' ) )	define( 'ACCOU_LOG_DIR'	, ACCOU_PLUGIN_DIR . 'logs/' );

/**
 * Database upgrade todo
 *
 * @since 1.1
 */
function accou_upgrader() {

	// Get the current version of the plugin stored in the database.
	$current_ver = get_option( 'accou_version', '0.0' );

	// Return if we are already on updated version.
	if ( version_compare( $current_ver, ACCOU_VERSION_NUM, '==' ) ) {
		return;
	}

	// This part will only be excuted once when a user upgrades from an older version to a newer version.

	// Finally add the current version to the database. Upgrade todo complete.
	update_option( 'accou_version', ACCOU_VERSION_NUM );
}
add_action( 'admin_init', 'accou_upgrader' );

// Load everything
require_once( ACCOU_PLUGIN_DIR . 'loader.php' );

// Register activation hook (this has to be in the main plugin file or refer bit.ly/2qMbn2O)
register_activation_hook( __FILE__, 'accou_activate_plugin' );
