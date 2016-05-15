<?php

/**
* Plugin Name: [redminka] Tendencias
* Plugin URI: http://stackoverflow.com/questions/10264347/how-to-write-a-sql-query-to-display-list-of-most-used-tags-in-the-last-30-days-i
* Description: Busca tags que son tendencia y los agrega con un shortcode
* Version: 1.0
* Author: Gabriel
* Author URI: http://wordpress.org/support/topic/display-list-of-most-used-tags-in-the-last-30-days?replies=1
* License: GPL2
*/

function tendencias(){	
	// Variables
	$dias = 300;
	$N_tags = 9;

	global $wpdb;
	
	$term_ids = $wpdb->get_col("
		SELECT term_id FROM $wpdb->term_taxonomy
		INNER JOIN $wpdb->term_relationships ON $wpdb->term_taxonomy.term_taxonomy_id=$wpdb->term_relationships.term_taxonomy_id
		INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
		WHERE DATE_SUB(CURDATE(), INTERVAL $dias DAY) <= $wpdb->posts.post_date");
		
	if(count($term_ids) > 0){
		$tags = get_tags(array(
			'orderby' => 'count',
			'order'   => 'DESC',
			'number'  => $N_tags,
			'include' => $term_ids,
			));
					
		foreach ( (array) $tags as $tag ) {
		  	$return .= '<li><a href="' . get_tag_link ($tag->term_id) . '" rel="tag">' . $tag->name . '</a></li>';		   
		}
	}
	return '<ul class="tendencias">' . $return . '</ul>';
}

// Shortcode para mostrar las "TENDENCIAS" 
add_shortcode('tendencias', 'tendencias');

?>