<?php
// Check to see if there is an update available.
// Accepts: Nothing
// Returns: Success = The appropriate plugin update information
// Failure: Shows Admin notice if needed or just returns.
function barley_check_update($option) {

    $barley_license_version = barley_license_version();

    if ( !$barley_license_version ) {
        add_action( 'admin_notices', 'barley_update_error' );
        return;
    }

    // There might be an update available.
    // But we need to check the version compared to the current version
    if ( isset($barley_license_version['version']) && $barley_license_version['version'] > BARLEY_WP_VERSION ) {

        // There is an update available
        // Set up WordPress' update parameters.
        $plugin_path = "barley/barley.php";
        
        if ( !isset($option->response[$plugin_path]) ) {
            $option->response[$plugin_path] = new stdClass();
            $option->response[$plugin_path]->url = "http://getbarley.com";
            $option->response[$plugin_path]->slug = "barley";
            $option->response[$plugin_path]->package = $barley_license_version["url"];
            $option->response[$plugin_path]->new_version = $barley_license_version["version"];
            $option->response[$plugin_path]->id = "0";
        }

        return $option;

    } else {
        
        // This baby is already up-to-date.
        $barley_update_version = barley_license_version_update(barley_get_licensekey());
    }

// We made it all the way here. No updates. Return.
return $option;
}

// If the plugin is up for an update, it checks transient table for latest version's change log
// Accepts: Nothing
// Returns: HTML
function barley_get_changelog(){
    if($_REQUEST["plugin"] != "barley")
            return;

    // Get changelog information from WordPress' Transient API
    $barley_license_version = get_transient('barley_license_version');
    $barley_license_version = json_decode($barley_license_version['body'], true);
    $barley_changelog = stripslashes($barley_license_version['change_log']);


    echo '<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->
<!--[if !IE 7]>
    <style type="text/css">
        #wrap {display:table;height:100%}
    </style>
<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>Barley for Wordpress Changelog</title>
    <link href="http://fonts.googleapis.com/css?family=Muli:400,400italic|Montserrat" rel="stylesheet" type="text/css">
    <link href="'.BARLEY_PLUGIN_URL.'/barley_editor/fonts/barlicons.css" rel="stylesheet" type="text/css">

    <style type="text/css">
        p { font-family:\'Muli\', sans-serif; font-size:12px; line-height:19px; margin: 10px 0; }
        h2 { border-bottom:1px dotted #d4d4d4;font-family:\'Montserrat\', sans-serif;font-size:14px;color:#333;text-transform:uppercase;letter-spacing:1px;word-spacing:2px;margin:0;padding:14px 0;font-weight:normal;margin:26px 0; }
        ul { list-style:none; font-family:\'Muli\', sans-serif; font-size:13px; }
            li { margin:6px 0; }
                li i { margin-left:-38px; width:38px; color:#666; }
        }
    </style>
</head>
<body style="margin:0;position:relative;-webkit-font-smoothing:antialiased;">
    <header style="height:70px;background:white;box-shadow:0 8px 0 0 rgba(0,0,0,0.04),0 0 4px 4px rgba(0,0,0,0.1);margin:0;padding:0 20px;border-top:8px solid #eed974;">
        <h1 style="font-family:\'Montserrat\', sans-serif;font-size:14px;color:#333;text-transform:uppercase;letter-spacing:1px;word-spacing:2px;margin:0;padding:25px 0;font-weight:normal;">
            Barley for Wordpress
            <span style="float:right;text-transform:none;letter-spacing:normal;">v '.$barley_license_version['version'].'</span>
        </h1>
    </header>
    <div style="background:white;background: -moz-linear-gradient(top,  rgba(232,232,232,1) 0%, rgba(255,255,255,1) 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(232,232,232,1)), color-stop(100%,rgba(255,255,255,1)));background: -webkit-linear-gradient(top,  rgba(232,232,232,1) 0%,rgba(255,255,255,1) 100%);background: -o-linear-gradient(top,  rgba(232,232,232,1) 0%,rgba(255,255,255,1) 100%);background: -ms-linear-gradient(top,  rgba(232,232,232,1) 0%,rgba(255,255,255,1) 100%);background: linear-gradient(to bottom,  rgba(232,232,232,1) 0%,rgba(255,255,255,1) 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#e8e8e8\', endColorstr=\'#ffffff\',GradientType=0 );position:absolute;top:70px;width:100%;height:100px;z-index:-1;opacity:0.35;">
    </div>

<!-- // BEGIN CONTENT // -->
    <div style="margin: 24px 32px 120px;padding: 1px 0;">';
        echo $barley_changelog;
    echo '</div>
    <!-- // END CONTENT // -->

        <footer style="margin:32px;border-top:1px dotted #d4d4d4;height:50px;position:relative;">
            <p style="font-family:\'Muli\', sans-serif;font-size:12px;line-height:19px;margin: 30px 0 30px;text-align:center;"><a href="#" style="color:#5590ab;text-decoration:none;border-bottom:1px dotted #5590ab;margin: 0 6px;">Barley Help Center</a><span style="color:#e5e5e5;">•</span><a href="#" style="color:#5590ab;text-decoration:none;border-bottom:1px dotted #5590ab;margin: 0 6px;">Learn about Barley CMS</a></p>
        </footer>

    </body>
    </html>';


    exit;
}

// If update errors for some reason (invalid license key, invalid host, etc. shows error)
// Accepts: Nothing
// Returns: HTML
function barley_update_error() {
    $barley_license_version = get_transient('barley_license_version');
    $barley_license_version = json_decode($barley_license_version['body'], true);
 ?>
        <div class="error">
            <p><strong><?php _e( 'Barley Update Error:', 'barley-for-wordpress' ); ?></strong> <?php printf( __( 'There may be an update available. However, %1$s <a href="http://support.getbarley.com">Visit the Barley Help Center</a> to report or correct the issue.', 'barley-for-wordpress'), $barley_license_version['message'] ); ?></p>
        </div>
<?php }


// Notice about update being available in WordPress Admin
// Accepts: Nothing
// Returns: HTML
function barley_update_available() {
    $barley_licensekey = barley_get_licensekey();
    if ( !isset($barley_licensekey) || '' == $$barley_licensekey ) { ?>
        <div class="notice">
            <p><strong><?php _e( 'Notice:', 'barley-for-wordpress' ); ?></strong> <?php _e( 'There is an update available for Barley for WordPress.', 'barley-for-wordpress' ); ?></p>
        </div>
    <?php }
}



// Plugin update actions
// Hooks into WordPress' update infrastructure
// Add Barley Editor HTML in footer
if ( BARLEY_LICENSE_KEY != 'NONE' ) { // If a License Key exists, check for update periodically
    if ( barley_license_check(BARLEY_LICENSE_KEY) ) {
        //add_filter("transient_update_plugins", 'barley_check_update');
        add_filter("site_transient_update_plugins", 'barley_check_update');
        add_action('install_plugins_pre_plugin-information', 'barley_get_changelog');
    }
}

?>