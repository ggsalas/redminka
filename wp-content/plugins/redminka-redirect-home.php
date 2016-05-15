<?php

/**
* Plugin Name: [redminka] Redirecciones
* Plugin URI: http://premium.wpmudev.org/forums/topic/redirect-homepage-to-another-for-logged-in-users
* Description: redireccionamientos para home y admin area
* Version: 1.0
* Author: Gabriel
* Author URI: http://premium.wpmudev.org/forums
* License: GPL2
*/

// Rediriir la pág. home a publicaciones para los usuarios logueados
add_action( 'template_redirect', 'logged_in_home_redirect' );
function logged_in_home_redirect() {
	$url = site_url();
    if( is_user_logged_in() && is_front_page() ) {
        wp_redirect( $url.'/publicaciones' );
        exit();
    }
}

//impedir ingreso al admin para todos, excepto administradores
// OJO: lo desactivo porque causaba problemas con Barley... agregué el plugin Dashboard Access Settings para reemplazar este código
// http://wordpress.stackexchange.com/a/14969
//add_action( 'admin_init', 'wpse_11244_restrict_admin', 1 ); 
function wpse_11244_restrict_admin() {
		global $wpdb;
		global $bp;
	//$url_mispublicaciones = bp_core_get_user_displaynames(bp_loggedin_user_id()). 'publicaciones/todas';
    if ( ! current_user_can( 'manage_options' )  && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php' ) {
		wp_redirect(get_site_url().'/miembros/'. $curauth->user_login .'/'); //wp_redirect( $url_mispublicaciones );
    }
}

// Redireccionar a los que NO son Miembros
add_filter('get_header','bp_guest_redirect',1); 
function bp_guest_redirect() {
	global $bp;
	$url = site_url();
	if (bp_is_current_component('members')) {
		// enter the slug or component conditional here
		if(!is_user_logged_in()) { // not logged in user
			wp_redirect( $url.'/registrarse' );
		} // user will be redirect to any link to want
	}
}

//redireccionar para los archivos del autor
add_action( 'template_redirect', 'authorblog_template_redirect' );
function authorblog_template_redirect(){
	if(is_author()){
		if(get_query_var('author_name')) :
		    $curauth = get_user_by('slug', get_query_var('author_name'));
		else :
		    $curauth = get_userdata(get_query_var('author'));
		endif;
		wp_redirect(get_site_url().'/miembros/'. $curauth->user_login .'/publicaciones/todas');
	}
}



?>
