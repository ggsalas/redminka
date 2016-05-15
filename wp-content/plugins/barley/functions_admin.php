<?php
// Adding an option to the Writing panel
// Followed this tutorial: http://ottopress.com/2009/wordpress-settings-api-tutorial/
add_action( 'admin_init', 'barley_options_init' );
function barley_options_init() {
	
	// Register a setting for the Barley license Key
	// Accepts: Group, Name of Option (array), Validation callback
	register_setting( 'writing', 'barley_options', 'barley_options_validate' );

	// Put the option for this key on the Writing options panel in the WordPress Admin
	// Accepts: ID for section, Title of section, HTML callback, Page name to show on
	add_settings_section('barley_options_main', 'Barley for WordPress', 'barley_section_html', 'writing');

	// Add the input field to the section
	// Accepts: ID for field, Title for field, Input box callback, Page name to show on, ID of settings section
	add_settings_field('barley_option_onbydefault', 'Turn Barley Editor on by default?', 'barley_section_checkbox', 'writing', 'barley_options_main');
}

// The HTML injected into the Writing options panel
function barley_section_html() {
	echo '<p>' . __('', 'barley-for-wordpress') . '</p>';
}

// The checkbox for the switch
// All options are saved under barley_options "array" in WordPress options table
function barley_section_checkbox() {
	$options = get_option('barley_options');
	if ( empty($options['barley_option_onbydefault']) ) { // unchecked, or empty == not on
		echo "<input id='barley_option_onbydefault' name='barley_options[barley_option_onbydefault]' size='40' type='checkbox' value='yes' /> Yes<br /><small>Barley is currently off by default.</small>";
	} else { // checked, or "yes" == yes on by default
		echo "<input id='barley_option_onbydefault' name='barley_options[barley_option_onbydefault]' size='40' type='checkbox' checked value='yes' /> Yes<br /><small>Barley is currently on by default. To turn off, uncheck the box and click 'Save Changes' button.</small>";
	}
}

function barley_options_validate($input) {
	if ( empty($input['barley_option_onbydefault']) ) $input['barley_option_onbydefault'] = '';

return $input;
}

?>