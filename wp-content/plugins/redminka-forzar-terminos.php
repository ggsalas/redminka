<?php
/**
* Plugin Name: [redminka] Forzar terminos y condiciones
* Plugin URI: http://premium.wpmudev.org/forums/topic/accept-terms-of-service-in-ultimate-facebook-connect
* Description: Forzar aceptar los terminos y condiciones utilizando Ultimate-Facebook
* Version: 1.0
* Author: Gabriel
* Author URI: http://premium.wpmudev.org/forums
* License: GPL2
*/

function my_fb_add_tos_box ($fields) {
	$fields[] = array(
		"name" => "tos",
		"description" => "Acepto los términos y condiciones http://redminka.com/tyc",	//"Acepto los <a href=\"#\"> Términos y Condiciones </a>",
		"type" => "checkbox",
		);
	return $fields;
	}
	add_filter('wdfb-registration_fields_array', 'my_fb_add_tos_box');
	
function my_fb_require_tos_box ($user_id, $he, $registration, $model) {
	if ($registration['tos']) return true;
	
	$model->delete_wp_user($user_id);
	wp_redirect(site_url("/error-terminos-condiciones"));
	die;
}
add_action("wdfb-user_registered-postprocess", "my_fb_require_tos_box", 10, 4);
?>