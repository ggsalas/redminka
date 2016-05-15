<?php

/**
* Plugin Name: [redminka] Mostrar posts antiguos
* Plugin URI: 
* Description: Muestra correctamente (con shortcodes y custom fields) a las publicaciones de la Versión 1 de redminka
* Version: 1.0
* Author: Gabriel
* Author URI: 
* License: GPL2
*/


//* Formato de publicaciones viejas ** LO MEJOR ES METER TODO ESTO EN UN PLUGIN********************/
// Esta funcion tambien lo usa el plugin "mostrar imagenes destacadas
function addhttp1($url) { // Corrige las direcciones insertadas por el usuario que no tienen http://  - http://stackoverflow.com/a/2762083
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}


	/* Agrego el formato de galería para mostrar bien los posts de redminka 1.0 (y poder borrar todas las categorías)*/
	add_theme_support('post-formats', array('gallery') );

add_action('genesis_before_entry_content', 'publicaciones_viejas');
function publicaciones_viejas(){ // media pila y darles formato... Bien Simple! porque nunca mas se van a utilizar.
	global $post;
	if (is_single()){
		$enlaces_link = get_post_meta( $post->ID, 'url_link', true );
		$video_embed =	get_post_meta($post->ID, 'url_video', true);
		$doc_archivo = get_post_meta($post->ID, 'subir_documento',true);

		if( $enlaces_link != '' ){
			$enlaces_link_corregido = addhttp1($enlaces_link);
			$enlaces_imagen = 'http://s.wordpress.com/mshots/v1/'.$enlaces_link_corregido.'?w=400';
			echo  '<a href="' . $enlaces_link_corregido . '" target="_blank"><img style="margin: 0 calc(50% - 200px) 15px ;" width="400" height="400" src="'.$enlaces_imagen.'" ></a>';
		
		} else if($video_embed != ''){
			echo wp_oembed_get( $video_embed ); // WP estandard

		} else if($doc_archivo != ''){
			$doc_link = wp_get_attachment_link( $doc_archivo);
			echo '<p>'.$doc_link. '</p>';
		}else if ( has_post_format('gallery')){
			$foto_galeria = 	do_shortcode('[gallery  type="rectangular" ]');	
			echo $foto_galeria;
		/*	NO HAY CASO, **para unir todas las categorías en 1 sola** TENDRE QUE AGREGAR [gallery] EN CADA POST :/
			Esto es para poder unir todos los post en una sola categoría "Artículos".
		*/
		}
	}
}


?>
