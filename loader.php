<?php
/**
 * Loads the plugin files
 *
 * @since 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load basic setup. Plugin list links, text domain, footer links etc. 
require_once( ACCOU_PLUGIN_DIR . 'admin/basic-setup.php' );

// Load admin setup. Register menus and settings
require_once( ACCOU_PLUGIN_DIR . 'admin/admin-ui-setup.php' );

// Render Admin UI
require_once( ACCOU_PLUGIN_DIR . 'admin/admin-ui-render.php' );

require_once( ACCOU_PLUGIN_DIR . 'admin/hide_organizer_info.php' );
require_once( ACCOU_PLUGIN_DIR . 'admin/mailchimp.php' );
require_once( ACCOU_PLUGIN_DIR . 'admin/new_event_email_notify.php' );
require_once( ACCOU_PLUGIN_DIR . 'admin/logging.php' );

// Do plugin operations
require_once( ACCOU_PLUGIN_DIR . 'functions/do.php' );

