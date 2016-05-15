<?php

/**
* Plugin Name: [redminka] Borrar Post
* Plugin URI: http://premium.wpmudev.org/forums/topic/get_delete_post_link-post_id-redirects-to-a-404-page#post-532757
* Description: Borrar un artículo desde Frontend
* Version: 1.0
* Author: Gabriel
* Author URI: 
* License: GPL2
*/

// Borrar publicación 

add_action( 'wpuf_edit_post_form_bottom', 'wpufe_edit_delete', 10, 2 );
function wpufe_edit_delete( $form_id, $post_id ) {
	$deletepostlink= add_query_arg( 'frontend', 'true', get_delete_post_link( $post_id ) );
	if (current_user_can('edit_post', $post_id)) {
		echo	   '<p style="text-align:right"><a class="button small borrar-publicacion" onclick="return confirm(\'¿Estás seguro de borrar esta publicación?\')" href="'.$deletepostlink.'">Borrar</a></p>';
	}
}

// backup para hacerlo desde formulario de frontendplugin: http://pastebin.com/M12am2s7 pero no funciona :P
function borrar_post(){ //Esto es para poner el link desde la lista de posts, me parece mejor dentro de editar post
	global $post;
	$deletepostlink= add_query_arg( 'frontend', 'true', get_delete_post_link( get_the_ID() ) );
	if (current_user_can('edit_post', $post->ID)) {
		echo	   '<span><a class="post-delete-link" onclick="return confirm(\'¿Estás seguro de borrar esta publicación?\')" href="'.$deletepostlink.'">Borrar</a></span>';
	}
}

//Redireccionar despues de borrar
add_action('trashed_post','trash_redirection_frontend');
function trash_redirection_frontend($post_id) {
    if ( filter_input( INPUT_GET, 'frontend', FILTER_VALIDATE_BOOLEAN ) ) {
        wp_redirect( get_option('siteurl').'/publicacion-eliminada' );
        exit;
    }
}



?>