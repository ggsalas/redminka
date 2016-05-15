<?php

/**
* Plugin Name: [redminka] Single Feature Image
* Plugin URI: http://sridharkatakam.com/medium-like-full-view-featured-image-genesis/
* Description: Personalización para que el single post tenga una feature image fullsize (como medium.com)
* Version: 1.0
* Author: Gabriel
* Author URI: 
* License: GPL2
*/

//* Relocate Post Title and Post Info
add_action( 'genesis_after_header', 'sk_relocate_post_title_info' );
function sk_relocate_post_title_info() {

	if ( is_singular('post' ) and has_post_thumbnail( $post_id ) ) {

		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

		echo '<div class="featured-single"><div class="wrap">';
			genesis_entry_header_markup_open();
			genesis_do_post_title();
			genesis_post_info();
			genesis_entry_header_markup_close();
		echo '</div></div>';
	}
}

//* Enqueue site-wide scripts
add_action( 'wp_enqueue_scripts', 'custom_load_scripts' );
function custom_load_scripts() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'header-fade', get_bloginfo( 'stylesheet_directory' ) . '/js/header-fade.js', array( 'jquery' ), '1.0.0', true );
}

//* Enqueue scripts on single Post pages
add_action( 'wp_enqueue_scripts', 'enqueue_singular' );
function enqueue_singular() {
	if ( is_singular('post' ) and has_post_thumbnail( $post_id ) ) {

		// to add a dynamically-resized background image to .featured-single
		wp_enqueue_script( 'backstretch',  get_stylesheet_directory_uri() . '/js/jquery.backstretch.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'backstretch-init',  get_stylesheet_directory_uri() . '/js/backstretch-init.js', array( 'backstretch' ), '1.0.0', true );

		// if the post has a featured image, send it to Backstretch else use a default one
		if ( has_post_thumbnail() ) {
			wp_localize_script( 'backstretch-init', 'BackStretchImg', array( 'src' => wp_get_attachment_url( get_post_thumbnail_id() ) ) );
		}
		else {
//				wp_localize_script( 'backstretch-init', 'BackStretchImg', array( 'src' => 'http://genesis.dev/wp-content/themes/genesis-sample/images/picjumbo.com_IMG_6533-1600.jpg' ) );
		}

		// for smooth scrolling when the down arrow is clicked
		wp_enqueue_script( 'scrollTo', get_stylesheet_directory_uri() . '/js/jquery.scrollTo.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'localScroll', get_stylesheet_directory_uri() . '/js/jquery.localScroll.min.js', array( 'scrollTo' ), '', true );

		// for setting the height of 'Featured Single' section so its background fills the viewport, adding the down arrow, and setting smooth scrolling speed
		wp_enqueue_script( 'singular', get_bloginfo( 'stylesheet_directory' ) . '/js/singular-scripts.js', array( 'jquery' ), '1.0.0', true );

		// for fading away Post title and info when scrolling down and fading in when scrolling up
		wp_enqueue_script( 'fade-on-scroll', get_stylesheet_directory_uri() . '/js/fade-on-scroll.js', array( 'scrollTo' ), '1.0.0', true );

	}
}

//* Add a ID to .site-inner
add_filter( 'genesis_attr_site-inner', 'custom_attributes_content' );
function custom_attributes_content( $attributes ) {
	if ( is_singular('post' ) and has_post_thumbnail( $post_id )) {
		$attributes['id'] = 'site-inner';
	}
	return $attributes;
}



?>
