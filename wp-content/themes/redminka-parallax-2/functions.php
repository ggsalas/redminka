<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'parallax', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'parallax' ) );

//* Add Image upload to WordPress Theme Customizer
add_action( 'customize_register', 'parallax_customizer' );
function parallax_customizer(){

	require_once( get_stylesheet_directory() . '/lib/customize.php' );
	
}

//* Include Section Image CSS
include_once( get_stylesheet_directory() . '/lib/output.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'redminka Parallax Pro 2' );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/parallax/' );
define( 'CHILD_THEME_VERSION', '1.2' );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles' );
function parallax_enqueue_scripts_styles() {

	wp_enqueue_script( 'parallax-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );

	wp_enqueue_style( 'dashicons' );

	// AGREGO LA FUENTE DE UBUNTU
	wp_enqueue_style( 'parallax-google-fonts', '//fonts.googleapis.com/css?family=Montserrat|Sorts+Mill+Goudy|Ubuntu:300,400,500,700', array(), CHILD_THEME_VERSION );

}

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_nav' );

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

//* Reduce the secondary navigation menu to one level depth
add_filter( 'wp_nav_menu_args', 'parallax_secondary_menu_args' );
function parallax_secondary_menu_args( $args ){

	if( 'secondary' != $args['theme_location'] )
	return $args;

	$args['depth'] = 1;
	return $args;

}

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Add support for additional color styles
add_theme_support( 'genesis-style-selector', array(
	'parallax-pro-blue'   => __( 'Parallax Pro Blue', 'parallax' ),
	'parallax-pro-green'  => __( 'Parallax Pro Green', 'parallax' ),
	'parallax-pro-orange' => __( 'Parallax Pro Orange', 'parallax' ),
	'parallax-pro-pink'   => __( 'Parallax Pro Pink', 'parallax' ),
) );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'width'           => 360,
	'height'          => 70,
	'header-selector' => '.site-title a',
	'header-text'     => false,
) );

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'subnav',
	'footer-widgets',
	'footer',
) );

//* Modify the size of the Gravatar in the author box
add_filter( 'genesis_author_box_gravatar_size', 'parallax_author_box_gravatar' );
function parallax_author_box_gravatar( $size ) {

	return 176;

}

//* Modify the size of the Gravatar in the entry comments
add_filter( 'genesis_comment_list_args', 'parallax_comments_gravatar' );
function parallax_comments_gravatar( $args ) {

	$args['avatar_size'] = 120;

	return $args;

}

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 1 );

//* Add support for after entry widget
add_theme_support( 'genesis-after-entry-widget-area' );

