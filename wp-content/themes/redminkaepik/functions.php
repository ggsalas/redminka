<?php
// Start the engine
require_once( get_template_directory() . '/lib/init.php' );

// Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'redminkaepik' );
define( 'CHILD_THEME_URL', 'http://ggsalas.com.ar' );

// Add Viewport meta tag for mobile browsers
add_action( 'genesis_meta', 'epik_viewport_meta_tag' );
function epik_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
}

// Create additional color style options
add_theme_support( 'genesis-style-selector', array( 
	'epik-black'	=>	__( 'Black', 'epik' ), 
	'epik-blue' 	=>	__( 'Blue', 'epik' ),
	'epik-darkblue' =>	__( 'Dark Blue', 'epik' ),  
	'epik-gray' 	=> 	__( 'Gray', 'epik' ), 
	'epik-green' 	=> 	__( 'Green', 'epik' ),
	'epik-orange' 	=> 	__( 'Orange', 'epik' ), 
	'epik-pink' 	=> 	__( 'Pink', 'epik' ),
	'epik-purple' 	=> 	__( 'Purple', 'epik' ), 
	'epik-red' 		=> 	__( 'Red', 'epik' ), 
) );

// Add support for custom background
add_theme_support( 'custom-background' );

// Add support for custom header
add_theme_support( 'genesis-custom-header', array(
	'width' => 1152,
	'height' => 120
) );

// Add new image sizes 
add_image_size( 'featured-img', 706, 441, TRUE );
add_image_size( 'featured-page', 276, 140, TRUE );
add_image_size( 'portfolio-thumbnail', 264, 200, TRUE );

// Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array( 'header', 'nav', 'subnav', 'inner', 'footer-widgets', 'footer' ) );

// Before Header Wrap
add_action( 'genesis_before_header', 'before_header_wrap' );
function before_header_wrap() {
	echo '<div id="head-wrap">';
}

// After Header Wrap
add_action( 'genesis_after_header', 'after_header_wrap' );
function after_header_wrap() {
	echo '</div>';
}

// Reposition the Secondary Navigation
remove_action( 'genesis_after_header', 'genesis_do_subnav' ) ;
add_action( 'genesis_before_header', 'genesis_do_subnav' );

// Customize search form input box text
add_filter( 'genesis_search_text', 'custom_search_text' );
function custom_search_text($text) {
    return esc_attr( 'Search...' );
}

add_action( 'admin_menu', 'epik_theme_settings_init', 15 ); 
/** 
 * This is a necessary go-between to get our scripts and boxes loaded 
 * on the theme settings page only, and not the rest of the admin 
 */ 
function epik_theme_settings_init() { 
    global $_genesis_admin_settings; 
     
    add_action( 'load-' . $_genesis_admin_settings->pagehook, 'epik_add_portfolio_settings_box', 20 ); 
} 

// Add Portfolio Settings box to Genesis Theme Settings 
function epik_add_portfolio_settings_box() { 
    global $_genesis_admin_settings; 
     
    add_meta_box( 'genesis-theme-settings-epik-portfolio', __( 'Portfolio Page Settings', 'epik' ), 'epik_theme_settings_portfolio',     $_genesis_admin_settings->pagehook, 'main' ); 
}  
	
/** 
 * Adds Portfolio Options to Genesis Theme Settings Page
 */ 	
function epik_theme_settings_portfolio() { ?>
	
	<p><?php _e("Exclude the following Category IDs:", 'genesis'); ?><br />
	<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_cat_exclude]" value="<?php echo esc_attr( genesis_get_option('epik_portfolio_cat_exclude') ); ?>" size="40" /><br />
	<small><strong><?php _e("Comma separated - 1,2,3 for example", 'genesis'); ?></strong></small></p>
	
	<p><?php _e('Number of Posts to Show', 'genesis'); ?>:
	<input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_cat_num]" value="<?php echo esc_attr( genesis_option('epik_portfolio_cat_num') ); ?>" size="2" /></p>
	
	<p><span class="description"><?php _e('<b>NOTE:</b> The Portfolio Page displays the "Portfolio Page" image size plus the excerpt or full content as selected below.', 'epik'); ?></span></p>
	
	<p><?php _e("Select one of the following:", 'genesis'); ?>
	<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_content]">
		<option style="padding-right:10px;" value="full" <?php selected('full', genesis_get_option('epik_portfolio_content')); ?>><?php _e("Display post content", 'genesis'); ?></option>
		<option style="padding-right:10px;" value="excerpts" <?php selected('excerpts', genesis_get_option('epik_portfolio_content')); ?>><?php _e("Display post excerpts", 'genesis'); ?></option>
	</select></p>
	
	<p><label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_content_archive_limit]"><?php _e('Limit content to', 'genesis'); ?></label> <input type="text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_content_archive_limit]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_content_archive_limit]" value="<?php echo esc_attr( genesis_option('epik_portfolio_content_archive_limit') ); ?>" size="3" /> <label for="<?php echo GENESIS_SETTINGS_FIELD; ?>[epik_portfolio_content_archive_limit]"><?php _e('characters', 'genesis'); ?></label></p>
	
	<p><span class="description"><?php _e('<b>NOTE:</b> Using this option will limit the text and strip all formatting from the text displayed. To use this option, choose "Display post content" in the select box above.', 'genesis'); ?></span></p>
