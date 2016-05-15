<?php
/*
Plugin Name: Barley for WordPress
Plugin URI: http://getbarley.com/
Description: The inline editor for everyone. Now for WordPress.
Text Domain: barley-for-wordpress
Author: The Barley Team
Version: 1.7.0
Author URI: http://getbarley.com/

------------------------------------------------------------------------
Copyright 2012-2013 Plain, LLC.  | http://plainmade.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program (see license.txt); if not, write to the
Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// Define a few constances that can be used throughout the plugin
define('BARLEY_MIN_WP_VERSION', '3.6.1');
define('BARLEY_WP_VERSION', '1.7.0');
define('BARLEY_WP_HOSTNAME', parse_url(get_bloginfo("url"),PHP_URL_HOST));
define('BARLEY_PLUGIN_URL', plugins_url() . '/barley');
define('BARLEY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BARLEY_ADMIN_AJAX_URL', parse_url(admin_url('admin-ajax.php'), PHP_URL_PATH) ); // Just return the path of the AJAX url so that it works with https and http
define('BARLEY_ENVIRONMENT', 'PRODUCTION');

$barley_options = get_option('barley_options');

if ( isset($_GET['barley_editor']) && $_GET['barley_editor'] == 'on' ) {
        $barley_editor_currently_on = true;
    } elseif ( isset($_GET['barley_editor']) && $_GET['barley_editor'] == 'off' ) {
        $barley_editor_currently_on = false;
    } else {
        if ( !empty($barley_options['barley_option_onbydefault']) ) {
            #Barley Editor is currently off by default
            $barley_editor_currently_on = true;
        } else {
            $barley_editor_currently_on = false;
        }
    }

// Loads Barley files during "init" for WordPress MU support
function barley_load() {
    if ( current_user_can('edit_posts') ) {

        // Load Admin Bar Modification Functions
        require_once(dirname(__FILE__) . '/functions_admin_bar.php');

        // Load Barley Editor Functions
        require_once(dirname(__FILE__) . '/functions_barley_editor.php');

        // Load WordPress Post Functions
        require_once(dirname(__FILE__) . '/functions_posts.php');

    }

    // If we're in the Admin, load some of these babies.
    // But not if you're on the front end. Just because, faster.
    if ( is_admin() ) {

        // Load Admin Bar Modification Functions
        require_once(dirname(__FILE__) . '/functions_admin.php');

        // Load optional widget to promote Barley
        require_once(dirname(__FILE__) . '/functions_widget.php');

    }

    // Hack for Origami theme
    require_once(ABSPATH . 'wp-admin/includes/screen.php');

}
add_action( 'plugins_loaded', 'barley_load' );



# Set up localization
# Locale file should be placed under barley/languages/ and named 'barley-for-wordpress_LOCALE.mo', 
# for example barley-for-wordpress-pl_PL.mo
function barley_init_plugin_localization(){
    load_plugin_textdomain('barley-for-wordpress', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('init', 'barley_init_plugin_localization');
?>
