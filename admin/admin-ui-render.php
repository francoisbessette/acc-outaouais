<?php
/**
 * Admin UI setup and render
 *
 * @since 1.1
 * @function	accou_general_settings_section_callback()	Callback function for General Settings section
 * @function	accou_chkbox_render()	Callback function for a single checkbox
 * @function	accou_chkboxes_render()	Callback function for multiple checkboxes
 * @function	accou_text_render()	 Callback function for a textbox
 * @function	accou_admin_interface_render()				Admin interface renderer
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Callback function for General Settings section
 *
 * @since 1.1
 */
function accou_general_settings_section_callback() {
	echo '<p>' . __('These are the settings specific to the ACC Outaouais plugin.', 'acc-outaouais') . '</p>';
}


/**
 * Render for a single on/off checkbox.
 * If checked, the WP database stores '1'.
 * If not checked, the WP database has no data for that option.
 *
 * @since 1.1
 */
function accou_chkbox_render($args) {
	$input_name = $args['name'];
	$value = accou_get_setting($input_name);
	$checked = (isset($value) && $value === '1') ? '1' : '';

	$html = "<input type=\"checkbox\"";
	$html .= " id=\"$input_name\"";
	$html .= " name=\"accou_settings[$input_name]\" value=\"1\"";
	$html .= checked( '1', $checked, FALSE ) . ' />';
	echo $html;
}

/**
 * Render for a list of checkboxes.
 * If checked, the WP database stores '1'.
 * If not checked, the WP database has no data for that option.
 *
 * @since 1.1
 */
function accou_chkboxes_render_backup($args) {
	$input_name = $args['name'];
	if (isset($args['choices'])) {
		$choices = $args['choices'];
	}
	$values = accou_get_setting($input_name);

	foreach ($choices as $key => $choice) {
		$checked = (isset($values[$choice]) && $values[$choice] === '1') ? '1' : '';
		$html = "<input type=\"checkbox\"";
		$html .= " id=\"$choice\"";
		$html .= " name=\"accou_settings[$choice]\" value=\"1\"";
		$html .= checked( '1', $checked, FALSE ) . ' />';
		$html .= '<label for="' . $choice . '">' . $choice . '</label><br>';
		echo $html;

	}
}


/**
 * Render for a list of checkboxes.
 * If checked, the WP database stores '1'.
 * If not checked, the WP database has no data for that option.
 *
 * @since 1.1
 */
function accou_chkboxes_render($args) {
	$input_name = $args['name'];
	$choices = $args['choices'];
	$values = accou_get_setting($input_name);

	foreach ($choices as $key => $choice) {
		$checked = (isset($values[$choice]) && $values[$choice] === '1') ? '1' : '';
		$html = "<input type=\"checkbox\"";
		$html .= " id=\"$choice\"";
		$html .= ' name="accou_settings[' . $input_name . '][' . $choice . ']" value="1"';
		$html .= checked( '1', $checked, FALSE ) . ' />';
		$html .= '<label for="' . $choice . '">' . $choice . '</label><br>';
		echo $html;
	}
}


/**
 * Render for a textbox field.
 *
 * @since 1.1
 */
function accou_text_render($args) {
	$input_name = $args['name'];
	$input_type = $args['type'];
	$value = accou_get_setting($input_name);

	$html = "<input type=\"$input_type\"";
	$html .= " id=\"$input_name\"";
	$html .= " name=\"accou_settings[$input_name]\"";
	//add extra html tags if any are given
	if ( !empty($args['html_tags'] )) { $html .= ' ' . $args['html_tags']; }
	$html .= " value=\"$value\"";
	$html .= "/>";
	echo $html;
}


/**
 * Admin interface renderer
 *
 * @since 1.1
 */ 
function accou_admin_interface_render () {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	/**
	 * If settings are inside WP-Admin > Settings, then WordPress will automatically 
	 * display Settings Saved. If not used this block
	 * @refer	https://core.trac.wordpress.org/ticket/31000
	 * If the user have submitted the settings, WordPress will add the "settings-updated" $_GET parameter to the url
	 *
	if ( isset( $_GET['settings-updated'] ) ) {
		// Add settings saved message with the class of "updated"
		add_settings_error( 'accou_settings_saved_message', 'accou_settings_saved_message', __( 'Settings are Saved', 'acc-outaouais' ), 'updated' );
	}
 
	// Show Settings Saved Message
	settings_errors( 'accou_settings_saved_message' ); */?> 
	
	<div class="wrap">	
		<h1><?php __('ACC Outaouais Plugin Settings', 'acc_outaouais')?></h1>
		
		<form action="options.php" method="post">		
			<?php
			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'accou_settings_group' );
			
			// Prints out all settings sections added to a particular settings page. 
			do_settings_sections( 'acc-outaouais' );	// Page slug
			
			// Output save settings button
			submit_button( __('Save Settings', 'acc-outaouais') );
			?>
		</form>
	</div>
	<?php
}