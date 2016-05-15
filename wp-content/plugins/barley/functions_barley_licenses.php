<?php
// Activate a Barley License
// Accepts: $barley_licensekey (string)
// Returns: True if activation is successful or Barley key already activated and valid for this host, False if error
function barley_license_activate( $barley_licensekey=false ) {
    // Ask API to activate license
    $barley_license_activate = barley_license_api_call( 'activate', $barley_licensekey );
    
    if($barley_license_activate === false){
        // Error contacting license API
        return false;
    }

    // Decode JSON
    $barley_license_activate = json_decode($barley_license_activate['body'], true);

    if ( isset($barley_license_activate['success']) && $barley_license_activate['success'] === false ) { // If license failed to activate, figure out why
        if($barley_license_activate['number'] == 4) { // License already activated - need to check host entry
            return barley_license_check($barley_licensekey); // Check domain (host)
        } else { // Other error - activation failed
            return false;
        }
    } else{ // Successfully activated
        return true;
    }
    
}

// Check Barley License Key (and domain)
// Accepts: $barley_licensekey (string)
// Returns: True if key is valid, False: If key is invalid or expired, False if domain is inactive
// Saves to WordPress Transient API for 12 hours
function barley_license_check($barley_licensekey=false) {

    // If license key not passed for this call, get it from DB
    if ( !$barley_licensekey ) {
        $barley_licensekey = barley_get_licensekey(); // Get Barley License Key from database.
    }
    
    if ( empty($barley_licensekey) ) { // Failed to retrieve key from params and DB
        return false;
    }

    // Check the database for valid license information
    $barley_license_check = get_transient('barley_license_check');

    // If none, ask API for updated information
    if ( !$barley_license_check ) {
        $barley_license_check = barley_license_api_call( 'check', $barley_licensekey );
        
        // If error with call, return false
        if ( !$barley_license_check ) {
            return false;
        } else{
            // Save to database
            set_transient("barley_license_check", $barley_license_check, 43200);
        }
    }
    
    // If no error, decode JSON
    $barley_license_check = json_decode($barley_license_check['body'], true);
    
    // If the actual response is "good" return true
    if ( isset($barley_license_check['success']) && $barley_license_check['success'] === true ) {
        return true;
    } else if($barley_license_check['success'] === false) { // License check failed
        if(isset($barley_license_check['number']) && $barley_license_check['number'] == 9){
            // License not activated - try to activate and check again 
            if(barley_license_activate($barley_licensekey)){
                // Remove cached response
                delete_transient("barley_license_check");
                // Run check again
                return barley_license_check($barley_licensekey);
            } else {
                return false;
            }
        } else{
            return false;
        }
    } else{
        return false;
    }
}

// Check to see if there are any updates available for this license key / domain
// Accepts: Nothing
// Returns: Array if there are updates available, True if there are no updates and False if error occured
function barley_license_version() {

    // Get License Key from DB
    $barley_licensekey = barley_get_licensekey(); // Get Barley License Key from database.
    
    if ( empty($barley_licensekey) ) { // Failed to retrieve key from DB
        return false;
    }

    // Look in DB for version information
    $barley_license_version = get_transient( 'barley_license_version' );

    // If none in DB, ask API
    if ( !$barley_license_version ) {
        $barley_license_version = barley_license_api_call( 'version' );

        // Save API response in DB for 12 hours, even if it failed
        set_transient( "barley_license_version", $barley_license_version, 43200 );

        // If problem with update, return false (no updates available)
        if ( empty($barley_license_version) ) {
            return false;
        }
    }

    // Decode JSON
    $barley_license_version = json_decode( $barley_license_version['body'], true );

    if( empty($barley_license_version) || !isset($barley_license_version['success'])){
        // API communication error - invalid or no response
        return false;
    } else {
        if ($barley_license_version['success'] === false) {
            // Invalid license passed
            return false;
        } else {
            // Correct license used
            if (isset($barley_license_version['number']) && $barley_license_version['number'] == 30) {
                // If Number is 30 the call was successful but no updates are available.
                return true;
            } else {
                // There is an update available!
                // Return an array for WordPress to handle.
                return array(
                    "is_valid_licensekey" => "1",
                    "version" => $barley_license_version['version'],
                    "url" => $barley_license_version['url'],
                    "change_log" => $barley_license_version['change_log']
                );
            }
        }
    }
}

// Update version information about the plugin stored at Barley backend DB
// Accepts: $barley_licensekey (string)
// Returns: True if updated correctly, false otherwise
function barley_license_version_update($barley_licensekey=null) {

    // Look in DB for version information
    $barley_license_version_update = get_transient( 'barley_license_version_update' );

    if ( !$barley_license_version_update ) { // Only run if there is no entry in DB, otherwise wait for 12 hours

        // If license key not passed for this call, get it from DB
        if ( !$barley_licensekey ) {
            $barley_licensekey = barley_get_licensekey(); // Get Barley License Key from database.
        }

        // Update the version for this key and host
        $barley_license_version_update = barley_license_api_call( 'version/update', $barley_licensekey );

        // Error updating version in our DB
        if ( !$barley_license_version_update ) {
            return false;
        }

        // Save any value into barley_license_version_update key not to call backend in next 12 hours
        set_transient( "barley_license_version_update", 'Up-to-date', 43200 ); // 12 hours

    }

    return true;
}

// Get the License Key from the WordPress Options Table
// Returns: License key as a string or false if it's not set
function barley_get_licensekey(){
    $barley_options = get_option('barley_options');
    return $barley_options !== false ? $barley_options['barley_option_licensekey'] : $barley_options;
}


// Function to call the Barley License API
// Returns: Array with output from wp_remote_request or false in case of failure
function barley_license_api_call( $method=null, $barley_licensekey=null ) {

    // If license key not passed for this call, get it from DB
    if ( !$barley_licensekey ) {
        $barley_licensekey = barley_get_licensekey(); // Get Barley License Key from database.
    }
    
    if ( empty($barley_licensekey) ){
        // Retrieving license key from both params and DB failed
        // No need to call license API
        return false;
    }

    // Set up the call
    $barley_wp_api_call_options = array('method' => 'GET', 'timeout' => 20);
    $barley_wp_api_call_options['headers'] = array(
        'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
        'User-Agent' => 'WordPress/' . get_bloginfo("version"),
        'Referer' => get_bloginfo("url")
    );
    $barley_wp_api_request_url = BARLEY_WP_API_URL . '/' . $method . '?key=' . $barley_licensekey . '&host=' . BARLEY_WP_HOSTNAME . '&version=' . BARLEY_WP_VERSION;

    $barley_license_api_response = wp_remote_request($barley_wp_api_request_url, $barley_wp_api_call_options);

    if ( is_wp_error($barley_license_api_response) ){
        // API request resulted in error
        return false;
    } else{
        // Correct API response
        return $barley_license_api_response;
    }
}

?>