<?php
// Remove default WordPress Admin Bar New Content Menu
function barley_remove_admin_bar(){
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('new-content');
	$wp_admin_bar->remove_menu('edit');
}
add_action( 'wp_before_admin_bar_render', 'barley_remove_admin_bar' );

// Add New Barley-Powered WordPress Admin Bar New Content Menu
function barley_add_admin_bar() {

	global $wp_admin_bar, $current_user, $post, $barley_options, $barley_editor_currently_on;
	$barley_action_url = home_url().'/?barley_action=';

	// Barley Editor is currently off, show menu option to turn on
	// And thats it.
	if ( !$barley_editor_currently_on ) {
	
		$wp_admin_bar->add_menu( array(
		    'id'   => 'barley-editor-on',
		    'meta' => array(),
		    'title' => __('Turn Barley Editor On','barley-for-wordpress'),
		    'href' => $_SERVER["HTTP_HOST"] . str_replace($_SERVER["REQUEST_URI"],'?barley_editor=off','').'?barley_editor=on'
		) );

		return;
	}

	// Main Menu: "+ New" default action: New Standard Blog Post
	$wp_admin_bar->add_menu( array(
	    'id'   => 'barley-new-content',
	    'meta' => array(),
	    'title' => __('+ Nueva','barley-for-wordpress'),
	    'href' => $barley_action_url.'new-blogpost'
	) );

/*(g) Desactivo Drafts de barley
	if ( !is_admin() ) { // Don't do this while in the Admin
		// Query WordPress Database for current posts drafts
		$barley_post_count = wp_count_posts();
		// Query WordPress Database for current page drafts
		$barley_page_count = wp_count_posts('page');

		// Total number of drafts for posts and pages.
		$barley_number_of_drafts = ($barley_post_count->draft+$barley_page_count->draft);
		
		if ( $barley_number_of_drafts > 0 ) { // If there are drafts, show drafts button

			// Main Menu: "View Drafts" opens draft modal
			$wp_admin_bar->add_menu( array(
			    'id'   => 'barley-view-drafts',
			    'meta' => array(),
			    'title' => __('View Drafts ('.$barley_number_of_drafts.')','barley-for-wordpress'),
			    'href' => '#'
			));
		}
	}
*/


	// Sub-menu: Blog Post default action: New Standard Blog Post
	$wp_admin_bar->add_menu( array(
	      'parent' => 'barley-new-content',
	      'id' => 'new-blogpost',
	      'title' => __('Blog Post','barley-for-wordpress'),
	      'href' => $barley_action_url.'new-blogpost',
	      'meta' => array()
	) );


	if ( current_theme_supports('post-formats') ) {

		$barley_post_formats = get_theme_support( 'post-formats' ); // list of post formats supported by current theme

    	if ( is_array( $barley_post_formats[0] ) ) {
        	foreach ( $barley_post_formats[0] as $barley_format ) {
        		// Sub-menu: Post Format default action: New Post Format Post
        		$wp_admin_bar->add_menu( array(
			      'parent' => 'barley-new-content',
			      'id' => 'new-'.ucfirst($barley_format),
			      'title' => ucfirst($barley_format),
			      'href' => $barley_action_url.'new-postformat&format='.strtolower($barley_format),
			      'meta' => array()
			  ) );
        	} // endforeach
    	} // and if is_array

    } // end if theme supports post-formats

    // Custom Post Formats
    $barley_custom_post_types = get_post_types( array(
    	'public'   => true,
    	'_builtin' => false), 'names');

    if ( is_array($barley_custom_post_types) ) {
    	foreach ( $barley_custom_post_types as $barley_post_type=>$barley_post_type_name ) {
        		// Sub-menu: Post Format default action: New Post Format Post
        		$wp_admin_bar->add_menu( array(
			      'parent' => 'barley-new-content',
			      'id' => 'new-'.ucfirst($barley_post_type),
			      'title' => ucfirst($barley_post_type_name),
			      'href' => $barley_action_url.'new-posttype&type='.strtolower($barley_post_type),
			      'meta' => array()
			  ) );
        	} // endforeach

    } // end if theme supports custom post types

    // Sub-menu: "Page" default action: New Page
    if ( current_user_can('publish_pages') ) {
	$wp_admin_bar->add_menu( array(
	      'parent' => 'barley-new-content',
	      'id' => 'new-page',
	      'title' => __('Page','barley-for-wordpress'),
	      'href' => $barley_action_url.'new-page',
	      'meta' => array()
	  ) );
	}

	// Sub-menu: Powered by Barley?
/*(g)
	$wp_admin_bar->add_menu( array(
	      'parent' => 'barley-new-content',
	      'id' => 'powered-by-barley',
	      'meta' => array( 'title'=>'Get Support for Barley', 'class'=>'barley-help', 'target'=>'_blank' ),
	      'title' => __('Visit Barley Help Center','barley-for-wordpress'),
	      'href' => 'http://support.getbarley.com/'
	  ) );
*/
	// Main Menu: "Publish" Post (only if current post is a draft)
	if ( current_user_can('publish_posts') ) {
		if ( (is_single()) && get_post_status($post->ID) == 'draft' ) {
			$wp_admin_bar->add_menu( array(
			    'id'   => 'barley-publish',
			    'meta' => array( 'title'=>'Publish', 'class'=>'barley-publish-post' ),
			    'title' => __('Publish','barley-for-wordpress'),
			    'href' => $barley_action_url.'publish-post&p='.$post->ID
			) );
		} // end if is_single
	}

	// Main Menu: "Publish" Page (only if current post is a draft)
/*(g)  Desactivo publicar páginas
	if ( current_user_can('publish_pages') ) {
	if ( is_page() && get_post_status($post->ID) == 'draft' ) {
		$wp_admin_bar->add_menu( array(
		    'id'   => 'barley-publish',
		    'meta' => array( 'title'=>'Publish', 'class'=>'barley-publish-post' ),
		    'title' => __('Publish','barley-for-wordpress'),
		    'href' => $barley_action_url.'publish-page&page_id='.$post->ID
		) );
	}
	}
*/

	// Main Menu: "Delete" button to delete the current post.
/*(g)  Agrego "!is_page() &&" para que detecte que no agregue el boton borrar a las páginas de buddypress
*/

	if ( !is_page() && is_single() && (current_user_can('delete_posts') && current_user_can('delete_others_posts')) || (current_user_can('delete_posts') && !current_user_can('delete_others_posts') && $post->post_author == $current_user->ID) ) {
		$wp_admin_bar->add_menu( array(
		    'id'   => 'barley-delete-post',
		    'meta' => array( 'title'=>'Delete this post', 'class'=>'barley-delete-post' ),
		    'title' => __('Delete','barley-for-wordpress'),
		    'href' => $barley_action_url.'delete-post&p='.$post->ID
		) );
	} // end if user can delete posts, can't delete others, but own's it

	// Main Menu: "Delete" button to delete the current page.
/*(g) Desactivo borrar página

	if ( is_page() && (current_user_can('delete_pages') && current_user_can('delete_others_pages')) || (current_user_can('delete_pages') && !current_user_can('delete_others_pages') && $post->post_author == $current_user->ID) ) {
		$wp_admin_bar->add_menu( array(
		    'id'   => 'barley-delete-page',
		    'meta' => array( 'title'=>'Delete this page', 'class'=>'barley-delete-page' ),
		    'title' => __('Delete','barley-for-wordpress'),
		    'href' => $barley_action_url.'delete-page&p='.$post->ID
		) );
	} // end if user can delete pages, can't delete others, but own's it

	if ( $barley_editor_currently_on ) {
	
		$wp_admin_bar->add_menu( array(
		    'id'   => 'barley-editor-off',
		    'meta' => array(),
		    'title' => __('Turn Barley Editor Off','barley-for-wordpress'),
		    'href' => $_SERVER["HTTP_HOST"] . str_replace($_SERVER["REQUEST_URI"],'?barley_editor=on','').'?barley_editor=off'
		) );

		return;
	}
*/
}

add_action( 'wp_before_admin_bar_render', 'barley_add_admin_bar' );

// Add Styles for Admin Bar
function barley_add_admin_bar_css($content=null) {
	echo '<style type="text/css">
		/* Styles for Admin Bar by Barley for WordPress */
		/*li.barley-delete-post { background: rgb(162, 45, 45) !important; }*/
		li.barley-publish-post { background: #c1c1c1 !important; }
		li.barley-publish-post a { color: #353536 !important; text-shadow: none !important; }
		li.barley-publish-post a:hover { color: #fff !important; }
	</style>';
	return;
}


// Styles for the Admin Bar (if the Barley Editor is loaded only.)
add_filter( 'wp_head', 'barley_add_admin_bar_css', 10, 2 );
?>