<?php
}	

// Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );
		
// Register widget areas
genesis_register_sidebar( array(
	'id'			=> 'slider',
	'name'			=> __( 'Slider', 'epik' ),
	'description'	=> __( 'This is the slider section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'welcome-wide',
	'name'			=> __( 'Welcome Wide', 'epik' ),
	'description'	=> __( 'This is the Wide (full width) section of the Welcome area.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'welcome-feature-1',
	'name'			=> __( 'Welcome Feature #1', 'epik' ),
	'description'	=> __( 'This is the first column of the Welcome feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'welcome-feature-2',
	'name'			=> __( 'Welcome Feature #2', 'epik' ),
	'description'	=> __( 'This is the second column of the Welcome feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'welcome-feature-3',
	'name'			=> __( 'Welcome Feature #3', 'epik' ),
	'description'	=> __( 'This is the third column of the Welcome feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-1',
	'name'			=> __( 'Home Feature #1 (Left)', 'epik' ),
	'description'	=> __( 'This is the first column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-2',
	'name'			=> __( 'Home Feature #2 (Right)', 'epik' ),
	'description'	=> __( 'This is the second column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-3',
	'name'			=> __( 'Home Feature #3 (Gray)', 'epik' ),
	'description'	=> __( 'This is the 3rd column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-4',
	'name'			=> __( 'Home Feature #4 (White)', 'epik' ),
	'description'	=> __( 'This is the 4th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-5',
	'name'			=> __( 'Home Feature #5 (Dark Gray)', 'epik' ),
	'description'	=> __( 'This is the 5th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-6',
	'name'			=> __( 'Home Feature #6 (White)', 'epik' ),
	'description'	=> __( 'This is the 6th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-7',
	'name'			=> __( 'Home Feature #7 (Gray)', 'epik' ),
	'description'	=> __( 'This is the 7th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-8',
	'name'			=> __( 'Home Feature #8 (White)', 'epik' ),
	'description'	=> __( 'This is the 8th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-9',
	'name'			=> __( 'Home Feature #9 (Gray)', 'epik' ),
	'description'	=> __( 'This is the 9th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-10',
	'name'			=> __( 'Home Feature #10', 'epik' ),
	'description'	=> __( 'This is the 10th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-11',
	'name'			=> __( 'Home Feature #11', 'epik' ),
	'description'	=> __( 'This is the 11th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-12',
	'name'			=> __( 'Home Feature #12', 'epik' ),
	'description'	=> __( 'This is the 12th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-13',
	'name'			=> __( 'Home Feature #13', 'epik' ),
	'description'	=> __( 'This is the 13th column of the feature section of the homepage.', 'epik' ),
) );
genesis_register_sidebar( array(
	'id'			=> 'home-feature-14',
	'name'			=> __( 'Home Feature #14 (White)', 'epik' ),
	'description'	=> __( 'This is the 14th column of the feature section of the homepage.', 'epik' ),
) );

// AGREGADOS GABI -------------------------------------------------------------------------------

// Modificaciones al menú de Perfil		--comienzo
function my_test_setup_nav() {
	global $bp;
	// Change the order of menu items
	$bp->bp_nav['notifications']['position'] = 100;

	// Remove a menu item
	$bp->bp_nav['blogs'] = false;
	// Change name of menu item
	if ( is_user_logged_in() ) { 
		// $bp->bp_nav['groups']['name'] = ‘community’;	
		$bp->bp_nav['settings']['name'] = 'Configuración';	
		$bp->bp_nav['messages']['name'] = 'Mensajes';	
		$bp->bp_nav['notifications']['name'] = 'Notificaciones';	
	}

	// Menú PUBLICACIONES 
	// NAV
	// name, slug, screen, position, default subnav
	
	bp_core_new_nav_item( array( 
		'name' => __( 'Publicaciones', 'buddypress'),
		'slug' =>  'publicaciones/todas' ,
		'parent_url' => $bp->displayed_user->domain ,
		'screen_function' => 'funcion_tab_publicaciones', 
		'position' => 40,
		'default_subnav_slug' => 'todas',
		//current selected, css class
	) );
	
	// SUBNAV
	// name, slug, parent_url, parent slug, screen function
	bp_core_new_subnav_item( array( 
		'name' => __( 'Todas', 'buddypress' ),  
		'slug' => 'todas', 
		'parent_url' => $bp->displayed_user->domain . 'publicaciones/'  ,
		'parent_slug' => 'publicaciones',
		'screen_function' => 'funcion_tab_publicaciones',
	) ); 
	
	bp_core_new_subnav_item( array( 
		'name' => __( 'Borradores', 'buddypress' ), 
		'slug' => 'borradores', 
		'parent_url' => $bp->displayed_user->domain . 'publicaciones/' ,
		'parent_slug' => 'publicaciones',
		'user_has_access' => ( bp_is_my_profile() || is_super_admin() ),
		'screen_function' => 'funcion_tab_borradores',
	) ); 
}

function funcion_tab_publicaciones() {
	//add title and content here - last is to call the members plugin.php template
	//add_action( 'bp_template_title', 'funcion_tab_publicaciones_title' );
	add_action( 'bp_template_content', 'funcion_tab_publicaciones_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

//function funcion_tab_publicaciones_title() {echo 'Mis Publicaciones'; }

function funcion_tab_publicaciones_content() {
	global $post;
	// arguments, adjust as needed
	$args = array(
		'post_type'      => 'post',
//		'posts_per_page' => 3,
		'post_status'    => 'publish',
		'author'			  => bp_displayed_user_id(),
		//'parent_slug'	  => $bp->displayed_user->domain.'publicaciones/todas',
		'paged'          => get_query_var( 'paged' )
	);

		global $wp_query;
		$wp_query = new WP_Query( $args );

//	    query_posts(array(
//                'author' => bp_displayed_user_id()
//        ));
		do_action('genesis_loop');
     	wp_reset_query();
}

function funcion_tab_borradores(){
	add_action( 'bp_template_content', 'funcion_tab_borradores_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function funcion_tab_borradores_content(){
	global $post; $current_user;
	// arguments, adjust as needed
	$args = array(
		'post_type'      => 'post',
//		'posts_per_page' => 3,
		'post_status'    => 'draft',
		'author'			  => bp_displayed_user_id(),
		'paged'          => get_query_var( 'paged' )
	);

	global $wp_query;
	$wp_query = new WP_Query( $args );
 
	if ( have_posts() ) : 
		echo '<table class="lista-posts-editar" cellspacing="0">';
		while ( have_posts() ) : the_post(); 
 
			echo  '<tr> <td><strong>' . get_the_title() . '</td><td style="text-align:right"> <a class="button small" href="' . get_edit_post_link() . '">Editar</a></td>' ;
 
		endwhile;
		echo '</table>';
		do_action( 'genesis_after_endwhile' );
	endif;
 
	wp_reset_query();	
	
//	query_posts(array(
//		'author' => bp_displayed_user_id(),
//		'post_status' => 'draft',
//	  	));
//	do_action('genesis_loop');
//	wp_reset_query();
//	echo do_shortcode('[display-posts author="' . the_author_meta(bp_loggedin_user_id()) . '" post_status="draft" posts_per_page="20"]');	
}
add_action( 'bp_setup_nav', 'my_test_setup_nav' );

// Shortcode para agregar lista de Borradores en la nueva página de publicar
add_shortcode('funcion_tab_borradores_content', 'testa');
function testa(){

$output = funcion_tab_borradores_content();
return $output;
	}
// Modificaciones al menú de Perfil		--fin
//
//
// Menú Principal completo			--comienzo

add_action( 'genesis_header_right', 'menu_principal' );
function menu_principal() {
    require(CHILD_DIR.'/hook-header.php');
}
// Menú Principal completo			--fin
//
//
// Sistema "Publicar" en blog				--comienzo

/** Register widget areas */
genesis_register_sidebar( array(
    'id'            => 'publicar-tabs',
    'name'          => __( 'Publicar tabs', 'themename' ),
    'description'   => __( 'Donde poner las tabs con los forms para publicar', 'themename' ),
) );

/** agregar el widget debajo del menu!! (antes: genesis_before_loop ) **/
// add_action( 'genesis_after_header', 'accion_tabs_publicar' );
function accion_tabs_publicar() {
	$menu_usuario=bp_core_get_user_domain(bp_loggedin_user_id());
	if (is_page( 'editar-publicacion' ) or is_page( 'publicar' ) or is_page_template('page_post_article.php')) { /* la pág. editar no debe tener los form. de crear posts */
	}else{
		echo '<div id="publicarform" class="after-post widget-area"> <div class="wrap">
		
					<div id="tabs"> <h3> ¿Qué podés compartir hoy? </h3>
						<ul>
						<li><a href="#tabs-1">Artículo</a></li>
						<li><a href="#tabs-2">Enlace</a></li>
						<li><a href="#tabs-3">Fotos</a></li>
						<li><a href="#tabs-4">Video</a></li>
						<li><a href="#tabs-5">Documento</a></li>
						</ul>
					<div class="publicar-tabs">
						<div id="tabs-1" class="tab-publicar">' . do_shortcode('	[wpuf_form id="130"]') .'
						<h3> Borradores</h3>';
						echo	funcion_tab_borradores_content();
						echo	'	
						</p>
						</div>					
						<div id="tabs-2" class="tab-publicar">' . do_shortcode('	[wpuf_form id="13"]') .'
						</div>
						<div id="tabs-3" class="tab-publicar">' . do_shortcode('	[wpuf_form id="19"]') .'
						</div>
						<div id="tabs-4" class="tab-publicar">' . do_shortcode('	[wpuf_form id="16"]') .'
						</div>
						<div id="tabs-5" class="tab-publicar">' . do_shortcode('	[wpuf_form id="17"]') .'
						</div>

					</div>
					</div>
		
				</div> </div>';
				
//      genesis_widget_area( 'publicar-tabs', array(
//         'before' => '<div id="publicarform" class="after-post widget-area"> <div class="wrap">',
//          'after' => '</div> </div>'
//      )	);
   }
}  

//Mensaje ¡Publicaste tu artículo! con link
add_filter( 'wpuf_add_post_redirect', 'wpufe_response_text', 10, 2 );
add_filter( 'wpuf_edit_post_redirect', 'wpufe_response_text', 10, 2 );
function wpufe_response_text( $response, $post_id ) {
    $response['message'] = str_replace( '%PERMALINK%', get_permalink( $post_id ), $response['message'] );
    return $response;
}

//Link a Mis publicaciones con un Shortcode (para la página de "Borraste tu post")
add_shortcode('mispublicaciones', 'link_mis_publicaciones');
function link_mis_publicaciones() {
	 $link_bp_mispublicaciones= bp_core_get_user_domain(bp_loggedin_user_id()).'publicaciones/todas' ;
    return $link_bp_mispublicaciones;
}


// Sistema "Publicar" en blog				--fin
//
//
// Formato post+autor+meta						--comienzo

add_action('genesis_before_post_title', 'agregar_divs_post_comienzo');
add_action('genesis_after_post', 'agregar_divs_post_fin');
add_action('genesis_post_content', 'agregar_divs_post_fin_content');
function agregar_divs_post_comienzo(){
	global $post;
	if ( in_category(array('notas','enlaces', 'fotos', 'videos', 'documentos', 'articulos' )) ) {
		echo '<div class="caja-autor">';
		echo 	'<a class="avatar" href="' 
				. bp_core_get_user_domain(get_the_author_meta( 'ID' )) . 
				'">'
				. get_avatar( get_the_author_meta( 'user_email' ), '50' ) .
				'</a>';				
		echo do_shortcode('[post_date format="d/m" before="<p>" after="</p>"]');
		echo do_shortcode('[post_date format="Y"]');	
		echo edit_post_link('Editar', '<span>', '</span>');
//		echo do_shortcode('[post_edit before="<span class=button>" after="</span>" link="Editar"]');
//		echo borrar_post();
		echo '</div> <div class=post-contenido>';
		echo '<div class=post-contenido-margen>';
			// Un test nomas... echo 	get_post_meta($post->ID, 'img_categoria', true); // borrador
		echo formato_posts_inicio();
	}else {
	}
}
function agregar_divs_post_fin_content(){
	if ( in_category(array('notas', 'enlaces', 'fotos', 'videos', 'documentos', 'articulos' )) ) {
		echo formato_posts_fin() . '</div>'; //fin del post-contenido-margen

	}
}	
function agregar_divs_post_fin(){
	if ( in_category(array('notas', 'enlaces', 'fotos', 'videos', 'documentos', 'articulos' )) ) {
		echo '</div>';
	}	
}

/* META */
// Modificar el Meta
function comments_meta1(){
$num_comments = get_comments_number(); // get_comments_number returns only a numeric value

if ( comments_open() ) {
	if ( $num_comments == 0 ) {
		$comments = __('No Comments');
	} elseif ( $num_comments > 1 ) {
		$comments = $num_comments . __(' Comments');
	} else {
		$comments = __('1 Comment');
	}
	$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
} else {
	$write_comments =  __('Comments are off for this post.');
}	
	}

add_filter( 'genesis_post_meta', 'post_meta_filter' );
function post_meta_filter($post_meta) {
	global $post;
	$comentarios_nr = get_comments_number();
	if ( $comentarios_nr == 1 ) :
		$comentarios_txt = '1 Comentario';
	elseif ( $comentarios_nr > 1 ) :
		$comentarios_txt = $comentarios_nr . ' Comentarios';
	else :
		$comentarios_txt = 'Comentarios';
	endif;
	
	$comentarios_link = get_comments_link( $post->ID );
	$test_comments = '<span class="post-comments"><a href="' . $comentarios_link . '">' . $comentarios_txt . '</a></span>';
	$meta_tags = '[post_tags sep="" before=""]';
	$facebook_share = 	'<a  class="post-share" href="#" 
								onclick="window.open(
									\'https://www.facebook.com/sharer/sharer.php?u='.get_permalink( $post->ID ).'\', 
									\'facebook-share-dialog\', 
									\'width=626,height=436 \'); 
									return false;">
								 </a> ';
	$clear = '<div style="clear: both;"></div>';

	
	return '<div class="post-etiquetas">' . $meta_tags . '</div> <div class="post-compartir">' . $test_comments . $facebook_share . ' </div>' . $clear;
}

// Formato post+autor+meta						--fin
//
//
// Formato de posts 							--comienzo

add_theme_support('post-formats', array( 'aside', 'gallery', 'link', 'video', 'status' ) );

// Armado de posts con Custom Fields			--comienzo
	
/* Funciones para insertar en el content */
function addhttp($url) { // Corrige las direcciones insertadas por el usuario que no tienen http://  - http://stackoverflow.com/a/2762083
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

//add_action( 'genesis_post_content', 'custom_field_in_content' );
function formato_posts_inicio(){	//custom_field_in_content() {
	global $post;

	if ( in_category('notas') ) {
		echo notas($post->ID);
	}else	if  ( in_category('enlaces') ) {
		echo enlaces($post->ID);
	}else	if ( in_category('fotos') ) {
		echo fotos($post->ID);
	}else	if ( in_category('videos') ) {
		echo video($post->ID);
	}else	if ( in_category('documentos') ) {
		echo documento($post->ID);
	}else	if ( in_category('articulo') ) {
		echo documento($post->ID);
	}else {
	}	
}

function notas($post_id){

	return '<div class="post-contenido-nota">';
}
function enlaces($post_id){
	$enlaces_link = get_post_meta( $post_id, 'url_link', true );
	$enlaces_link_corregido = addhttp($enlaces_link);
	$enlaces_imagen = 'http://s.wordpress.com/mshots/v1/'.$enlaces_link_corregido.'?w=400';
	$enlaces_imagen_insertar =  '<a href="' . $enlaces_link_corregido . '" target="_blank"><img style="float:left;" width="400" height="400" src="'.$enlaces_imagen.'" ></a>';
	return '<div class="one-half first">' . $enlaces_imagen_insertar . '</div> <div class="one-half">';
}
function fotos($post_id){
	$foto_galeria = 	do_shortcode('[gallery columns="5" ]');	
	return $foto_galeria.$related_posts;
}
function video($post_id){
	$video_embed =	get_post_meta($post_id, 'url_video', true);
	$video_insertar = wp_oembed_get( $video_embed	 ); // WP estandard
	return '<div class="video-container">'.$video_insertar.'</div>';
}
function documento($post_id){
	$doc_archivo = get_post_meta($post_id, 'subir_documento',true);
	$doc_link = wp_get_attachment_link( $doc_archivo);
	$doc_boton = '<p>'.$doc_link. '</p>';
	return '<div class="one-third first">' . $doc_boton . '</div> <div class="two-thirds">';
}

function formato_posts_fin(){	//custom_field_in_content() {
	global $post;
	if ( in_category('notas') ) {
		echo '</div>';
	}else	if  ( in_category('enlaces') ) {
		echo '</div>';
	}else	if ( in_category('fotos') ) {

	}else	if ( in_category('videos') ) {

	}else	if ( in_category('documentos') ) {
		echo '</div>';
	}else	if ( in_category('articulo') ) {

	}else {
	}	
}

// Varios		--comienzo
// Sacar título y descripción a páginas seleccionadas
//if ( is_page())
//	remove_action( 'genesis_before_post_content', 'genesis_post_info' );
	
add_action( 'get_header', 'remove_titulopaginas' );
function remove_titulopaginas(){
	if (bp_is_user()) { 
		remove_action( 'genesis_post_title', 'genesis_do_post_title' );
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
		//remove_action( 'genesis_after_post_content', 'genesis_post_meta' ); //la saco con css
	}else if (bp_is_directory()){
		add_action( 'genesis_post_title', 'genesis_do_post_title' );
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
	}else{
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
//		add_action( 'genesis_post_title', 'genesis_do_post_title' );
	}
}
/* Borrar titulo y post-info para home.php */
add_action( 'genesis_before_post', 'borrar_titulo' );
function borrar_titulo(){
	global $post;
	if ( in_category(array('notas', 'enlaces', 'documentos', 'videos')) ) {
		add_action( 'genesis_post_title', 'genesis_do_post_title' );
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
	}else if ( has_category(array('fotos') ))  {
		add_action( 'genesis_post_title', 'genesis_do_post_title' );
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );
	}
}

/* Remove featured image from certain posts.
*/
add_action( 'genesis_before_post' , 'borrar_featured_img' );
function borrar_featured_img(){
global $post;
remove_action( 'genesis_post_content', 'genesis_do_post_image' );		
	if( in_category(6, $post->ID)){
		remove_action( 'genesis_before_post_content', 'genesis_do_post_image');		
	} else {
		add_action( 'genesis_before_post_content', 'genesis_do_post_image');		
	}
}


// Agregar featured image en single.php de la categoría Articulos
//add_action('genesis_after_post_title', 'featured_single'); //No lo necesito porque ahora las imagenes se adjuntan al post
function featured_single(){
	global $post;
	if (is_single() and in_category('articulos') and has_post_thumbnail()){
		$img_articulos = the_post_thumbnail('featured-img', array(
			'class' => "featured_image_single",
			'alt'   => $post->post_title,
			'title' => $post->post_title,
			));
	return	 $img_articulos;	
	}else {}
}

// Compartir!! para los autores del post
//add_filter( 'genesis_before_post', 'autor_compartir' );
function autor_compartir(){
	global $post,$current_user;
	get_currentuserinfo();
	if ($post->post_status == 'publish' and $post->post_author == $current_user->ID and is_single()){
		echo '<div class="compartir-facebook">';
		echo	'<p style="text-align:justify;">';
		echo	the_author_meta('display_name', bp_loggedin_user_id());
		echo	', esta publicación se compartió en la <a href="http://facebook.com/redminka" target="_blank">página de facebook de redminka</a>.
				Desde aquí podés republicarla en tu biografía, tus grupos, tus páginas o en un mensaje privado de facebook.
				</p>';
		echo '<p style="text-align:right"><a class="button medium white"  href="#" 
					onclick="window.open(
						\'https://www.facebook.com/sharer/sharer.php?u=\'+encodeURIComponent(location.href), 
						\'facebook-share-dialog\', 
						\'width=626,height=436 \'); 
						return false;">
				Republicar en facebook</a> </p></div>';
//		echo '<a class="boton boton-compartir-facebook"  href="#" 
//					onclick="window.open(
//						\'https://www.facebook.com/sharer/sharer.php?u=\'+encodeURIComponent(location.href), 
//						\'facebook-share-dialog\', 
//						\'width=626,height=436 \'); 
//						return false;">
//				Republicar en facebook</a></div>';
	}
}

// Footer
add_filter( 'genesis_footer_creds_text', 'custom_footer_creds_text' );
function custom_footer_creds_text() {
echo '<div class="creds"><p>';
echo 'Copyright &copy; ';
echo date('Y');
echo ' &middot; <a href="redminka.com">www.redminka.com</a> ';
echo '</p></div>';
}
add_filter( 'genesis_footer_backtotop_text', 'custom_footer_backtotop_text' );
function custom_footer_backtotop_text($backtotop) {
	$backtotop = '<ul class="footer-links">
							<li><a target="blank" id="facebook-footer" href="https://www.facebook.com/redminka">Facebook</a></li>
							<li><a target="blank" id="twitter-footer" href="https://twitter.com/redminka">Twitter</a></li>
							<li><a target="blank" id="linkedin-footer" href="http://www.linkedin.com/company/redminka">Linkedin</a></li>
							<li><a target="blank" id="googleplus-footer" href="https://plus.google.com/103177985212936626798" rel="publisher">Google plus</a></li>
						</ul>'; //[footer_backtotop text="Ir Arriba"]';
	return $backtotop;
}


// PROVISORIO para que el contenido del SINGLE sea igual que el de los ARCHIVES
//add_action( 'genesis_post_content', 'custom_field_in_content' );

// BORRAR ADMINBAR
add_filter('show_admin_bar', '__return_false');


// Ancho de la galería y los incrustados
if ( ! isset( $content_width ) )
    $content_width = 609;

// Login logo + background
function my_login_logo() { ?>
    <style type="text/css">
			body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri()?>/img/login-logo.png);
            background-size: 274px auto;
            margin-bottom: 0;
        	}
			body.login { background: #009B77;	}
			.login #nav a, .login #backtoblog a{
				color: #ffffff !important;
				text-shadow: 0 0px 0 #222222 !important;		
				font-weight: bold;
				}
			.login #nav a:hover, .login #backtoblog a:hover{
				color: #D8A729 !important;
				}
			.login #nav, .login #backtoblog{
				margin: 0 !important;
				text-align: center;		
				background-color: #009B77;		
				}
			.login h1 a{
				width: 274px;
				}
			/*a:hover, a:active{color: #D8A729 !important;	} */
			.login #loginform{
				padding-bottom: 72px !important;
				}
			.wp-core-ui .button-primary{
				background: #009B77 !important;	
				border:0px solid #009B77 !important;
				box-shadow: 0 0 0 #009B77  !important;
				/*font-size: 16px !important; 
				height: 44px !important;
				margin-bottom: 1rem !important;
				line-height: 16px !important;
				margin-top: 0.5rem;
				padding: 0 20px !important; */
				}
			.wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus, .wp-core-ui .button-primary:active{
				background: #D8A729 !important;	
				border: 0px solid #D8A729 !important;
				}
			#facebook-btn-wrap {
			   display: inline-block !important;
			   margin-bottom: 16px !important;
    			}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'redminka - Red Social Sustentable';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

//Ocultar comentarios para borradores
add_action( 'wp_enqueue_scripts', 'custom_remove_comments' );
function custom_remove_comments() {
	global $post;
	if (have_posts() and $post->post_status == 'draft'){
		remove_action( 'genesis_after_post', 'genesis_get_comments_template' );
	}
}

// Para que el widget "text" ejecute shortcodes
add_filter('widget_text', 'do_shortcode');


// Varios		--fin
//
//
// Sidebars condicionales 					--comienzo

//Registro de sidebars
genesis_register_sidebar( array(
    'id' => 'sidebar_usuarios',
    'name' => 'sidebar USUARIOS',
    'description' => 'Sidebar para la página de usuarios - http://www.redminka.com/usuarios',
) );

genesis_register_sidebar( array(
    'id' => 'sidebar_perfil',
    'name' => 'sidebar PERFIL',
    'description' => 'Sidebar para la página de perfiles - http://www.redminka.com/usuarios/*NOMBRE-USUARIO*',
) );

/* //en este caso uso la sidebar predeterminada
genesis_register_sidebar( array(
    'id' => 'sidebar-publicaciones',
    'name' => 'sidebar PUBLICACIONES',
    'description' => 'Sidebar para el archivo de publicaciones, categorias y tags - http://www.redminka.com/publicaciones + categorias + tags ',
) );
*/

// Carga de sidebars segun condiciones
add_action( 'get_header', 'child_sidebar_logic' );
// @link http://dreamwhisperdesigns.com/?p=1034
function child_sidebar_logic() {
    if (bp_is_user()) { // Check if we're on a single post for my CPT called "jobs"
        remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); //remove the default genesis sidebar
        add_action( 'genesis_sidebar', 'child_get_blog_sidebar_perfil' ); //add an action hook to call the function for my custom sidebar
    }else if(bp_current_component('members') ) {
        remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); //remove the default genesis sidebar
        add_action( 'genesis_sidebar', 'child_get_blog_sidebar_usuarios' ); //add an action hook to call the function for my custom sidebar   	
 	}else if (bp_current_component('register')){
        remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); 
        remove_action( 'genesis_sidebar', 'child_get_blog_sidebar_usuarios' ); 		
        remove_action( 'genesis_sidebar', 'child_get_blog_sidebar_perfil' ); 	
    }else{
    //	add_action( 'genesis_sidebar', 'genesis_do_sidebar' );
    	}
} 
// Retrieve blog sidebar
function child_get_blog_sidebar_usuarios() {
	dynamic_sidebar( 'sidebar_usuarios' );
}
function child_get_blog_sidebar_perfil() {
	dynamic_sidebar( 'sidebar_perfil' );
}

//add_action('genesis_before_header', 'test'); //Para ver el nombre de la página de buddypress a filtrar
function test(){
echo bp_current_component();
}

// Sidebars condicionales 					--fin
//
//
// TRADUCCIONES y adaptaciones ------------

// Agregar nombre de usuario en la pág. de perfil
add_filter( 'bp_before_member_header_meta', 'nombre_perfil' );
function nombre_perfil(){
	echo '<h2>' . bp_get_displayed_user_fullname() . '</h2>';
	}

// Ocultar "more" si el contenido tiene menos de 250 caracteres
add_filter( 'get_the_content_more_link', 'child_read_more_link' );
function child_read_more_link() {
$content = apply_filters('the_content',get_the_content());
   if ( strlen($content) < 250 ) {  // Max 250 characters
      return '';
   } else {
	return '<a class="more-link " href="' . get_permalink() . '">Leer más...</a>';
   }
}

/** Forzar full width layout on registro de usuarios solamente*/
add_filter( 'genesis_pre_get_option_site_layout', 'full_width_layout_registro' );
function full_width_layout_registro($opt) {
    if (bp_is_user()){ 
    	$opt = 'content-sidebar'; 
      return $opt;
    }else if ( (bp_current_component() == 'members')){ 
    	$opt = 'content-sidebar'; 
      return $opt;
    }else if (bp_current_component() == 'register' or bp_current_component() == 'activate'){
    	$opt = 'full-width-content'; 
      return $opt;
    }
}

//* Cargar CSS para Home
add_action( 'wp_enqueue_scripts', 'custom_load_custom_style_sheet' );
function custom_load_custom_style_sheet() {
	if (is_home()){
			wp_enqueue_style( 'custom-stylesheet', CHILD_URL . '/style-home.css', array(), PARENT_THEME_VERSION );
	}
}

/** Remove Edit Link */
add_action( 'get_header', 'borrar_edit_paginas' );
function borrar_edit_paginas(){
	if (is_page( array(120, 10) ) ){ //Tengo que agregar de a una, no me anda poner: todas las pag.
			add_filter( 'edit_post_link', '__return_false' );	
	}
}

// Shortcode login with facebook
add_shortcode('facebook_login', 'shortcode_facebook_login');
function shortcode_facebook_login() {
	 $facebook_login= jfb_output_facebook_btn();
    return  $facebook_login;
}

// Botón Ingresar con facebook desde la pág. de registro
add_action( 'bp_before_register_page', 'loginfacebook_register_page');
function loginfacebook_register_page(){
	echo jfb_output_facebook_btn();
	}
	
// Google Analytics
add_action( 'wp_head', 'google_analytics' );
function google_analytics(){
	if (!is_page(120)){
		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		var pluginUrl = 
		 '//www.google-analytics.com/plugins/ga/inpage_linkid.js';
		_gaq.push(['_require', 'inpage_linkid', pluginUrl]);
		  _gaq.push(['_setAccount', 'UA-45912371-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>		
		<?php
	}
}

// Alerta para usuarios de exploradores obsoletos (IE 11 es el único IE que funciona bien con el plugin WPUF y la carga de imágenes)
add_action( 'genesis_before_header', 'browser_check');
function browser_check(){
	if(is_user_logged_in() && is_ie() && get_browser_version() <= 10) { 
		echo '<div class="notificacion-top">Parece que estás utilizando un navegador obsoleto que no permite acceder a todas las funcionalidades de redminka. Por favor, <a href="http://browsehappy.com/" target="_blank">utilizá otro navegador o actualizalo</a></div>';
	};
}

// Feedback
add_action('genesis_footer', 'feedback');
function feedback(){
	 if(is_user_logged_in() && !is_page(120)){
	 	echo '<a href="#FSContact1" id="feedback">Contacto</a>';
	 	echo  do_shortcode('[si-contact-form form=1]');
	 }	
}

// Corregir los links del perfil
//http://irz.fr/tag/bp_get_the_profile_field_value/
add_filter( 'bp_get_the_profile_field_value','modified_fields');
function modified_fields($field_value) {
	$bp_this_field_name = bp_get_the_profile_field_name();
	// remplacer 'Vidéo' par le champ que vous souhaitez customiser
	if($bp_this_field_name=='Facebook') {
		$field_value = strip_tags( $field_value );
		$field_value = '<a href="'.$field_value.'">'.$field_value.'</a>';
	}else if($bp_this_field_name=='Sitio Web') {
		$field_value = strip_tags( $field_value );
		$field_value = '<a href="'.$field_value.'">'.$field_value.'</a>';
	}
	return $field_value;
}

// Agregar y modificar estilos de TinyMCE (the WYSIWYG editor). Function finds stylesheet from the root of the current theme's folder.
add_theme_support('editor_style');
add_editor_style('tinymce-style.css');


/* Add Editor Style */
add_filter( 'mce_css', 'my_plugin_editor_style' );
function my_plugin_editor_style( $mce_css ){

 $mce_css .= ', ' . 'http://ggsalas.staging.wpengine.com/wp-content/themes/redminkaepik/tinymce-style.css';
    return $mce_css;
}

// Borrar algunos botones de tinyMCE
function custom_disable_mce_buttons( $opt ) {
    $opt['theme_advanced_disable'] = 'wp_more, justifyfull,forecolor, strikethrough, underline, pasteword,';
    return $opt;
}
add_filter('tiny_mce_before_init', 'custom_disable_mce_buttons');  

// Borrar el texto "protectes" de las pág privadas
add_filter( 'private_title_format', 'aubreypwd_remove_protected' );
add_filter( 'protected_title_format', 'aubreypwd_remove_protected' );
function aubreypwd_remove_protected( $format ) {
    return '%s';
}

// Menu de la pagina "publicar"
genesis_register_sidebar( array(
    'id'            => 'menu-template-publicar',
    'name'          => __( 'Menu Publicar', 'themename' ),
    'description'   => __( 'Menu para el template de pagina "publicar"', 'themename' ),
) );

?>
