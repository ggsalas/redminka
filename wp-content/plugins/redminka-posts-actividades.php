<?php

/**
* Plugin Name: [redminka] Posts Actividades
* Plugin URI: https://buddypress.org/support/topic/custom-activity-new-post-content-not-broken-after-update-the-post/#post-211883
* Description: Personalización de las actividades para las publicaciones + Agrega comentarios como nuevas actividades
* Version: 2.0
* Author: Gabriel
* Author URI: 
* License: GPL2
*/


/* También se pueden crear y personalizar las actividades para custompost types
http://buddypress.org/support/topic/display-custom-post-types-in-activity-feed/#post-137101
http://buddypress.org/support/topic/another-custom-post-type-in-activity-stream/#post-160005
http://buddypress.org/support/topic/multiple-custom-post-types-in-activity-stream/

... y los comentarios también
- http://www.mrova.com/blog/buddypress-custom-activity-stream-string-for-comments-on-custom-post-type-quick-tip/
*/


/*
	Grabar actividades de custom post types
*/
//add_filter ( 'bp_blogs_record_post_post_types', 'activity_publish_custom_post_types',1,1 );
function activity_publish_custom_post_types( $post_types ) {
	$post_types[] = 'post';
	return $post_types;
}

//add_filter('bp_blogs_activity_new_post_action', 'record_cpt_activity_action', 10, 3);
function record_cpt_activity_action( $activity_action, $post, $post_permalink ) {
	global $bp;

	if( $post->post_type == 'post' ) {
		$activity_action = sprintf( __( '%1$s publicó un nuevo artículo: %2$s', 'buddypress' ), bp_core_get_userlink( (int) $post->post_author ), '' . $post->post_title . '' );	
	}
	return $activity_action;
}

/**
 * Modificar el contenido de las actividades de las publicaciones
 */

add_filter('bp_get_activity_content_body', 'icondeposit_bp_activity_entry_meta1');
function icondeposit_bp_activity_entry_meta1($content) {
	if (bp_get_activity_object_name() == 'blogs' && bp_get_activity_type() == 'new_blog_post'){
		$post_id =  bp_get_activity_secondary_item_id();
		$content_post = get_post($post_id);
		$content = $content_post->post_content;
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		$content = strip_tags($content);
		$trimmed_content = wp_trim_words( $content, 40, '<a class="boton" style="float:right" href="'. get_permalink($post_id ) .'"> Leer +</a>' );
		$content= 	'<a href="' . get_permalink( $post_id ) . '" class="activity-content-title" >' 
					. get_the_post_thumbnail( $post_id, 'articulo-lateral') 
					. get_the_title($post_id) . '</a>'
					. $trimmed_content ;
		} else if(bp_get_activity_type() == 'activity_comment' || bp_get_activity_type() == 'new_blog_comment'){}

return $content;
}


/**************************************************************************************************
 * COMENTARIOS
 *************************************************************************************************/

/**
 * Buddypress: Crear actividades para nuevos comentarios Creados En La Actividad
 * https://buddypress.org/support/topic/blog-comments-not-appearing-in-activity-stream/#post-186094
 */
add_filter( 'bp_ajax_querystring', 'bpfr_stream', 20, 2 );
function bpfr_stream( $qs, $object ) {
	if ( 'activity' != $object ) {
		return $qs;
	}
	$qs = wp_parse_args( $qs, array() );
	$qs['display_comments'] = 'stream';
	return $qs;
}


/**
 * Buddypress: Crear actividades para nuevos comentarios Creados En El Post
 * https://buddypress.org/support/topic/blog-post-comments-not-update-activity-comments/#post-218766
 *
 * When a new comment gets added to the database, add this comment to the
 * activity stream
 *
 ** 2015-01-07 Debo desactivar toda esta personalización porque crea comentarios duplicados. Parece que buddypress ahora funciona bien
 ** Muy raro, pero es así. En el sitio de pruebas (staging) buddypress no crea los comentarios por eso no se ven duplicados... Más raro aún

add_action('comment_post', 'bca_record_activity', 10, 2); //comment_post is triggered "just after a comment is saved in the database".
function bca_record_activity($comment_id, $approval) {
    if($approval == 1) {
        $comment = get_comment($comment_id);
        $userlink = bp_core_get_userlink($comment->user_id);
        $postlink = '<a href="' . get_permalink($comment->comment_post_ID) . '">' 
                        . get_the_title($comment->comment_post_ID) . '</a>';

        bp_activity_add(array(
            'action'            => sprintf( __( '%1$s hizo un nuevo comentario en: %2$s', 'buddypress' ), 
                                                $userlink, $postlink),
            'content'           => $comment->comment_content,
            'component'         => 'bp_plugin',
            'user_id'           => $comment->user_id,
            'type'              => 'activity_comment',
    		'primary_link'      => get_comment_link( $comment ), // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink)

        ));
    }
}

 **/

/*
 * We want activity entries of blog comments to be shown as "mini"-entries
 *
 ** 2015-01-07 A partir de acá lo dejo para la compatibilidad con comentarios viejos (muy poquitos :P )
 **/

	add_filter('bp_activity_mini_activity_types', 'bca_minify_activity');
	function bca_minify_activity($array) {
		$array[] = 'new_blog_comment';
		$array[] = 'activity_comment';
		return $array;
	}

	/*
	 * Disables comments on this type of activity entry
	 */
	add_filter('bp_activity_can_comment', 'bca_remove_commenting');
	function bca_remove_commenting($can_comment) {
		if($can_comment == true) {
			$can_comment = ! ('new_blog_comment' == bp_get_activity_action_name());
		}
		return $can_comment;
	}

	/*
	 * Agregar boton "Ver conversación"
	 * http://bp-tricks.com/snippets/adding-buttons-to-activity-stream-entries/
	 */
	add_action('bp_activity_entry_meta', 'my_bp_activity_entry_meta');
	function my_bp_activity_entry_meta() {
		$comment = get_comment($comment_id);
		if ( bp_get_activity_type() == 'new_blog_comment' ) {
			?>
		    <a class="button view bp-secondary-action" style="float: left" href="<?php bp_activity_thread_permalink() ?>">Ver Conversación</a>
			<?php 
		}
	}

?>
