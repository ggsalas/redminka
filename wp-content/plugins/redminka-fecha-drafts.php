<?php
/**
 * Plugin Name: [redminka] Actualizar fecha de publicación para los drafts
 * Plugin URI: http://ggsalas.com.ar	
 * Description: Actualiza la fecha (fecha de publicación) los "drafts" (borradores)
 * Author: ggsalas
 * Author URI: http://ggsalas.com.ar
 * Version: 0.2
 */
/// Parece ser que wordpress asigna la fecha de los artículos cuando se crean.
/// Esto es un problema si uno comeinza un borrador y lo publica 1 mes después porque
/// la fecha con la que sale publicado es la de su creación. Este plugin actualiza a la 
/// fecha de Publicación.
/// http://biostall.com/update-published-date-when-going-from-draft-to-published-in-wordpress

add_action( 'draft_to_publish', 'my_update_published_date' );

function my_update_published_date( $post ) {  
	// Update the post  
	$my_post = array(  
		'ID'            => $post->ID,  
		'post_date'     => date("Y-m-d H:i:s"),  
		'post_date_gmt'     => date("Y-m-d H:i:s")  
	);  

	// Update the post into the database  
	wp_update_post( $my_post );  
}    
 
?>
