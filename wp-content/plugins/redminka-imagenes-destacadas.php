<?php
/**
 * Plugin Name: [redminka] Imagenes destacadas
 * Plugin URI: http://ggsalas.com.ar	
 * Description: URL de las imagenes destacadas por post y categoría en el custom-field "img_categoria" y en el OG para facebook
 * Author: ggsalas
 * Author URI: http://ggsalas.com.ar
 * Version: 0.2
 */
 
/*	Hacer:
**	- img por defecto si no encuentra ninguna
** - img video para "vimeo"
*/

 // Img destacada por categoría
add_action( 'wpuf_add_post_after_insert', 'function_img_cat');
add_action( 'wpuf_edit_post_after_update', 'function_img_cat');
function function_img_cat($post_id){
	$theme_url = "http://www.redminka.com/wp-content/themes/redminkaepik";		
	// Búsqueda de la primera imágen adjunta
		$args = array(
			'numberposts' => 1,
			'order' => 'ASC',
			'post_mime_type' => 'image',
			'post_parent' => $post_id,
			'post_status' => null,
			'post_type' => 'attachment',
		);
		$attachments = get_children( $args );
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' )  ? wp_get_attachment_image_src( $attachment->ID, 'thumbnail' ) : wp_get_attachment_image_src( $attachment->ID, 'full' );
						$img_cat = wp_get_attachment_thumb_url( $attachment->ID );
			} 
			} else {
						$img_cat = $theme_url.'/images/logo-redminka-600.png';
		}	
	
	// ahora designo una imagen por categoría
	if ( in_category('notas', $post_id) ) {
		$url_imagen = $theme_url.'/img/relatedposts_nota.png';	
	}else	if  ( in_category('enlaces', $post_id) ) {
		$enlaces_link = get_post_meta( $post_id, 'url_link', true );
		$enlaces_link_corregido = addhttp2($enlaces_link);
		$url_imagen = 'http://s.wordpress.com/mshots/v1/'.$enlaces_link.'?w=400';			
	}else	if ( in_category('fotos', $post_id) ) {
		$url_imagen = $img_cat;
	}else	if ( in_category('videos', $post_id) ) {
		$video_embed =	get_post_meta($post_id, 'url_video', true);
		$url_imagen = 'http://img.youtube.com/vi/' .substr($video_embed, -11). '/0.jpg';
	}else	if ( in_category('documentos', $post_id) ) {
		$url_imagen = $theme_url.'/images/logo-redminka-600.png';
	}else	if ( in_category('articulos', $post_id) ) {
		$url_imagen = $img_cat;
	}else {
		if (has_post_thumbnail($post->ID )){
			$url_imagen = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
		}
}
	
update_post_meta($post_id, 'img_categoria', $url_imagen);
}

/* Funciones para insertar en el content */
/* Esta función ya esté en el plugin "mostrar posts antiguos" le cambié el nombre y listo!! */
function addhttp2($url) { // Corrige las direcciones insertadas por el usuario que no tienen http://  - http://stackoverflow.com/a/2762083
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}


// Imágenes para facebook
add_action('genesis_meta', 'fb_img_enlaces');
function fb_img_enlaces($post_id){
	global $post;
	$theme_url = "http://www.redminka.com/wp-content/themes/redminkaepik";
	
		// Búsqueda de la primera imágen adjunta
		$args = array(
			'numberposts' => 1,
			'order' => 'ASC',
			'post_mime_type' => 'image',
			'post_parent' => get_the_ID(),
			'post_status' => null,
			'post_type' => 'attachment',
		);
		$attachments = get_children( $args );
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'medium' )  ? wp_get_attachment_image_src( $attachment->ID, 'medium' ):'true';
				$img_cat = $image_attributes[0];
			} 
			} else {
				$img_cat = $theme_url.'/img/redminka-logo-250.png';
		}
		
	$img_featured = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium');
	echo	'<meta property="fb:app_id" content="566866693374391">
			<meta property="og:locale" content="es_ES">';
			
	if ( in_category('notas') ) {
		echo		'<meta property="og:image" content="'.$theme_url.'/images/logo-redminka-600.png"/>';		
	}else	if  ( in_category('enlaces') ) { // Parece que encuentra la imagen solo, por eso está todo comentado
		$enlaces_link = get_post_meta( $post->ID, 'url_link', true );
		$enlaces_link_corregido = addhttp2($enlaces_link);
		$enlaces_imagen = 'http://s.wordpress.com/mshots/v1/'.$enlaces_link.'?w=400';
		echo		'<meta property="og:image" content="' . $enlaces_imagen . '"/>';					
	}else	if ( in_category('fotos') ) {
		echo		'<meta property="og:image" content="' . $img_cat . '"/>';				
	}else	if ( in_category('videos') ) {
		$video_embed =	get_post_meta($post->ID, 'url_video', true);
		echo		'<meta property="og:image" content="http://img.youtube.com/vi/' .substr($video_embed, -11). '/0.jpg"/>
					<meta property="og:type" content="video">';						
	}else	if ( in_category('documentos') ) {
		echo		'<meta property="og:image" content="'.$theme_url.'/images/logo-redminka-600.png"/>';
	}else	if ( in_category('articulos') ) {
		echo		'<meta property="og:image" content="' . $img_cat . '"/>';				
	}else {
		if (has_post_thumbnail( $post->ID ) ){
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' ); 
		}
		echo '<meta property="og:image" content="' . $image[0]. '"/>';
	}
}

// Para que jetpack no me agregue una imagen en blanco si no env¿cuentra nada
function jeherve_custom_image( $media, $post_id, $args ) {
    if ( empty( $media ) ) {
        $permalink = get_permalink( $post_id );
        $url = apply_filters( 'jetpack_photon_url', '' );
        //http://www.redminka.com/wp-content/themes/redminkaepik/img/redminka-logo-200.png
     
        return array( array(
            'type'  => 'image',
            'from'  => 'custom_fallback',
            'src'   => esc_url( $url ),
            'href'  => $permalink,
        ) );
    }
}
add_filter( 'jetpack_images_get_images', 'jeherve_custom_image', 10, 3 );

// Imagen para pag home
function fb_home_image() {
    $fb_home_img = 'http://www.redminka.com/wp-content/themes/redminkaepik/images/logo-redminka-600.png';
    $fb_home_img_output = sprintf( '<meta property="og:image" content="%s" />', esc_attr( $fb_home_img ) );
    if ( is_home() )
        echo $fb_home_img_output;
}
add_action( 'wp_head', 'fb_home_image' );

?>