//* Relocate after entry widget
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
add_action( 'genesis_after_entry', 'genesis_after_entry_widget_area', 5 );

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-section-1',
	'name'        => __( 'Home Section 1', 'parallax' ),
	'description' => __( 'This is the home section 1 section.', 'parallax' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-section-2',
	'name'        => __( 'Home Section 2', 'parallax' ),
	'description' => __( 'This is the home section 2 section.', 'parallax' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-section-3',
	'name'        => __( 'Home Section 3', 'parallax' ),
	'description' => __( 'This is the home section 3 section.', 'parallax' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-section-4',
	'name'        => __( 'Home Section 4', 'parallax' ),
	'description' => __( 'This is the home section 4 section.', 'parallax' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-section-5',
	'name'        => __( 'Home Section 5', 'parallax' ),
	'description' => __( 'This is the home section 5 section.', 'parallax' ),
) );

//*** PERSONALIZACION PARA REDMINKA *******************************************
/**
* Add Favicon
* @author Bill Erickson Y Lynda
* @link http://www.billerickson.net/code/multiple-sizes-favicon/
* Y : http://stackoverflow.com/a/23851464
*/
add_action( 'wp_head', 'be_favicon' );
remove_action( 'wp_head', 'genesis_load_favicon' );
function be_favicon() {
// For IE 9 and below. ICO should be 32x32 pixels in size
	echo '<!--[if IE]><link rel="shortcut icon" sizes="32x32 64x64 128x128 256x256" href="' . get_stylesheet_directory_uri() . '/images/favicon.ico"> <![endif]-->';

// Touch Icons - iOS and Android 2.1+ 152x152 pixels in size
	echo '<link rel="apple-touch-icon-precomposed" href="' . get_stylesheet_directory_uri() . '/images/favicon-152.png">';	

// Firefox, Chrome, Safari, IE 11+ and Opera. 96x96 pixels in size
	echo '<link rel="icon" href="' . get_stylesheet_directory_uri() . '/images/favicon-96.png" type="image/png">';	
}


//* Mostrar - Ocultar la barra admin de wordpress
//add_filter('show_admin_bar', '__return_false');

//* Sacar título y descripción a páginas seleccionadas
add_action( 'get_header', 'remove_titulopaginas' ); 
function remove_titulopaginas(){
//	if ($bp->activity->name == $bp->current_component or $bp->members->name == $bp->current_component or $bp->profile->name == $bp->current_component or $bp->messages->name == $bp->current_component or $bp->notofications->name == $bp->current_component or $bp->settings->name == $bp->current_component){
	if (bp_is_current_component('activity') or bp_is_current_component('members') or bp_is_current_component('profile') or bp_is_current_component('messages') or bp_is_current_component('notifications') or bp_is_current_component('settings') ) { 
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
 		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
	}else if(bp_is_current_component('publicaciones')){
 		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
	}else{
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
		add_action( 'genesis_entry_header', 'genesis_do_post_title' );
 		add_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		add_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		add_action( 'genesis_entry_footer', 'genesis_post_meta' );
		add_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		add_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

	}
}

//* Imagenes tamaño personalizado
add_image_size( 'articulo-lateral', 300, 300, true );

//* Formatear los archivos

	// Formato del meta
	add_action( 'get_header', 'diferente_entry_footer' );
	function diferente_entry_footer(){
		if (is_archive() OR is_page(209) OR is_search()){
			add_filter( 'genesis_post_meta', 'entry_footer_archivos' );
		}else if (is_single()){
			add_filter( 'genesis_post_meta', 'entry_footer_single' );
		}
	}
		// Formato visible en los archivos
		function entry_footer_archivos($post_meta) {
			$post_meta = 'Por [post_author_posts_link before="<b>" after="</b>"]';
			return $post_meta;
		}

		// Formato visible en el single
		function entry_footer_single($post_meta) {
			$post_meta = '[post_tags sep=", " before="Etiquetado en: "]';
			return $post_meta;
		}

	// Agregar author box
	add_filter( 'get_the_author_genesis_author_box_single', '__return_true' );
	
		// Personlaizarlo: http://www.billerickson.net/code/customize-author-box/
		add_filter( 'genesis_author_box', 'redminka_author_box', 10, 6 );
		function redminka_author_box($output, $context, $pattern, $gravatar, $title, $description){
			// Author's Gravatar image
			$gravatar_size = apply_filters( 'genesis_author_box_gravatar_size', 70, $context );
			$gravatar = get_avatar( get_the_author_meta( 'email' ), $gravatar_size );
			 
			// Author's name
			$name = get_the_author();
			$title = get_the_author_meta( 'title' );
			$nombre_buddypress = get_the_author_meta( 'user_login' );
			if( !empty( $title ) )
			$name .= ', ' . $title;

			// Biografía
			global $bp;
			$args = array(
				'field'   => 32, // Field name or ID.
				'user_id' => get_the_author_meta( 'ID' )
				);
			$bio = '<div class="biografia">'.bp_get_profile_field_data( $args ).'</div>';
			 
			// Build Author box output
			$output = '';
			$output .= '<section class="author-box" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author">';
			$output .= '<a href="' . get_site_url() . '/miembros/' . $nombre_buddypress . '/profile/">' . $gravatar . '</a>';
			$output .= '<div class="author-box-title"><span itemprop="name"> 
						<a href="' . get_site_url() . '/miembros/' . $nombre_buddypress . '/profile/">'
						. $name . '&nbsp;(@' . $nombre_buddypress .')</span></a></div>';
			$output .= '<div itemprop="description" class="author-box-content">' . $bio  . '</div>';
			$output .= '</section>';
			return $output;
		}


	// Mover la imagen destacada antes del título
	remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
	add_action( 'genesis_entry_header', 'genesis_do_post_image', 9 );

	// Loongitud Excerpt genesis
	add_filter( 'excerpt_length', 'sp_excerpt_length' );
	function sp_excerpt_length( $length ) {
		return 15; // pull first 50 words
	}

	// Loongitud Excerpt buddypress
	add_filter('bp_activity_excerpt_length', 'px_bp_activity_excerpt_length');
	function px_bp_activity_excerpt_length() {
		return 180;
	}


	// Formato del post-info (abajo del titulo)
	add_action( 'get_header', 'diferente_post_info' );
	function diferente_post_info(){
		if (is_archive() OR is_page(209) OR is_search()){
			remove_action( 'genesis_before_post_content', 'genesis_post_info' );
		}else if (is_single()){
			add_filter( 'genesis_post_info', 'post_info_single' );
		}
	}
		// Formato visible en single
		function post_info_single($post_info) {
		if ( !is_page() ) {
		$post_info = '[post_date] por [post_author_posts_link before="<b>" after="</b>"] [post_comments zero="Comentar" one="(1 Comentario)" more="(% Comentarios)"] ';
		return $post_info;
		}}

add_action( 'get_header', 'formato_titulo_archivo' );
function formato_titulo_archivo(){
	if (is_archive() OR is_page(209) OR is_search() ){
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
	}	
}

//* Forzar layout en páginas de buddypress (las opciones de genesis no funcionan con buddypress)
add_filter( 'genesis_pre_get_option_site_layout', 'full_width_layout_registro' ); 
function full_width_layout_registro($opt) {
    if ((bp_is_current_component('members'))){ 
    	$opt = 'content-sidebar'; 
      	return $opt;
    }else if (is_archive() or is_search()){
    	$opt = 'sidebar-content'; 
      	return $opt;
	}
}

//* Ancho de la galería y los incrustados
if ( ! isset( $content_width ) )
    $content_width = 768;


//* Formato Buddypress Single Member
// Mostrar bio arriba, abajo la oculto con css y evito editar la plantilla de BP
add_action( 'bp_profile_header_meta', 'mostrar_bio' );
function mostrar_bio() {
    $args = array(
        'field'   => 32, // Field name or ID.
        );
    $bio = '<div class="biografia">'.bp_get_profile_field_data( $args ).'</div>';
 
    if ($bio) {
        echo $bio;
    }
}


//* Desactivar "favoritos" de Buddypress
add_filter( 'bp_activity_can_favorite', '__return_false' );
add_filter( 'bp_get_total_favorite_count_for_user', '__return_false' );

add_action( 'wp_before_admin_bar_render', 'bp_admin_bar_render_remove_favorites' );
function bp_admin_bar_render_remove_favorites() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('my-account-activity-favorites');
}

add_action( 'bp_setup_nav', 'quitar_submenu_favoritos', 15 );
function quitar_submenu_favoritos() {
bp_core_remove_subnav_item( 'activity', 'favorites' );
}

//* Pié de página
	add_filter( 'genesis_footer_creds_text', 'sp_footer_creds_text' );
	function sp_footer_creds_text() {
		echo '<div class="creds"><p>';
		echo 'SEGUINOS EN ';
		echo ' <a target="blank" id="twitter-footer" href="https://twitter.com/redminka">Twitter</a>';
		echo ' <a target="blank" id="facebook-footer" href="https://www.facebook.com/redminka">Facebook</a>';
		echo ' <a target="blank" id="linkedin-footer" href="http://www.linkedin.com/company/redminka">Linkedin</a>';
		echo ' <a target="blank" id="googleplus-footer" href="https://plus.google.com/103177985212936626798" rel="publisher">Google+</a>';
		echo ' &middot; Copyright &copy; ';
		echo date('Y');
		echo ' &middot; <a href="'.get_site_url().'">www.redminka.com</a>';
		if ( is_user_logged_in() ){
			echo ' &middot; <a href="'.get_site_url().'/contacto">Contacto</a>';
		}
		echo ' &middot; <a href="'.get_site_url().'/info">info</a>';
		echo '</p></div>';
	}

	// Agregar formulario de contacto
	add_action('genesis_entry_content', 'formulario_contacto');
	function formulario_contacto(){
		if (is_page('contacto'))
		echo do_shortcode('[si-contact-form form=1]');
	}

//*** Related posts Jetpack ***/
	// Reubicar

	add_filter( 'wp', 'jetpackme_remove_rp', 20 );
	function jetpackme_remove_rp() {
		$jprp = Jetpack_RelatedPosts::init();
		$callback = array( $jprp, 'filter_add_target_to_dom' );
		remove_filter( 'the_content', $callback, 40 );
	}

	add_action('genesis_after_entry','jetpack_relacionados',5);
	function jetpack_relacionados(){
		echo do_shortcode( '[jetpack-related-posts]' );
	}

	// Quitar texto explicativo
	add_filter( 'jetpack_relatedposts_filter_post_context', '__return_empty_string' );



//*** HERRAMIENTAS ********

//* Ver nombre de los componentes de buddypress
// add_action('genesis_before_header', 'test'); //Para ver el nombre de la página de buddypress a filtrar
function test(){
	echo bp_current_component();
}



//*****PRUEBAS PARA QUE FUNCIONE EL EDITOR DE POSTS DE WORDPRESS
//* Agregar imagen encabezado
add_action( 'genesis_before_entry', 'single_post_featured_image', 5 );

function single_post_featured_image($post_object) {
	if ( is_singular( 'post' ) ){
	echo get_the_post_thumbnail( $post_id, thumbnail,array(
		'class'	=> "destacada-post",
		));
	}
}

//* Formatear imagen y titulo encabezado 
//add_action( 'wp_enqueue_scripts', 'estilo_single' );
function estilo_single() {
	if ( is_singular('post' ) and has_post_thumbnail( $post_id ) ) {
   		wp_enqueue_style( 'estilo-single', get_stylesheet_directory_uri().'/estilo-single.css' );
	}
}

add_action( 'wp_head', 'estilo_single1' );
function estilo_single1(){
	if ( is_singular('post' ) and has_post_thumbnail( $post_id ) ) {
		$background = wp_get_attachment_image_src( get_post_thumbnail_id( $page->ID ), 'large' );
		echo'<style>
.entry-header::before{
			content:"";
			height: 100vh;
			left: 0;
			position: absolute;
			top: 0;
			width: 100%;
			margin: 0;
			padding:0;
			opacity: .4;
			background: linear-gradient(rgba(0,0,0,1), rgba(0,0,0,0)) ;
		}
		.entry-header {
			height: 100vh;
			left: 0;
			position: absolute;
			text-align: center;
			top: 0;
			width: 100%;
			background: url('.$background[0].');
			background-repeat: no-repeat;
			background-size: cover;
		}

		.entry-content {
			padding-top: 100vh;
		}

		.header-full-width .title-area{display: none;}

		.fee-on .entry-content {
			margin-top: 0;
			padding-top: 0;
		}

		.fee-on .fee-thumbnail{
			margin-top: 100vh; 
			border-top: 0px dashed #ccc;
			border-bottom: 1px dashed #ccc;
			border-left: 1px dashed #ccc;
			border-right: 1px dashed #ccc;	

		}

		.single-post .site-inner {
			margin-top: 0;
		}

		.entry-title{
			font-size: 5vw;
			padding: 0 10%;
			font-weight: 500;
			opacity: 1;
			color: #fff;
			text-shadow: 0 2px #000;
			position: absolute;
			text-align: center;
			top: 50%;
			transform: translateY(-50%);
			width: 100%;
		}

		.entry-header .entry-meta{
			background: none repeat scroll 0 0 rgba(0, 0, 0, 0.6);
			bottom: 0;
			color: #fff;
			opacity: 1;
			padding: 8px 15% 16px 15%;
			position: absolute;
			text-shadow: 0 1px #000;
			width: 100%;
		}
		.entry-header .entry-meta a{ color: #fff;}

		.fee-on .fee-thumbnail-wrap:before {
			color: #777;
			content: "Modificar imagen destacada";
			left: 180px;
			position: absolute;
			top: 60px;
			font-family: ubuntu;
			font-size: 18px;
		}


		</style>';
	}else {}
}


//* Para que no edite páginas
remove_post_type_support( 'page', 'front-end-editor' );

// solución al plugin de Janice
if (class_exists('WPE_Heartbeat_Throttle')) {
  function fix_fee_plugin_missing_hearbeat_scripts( $wpe_heartbeat_allowed_pages ) {
    $wpe_heartbeat_allowed_pages = array(
        'post-new.php',
        'index.php',
        'admin.php',
        'edit.php',
        'post.php',
    );
    return $wpe_heartbeat_allowed_pages;
   }
add_filter( 'wpe_heartbeat_allowed_pages', 'fix_fee_plugin_missing_hearbeat_scripts' );
}



/**
 * Solución provisoria al plugin Barley.
 * Requiere que los autores publiquen html sin filtrar, y eso pone en riesto el sitio
 */

// get the the role object
$rol_autor = get_role( 'author' );

// add $cap capability to this role object
$rol_autor->add_cap( 'unfiltered_html' );


