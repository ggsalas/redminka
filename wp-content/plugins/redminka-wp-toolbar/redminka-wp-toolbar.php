<?php

/**
* Plugin Name: [redminka] WP Toolbar
* Plugin URI: http://sumtips.com/2011/03/customize-wordpress-admin-bar.html
* Description: Nueva toolbar (adminbar) para utilizarla como barra principal de redminka
* Version: 1.0
* Author: Gabriel
* Author URI: 
* License: GPL2
*/

//* Deshabilitar metaboxes en el editor del backend. 
// OJO con esto porque trae problemas, por ejemplo: los posts salen con comentarios desactivados
//add_action('admin_init','customize_meta_boxes');
function customize_meta_boxes() {
  /* Removes meta boxes from Posts */
  remove_meta_box('postcustom','post','normal');
  remove_meta_box('trackbacksdiv','post','normal');
  remove_meta_box('commentstatusdiv','post','normal');
  remove_meta_box('commentsdiv','post','normal');
//  remove_meta_box('tagsdiv-post_tag','post','normal');
  remove_meta_box('postexcerpt','post','normal');
  /* Removes meta boxes from pages */
  remove_meta_box('postcustom','page','normal');
  remove_meta_box('trackbacksdiv','page','normal');
  remove_meta_box('commentstatusdiv','page','normal');
  remove_meta_box('commentsdiv','page','normal');  
}



//* Mostrar Header (genesis) solo en home y adminbar en las demas */
//add_action('wp_head', 'header_adminbar');
function header_adminbar(){
	if (is_front_page()){
		add_filter('show_admin_bar', '__return_false');
	} else {
		remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
		remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
	}
}

	//* Mostrar siempre! y remover header clasico de genesis
	add_action('wp_head', 'header_adminbar_siempre');
	function header_adminbar_siempre(){
			remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
			remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
	}


//* Quitar
function remove_admin_bar_links() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('updates');
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('updates');
	$wp_admin_bar->remove_menu('search');
	$wp_admin_bar->remove_menu('site-name');
	$wp_admin_bar->remove_menu('wpabr_ablh');
	$wp_admin_bar->remove_menu('stats');

	if ( !is_single() or is_attachment()){
		$wp_admin_bar->remove_menu('edit');
	}
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

function OXP_admin_bar_edit() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('new-page'); 
    $wp_admin_bar->remove_menu('new-media'); 
    $wp_admin_bar->remove_menu('new-link'); 
    $wp_admin_bar->remove_menu('new-user'); 
    $wp_admin_bar->remove_menu('new-theme');
    $wp_admin_bar->remove_menu('new-plugin');
}
add_action( 'wp_before_admin_bar_render', 'OXP_admin_bar_edit' );

//* Links
add_action('admin_bar_menu', 'boton_redminka',25);
function boton_redminka() {
	global $wp_admin_bar;
	if ( !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array(
	'id' 	=> 'redminka',
	'title' => __( 'redminka'),
	'href' 	=> __('/'),
	'meta'  => array( 
			'class' => 'boton_principal_redminka',
			)
	));

		// Submenu de redminka
		$wp_admin_bar->add_menu( array(
			'parent' => 'redminka',
			'id'     => 'publicaciones',
			'title' => __( 'Publicaciones'),
			'href' => __('/publicaciones'),
		));

		$wp_admin_bar->add_menu( array(
			'parent' => 'redminka',
			'id'     => 'Actividad',
			'title' => __( 'Actividad'),
			'href' => __('/actividad'),
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'redminka',
			'id'     => 'Miembros',
			'title' => __( 'Miembros'),
			'href' => __('/miembros'),
		));


		// Submenu de "add-new"
		$wp_admin_bar->add_menu( array(
			'parent' => 'new-content',
			'id'     => 'borradores',
			'title' => __( 'Ver Mis Borradores'),
			'href' => __('/borradores'),
		));

		// Submenu de "Barley add-new"
		$wp_admin_bar->add_menu( array(
			'parent' => 'barley-new-content',
			'id'     => 'borradores',
			'title' => __( '(Ver Mis Borradores)'),
			'href' => __('/borradores'),
		));
}

//add_action('admin_bar_menu', 'add_sumtips_admin_bar_link',25);
function add_sumtips_admin_bar_link() {
	global $wp_admin_bar;
	if ( !is_super_admin() || !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array(
	'id' => 'sumtips_link',
	'title' => __( 'SumTips Menu'),
	'href' => __('http://sumtips.com'),
	));

	// Add sub menu link "View All Posts"
	$wp_admin_bar->add_menu( array(
		'parent' => 'sumtips_link',
		'id'     => 'sumtips_all',
		'title' => __( 'View All Posts'),
		'href' => __('http://sumtips.com/all'),
	));

	// Add sub menu link "Downloads"
	$wp_admin_bar->add_menu( array(
		'parent' => 'sumtips_link',
		'id'     => 'sumtips_downloads',
		'title' => __( 'Downloads'),
		'href' => __('http://sumtips.com/downloads'),
		'meta'   => array(
			'class' => 'st_menu_download',),
	));
		$wp_admin_bar->add_menu( array(
			'parent' => 'sumtips_downloads',
			'id'     => 'sumtips_browsers',
			'title' => __( 'Browsers'),
			'href' => __('http://sumtips.com/downloads?category=3'),
		));
}

/* Remove the Admin Bar preference in user profile */
remove_action( 'personal_options', '_admin_bar_preferences' ); 

//* CSS
//add_action( 'admin_head', 'css_admin_bar' );
add_action( 'wp_head', 'css_admin_bar' );
function css_admin_bar() {
    wp_enqueue_style( 'style-toolbar', plugins_url('style-toolbar.css', __FILE__) );
}

/* Edicion del backend para autores */
add_action( 'admin_head', 'css_admin_author' );
function css_admin_author(){
	if ( current_user_can( 'publish_posts' ) and !current_user_can( 'publish_pages' ) ){
	echo '<style type="text/css">

	#adminmenuback, #adminmenuwrap{ display: none;}
	div#wpcontent {
	max-width: 1000px;
	margin: 10px auto !important;
	}
	
	</style>';
	}
}
?>
