<?php
/*
Plugin Name: [redminka] Cantidad de lecturas c/Jetpack
Plugin URI: http://ggsalas.com.ar
Description: Mostrar cantidad de lecturas de post usando jetpack
Author: GGsalas
Version: 1.0
Author URI: http://ggsalas.com.ar
*/

add_action( 'genesis_before_post', 'render_stats1' );
function render_stats1(){
	echo '<p>views:</p>'.render_stats($post->ID);
}

function render_stats($post) {
$args = array(
'days'=>-1,
'post_id'=>get_the_ID(), // !!Ojo, puede ser que no identifique el post desde plugin, este codigo en el functions.php funcionabe bien
);

$result = stats_get_csv('postviews', $args);

$views = $result[0]['views'];
return number_format_i18n($views);
}

?>