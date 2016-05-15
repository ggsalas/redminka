<?php

/**
* Plugin Name: [redminka] Lista Borradores
* Plugin URI: 
* Description: Busca tags que son tendencia y los agrega con un shortcode
* Version: 1.0
* Author: Gabriel
* Author URI: 
* License: GPL2
*/

function mostrar_borradores(){
	global $post; $current_user;
	$args = array(
		'post_type'      => 'post',
//		'posts_per_page' => 3,
		'post_status'    => 'draft',
		'author'			  => get_current_user_id(),
		'paged'          => get_query_var( 'paged' )
	);

	global $wp_query;
	$wp_query = new WP_Query( $args );
 
	if ( have_posts() ) : 
		echo '<table class="lista-posts-editar" cellspacing="0">';
		while ( have_posts() ) : the_post(); 
 
			echo  '<tr> <td style="text-align: center;"> <a href="'. get_permalink() .'">'. get_the_title() . '</a></td>' ;
 
		endwhile;
		echo '</table>';
		do_action( 'genesis_after_endwhile' );
	endif;
 
	wp_reset_query();	

}

// Agregar en página "borradores"
add_action('genesis_entry_content', 'pagina_borradores');
function pagina_borradores(){
	if (is_page('borradores')){
	return mostrar_borradores();
	}
}






//* Buddupress: Pestaña Publicaciones
// http://stackoverflow.com/a/20590260/2903800
add_action( 'bp_setup_nav', 'bp_content_setup_nav' );
function bp_content_setup_nav() {
global $bp;

bp_core_new_nav_item( array(
    'name'                  => __('Publicaciones', 'buddypress'),
    'slug'                  => 'publicaciones',
    'screen_function'       => 'redirigir_todas',
    'position'              => 30,//weight on menu, change it to whatever you want
    'default_subnav_slug'   => '/',
) );
/* No puedo hacer andar la subnav con la paginación de genesis*/
    bp_core_new_subnav_item( array(
        'name'                  => __( 'Todas', 'buddypress' ),
        'slug'                  => 'todas',
        'parent_url'            => trailingslashit( bp_loggedin_user_domain() . 'publicaciones/' ),
        'parent_slug'           => 'publicaciones',
        'screen_function'       => 'mis_publicaciones',
        'position'              => 10//again, weight but for submenu
    ) );

    bp_core_new_subnav_item( array(
        'name'                  => __( 'Borradores', 'buddypress' ),
        'slug'                  => 'borradores',
        'parent_url'            => trailingslashit( bp_loggedin_user_domain() . 'publicaciones' ),
        'parent_slug'           => 'publicaciones',
        'screen_function'       => 'mis_borradores',
		'user_has_access' 		=> ( bp_is_my_profile() || is_super_admin() ),
        'position'              => 20//again, weight but for submenu
    ) );


do_action( 'bp_content_setup_nav' );
}

function mis_publicaciones() {
    add_action( 'bp_template_title', 'mis_publicaciones_title' );
    add_action( 'bp_template_content', 'mis_publicaciones_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function mis_publicaciones_title() {}

function mis_publicaciones_content() {
//    get_template_part( 'directory-to-content-file' );
	global $post;

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'author'		 => bp_displayed_user_id(),
		'paged'          => get_query_var( 'paged' )
	); 

		global $wp_query;
		$wp_query = new WP_Query( $args );

               do_action( 'genesis_before_loop' );
                do_action( 'genesis_loop' );
                do_action( 'genesis_after_loop' );
     	wp_reset_query();
}

function mis_borradores() {
//    add_action( 'bp_template_title', 'mis_borradores_title' );
    add_action( 'bp_template_content', 'mostrar_borradores' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function redirigir_todas(){
	$url_todas= bp_displayed_user_domain() .'publicaciones/todas';
	wp_redirect($url_todas);
}



?>
