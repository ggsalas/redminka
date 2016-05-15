<?php
// Wrap content with data-barley attributes depending on context
// Accepts: context (WordPress function for the content), content (the content to wrap), acf_key for Advanced Custom Fields
// Returns: content wrapped in an appropriate data-barley.
function barley_wrapper($context='',$content=null, $key=null) {
	global $post;

	switch ($context) {
		case "the_title":

			// Wrap the title in an editable span
			$barlified_content = '<div data-barley="the_title" data-barley-editor="mini" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

		break;
		case 'the_content':

			$barlified_content = "";
			
			// If a blog post put in categories and tags
			if (is_single()) {
				$barlified_content .= '<div class="barley_editor_meta_wraper" id="barley_editor_edit_meta"><a class="barley_meta_btn" id="barley_show_meta_cxt" href="#" title="Click to edit categories and tags" alt="Click to edit categories and tags"><i class="barley-icon-tag"></i></a></div>';
			}

            // If a page, show page meta
            if (is_page()) {
                $barlified_content .= '<div class="barley_editor_meta_wraper" id="barley_editor_edit_meta_page"><a class="barley_meta_btn" id="barley_show_meta_cxt_page" href="#" title="Click to edit page attributes" alt="Click to edit page attributes"><i class="barley-icon-cog"></i></a></div>';
            }

			// Wrap the post in an editable div
			$barlified_content .= '<div id="barley_the_content" data-barley="the_content" data-barley-editor="advanced" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

		break;
        case 'wp_custom_field':

            // Do not wrap a custom field if
            //      the content contains field_ for ACF
            //      the key's first character is _ for ACF
            if ( strpos($content, 'field_') !== false ) {  return $content; }
            if ( substr($key, 0, 1) == '_' ) { return $content; }

            $barlified_content = '<div data-barley="'.$key.'" data-barley-editor="mini" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';
        break;
        case "acf_text":

            // Wrap the title in an editable span
            $barlified_content = '<div data-barley="'.$key.'" data-barley-editor="mini" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

        break;
        case "acf_textarea":

            // Wrap the title in an editable span
            $barlified_content = '<div data-barley="'.$key.'" data-barley-editor="advanced" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

        break;
        case "acf_number":

            // Wrap the title in an editable span
            $barlified_content = '<div data-barley="'.$key.'" data-barley-editor="mini" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

        break;
        case "acf_email":

            // Wrap the title in an editable span
            $barlified_content = '<div data-barley="'.$key.'" data-barley-editor="mini" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

        break;
        case "acf_wysiwyg":

            // Wrap the title in an editable span
            $barlified_content = '<div data-barley="'.$key.'" data-barley-editor="advanced" data-barley-wordpressid="'.$post->ID.'">'.$content.'</div>';

        break;
	} // end switch()

	return $barlified_content;

}

// Add Barley-Editor specific JS and CSS.
function barley_add_editorJScss($content=null) {

    // Load the rest of Barley's Required JS Files
    // Check for Development or Production
    // If DEV then show uncompressed files and if production show single file for all plugins.
    if (BARLEY_ENVIRONMENT == 'DEV') {
        wp_enqueue_script( 'barley-base', BARLEY_PLUGIN_URL . '/barley_assets/barley-wordpress-base.js' );
        wp_enqueue_script( 'barley-editor', BARLEY_PLUGIN_URL . '/barley_editor/barley-editor.min.js' );
        wp_enqueue_script( 'barley-wordpress', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress.js' );
        wp_enqueue_script( 'barley-wordpress-upload', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress-upload.js' );
        wp_enqueue_script( 'barley-youtube', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress-youtube.js' );
        wp_enqueue_script( 'barley-wordpress-meta', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress-meta.js' );
        wp_enqueue_script( 'barley-wordpress-meta-page', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress-meta-page.js' );
        wp_enqueue_script( 'barley-wordpress-linksuggest', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress-link-suggest.js' );
        wp_enqueue_script( 'barley-wordpress-featured', BARLEY_PLUGIN_URL . '/barley_plugins/wordpress-featured.js' );
        // Localize the AJAX script
        wp_localize_script( 'barley-wordpress', 'barleyVars', array( 'ajaxurl' => BARLEY_ADMIN_AJAX_URL ) );
    } else {
        wp_enqueue_script( 'barley-plugin-base', BARLEY_PLUGIN_URL . '/barley_assets/barley-wordpress-base.js' );
        wp_enqueue_script( 'barley-editor-scripts', BARLEY_PLUGIN_URL . '/barley_editor/barley-editor.min.js' );
        wp_enqueue_script( 'barley-editor-plugin-scripts', BARLEY_PLUGIN_URL . '/barley_assets/barley-plugin-scripts.js' );
        // Localize the AJAX script
        wp_localize_script( 'barley-editor-plugin-scripts', 'barleyVars', array( 'ajaxurl' => BARLEY_ADMIN_AJAX_URL ) );
    }

    // Load the styles
	wp_enqueue_style( 'barley-editor-styles', BARLEY_PLUGIN_URL . '/barley_editor/barley-editor.css' );
	wp_enqueue_style( 'barley-editor-plugin-styles', BARLEY_PLUGIN_URL . '/barley_assets/barley-plugin-styles.css' );
	return;
}

// Script used to enqueue all of the proper scripts for WordPress's media libaray.
function barley_add_upload_functionality() {
	wp_enqueue_media();
}

function barley_image_resize() 
{

    $i = $_POST['i']; // Image
    $w = $_POST['w']; // New Width
    $h = $_POST['h']; // New Height
	$c = $_POST['c']; // Hard Crop - boolean

	// Process URL
    $scheme = parse_url($i, PHP_URL_SCHEME);
    $host 	= parse_url($i, PHP_URL_HOST);
    $temp_path = $scheme . "://" . $host;

    // Get Server URL
    $file_path = parse_url($i, PHP_URL_PATH);
    $server_path = $_SERVER['DOCUMENT_ROOT'] . $file_path; // Creates Full Path

    // Get File Parts for Renaming Purposes
    $file_name = pathinfo($server_path, PATHINFO_DIRNAME) . "/" . pathinfo($server_path, PATHINFO_FILENAME);
    $file_extension = pathinfo($server_path, PATHINFO_EXTENSION);
    $save_path = $file_name . "-" . $w . "x" . $h . "." . $file_extension;


    $img = wp_get_image_editor($server_path);
    if ( ! is_wp_error( $img ) ) 
    {
    	$img->resize($w, $h, $c);
        $final = $img->save($save_path);
		$finalURL = str_replace($_SERVER['DOCUMENT_ROOT'], $temp_path, $final['path']); // Rebuild URL

		// Build Response
		$response = array('success' => true, 'fileURL' => $finalURL);

    }
    else 
    {
		// Build Response
		$response = array('success' => false, 'message' => $img->get_error_message());
    }

	// Respond
	header('Content-Type: application/json');
	print json_encode($response);
	exit();

}
add_action( 'wp_ajax_barley_image_resize', 'barley_image_resize' );

//if you wanted you can get the file path from parse_url and not have to use str_replace at all other than the finalURL and you can call path info and parse_url once and just use the array it provides as return, it will require less processing by PHP


// Append Barley editor to template footer
// Accepts: Nothing
// Returns: Simply echos HTML to page. Should add it last on the page somehow.
// To Do: Change to print to the end of the footer rather than beginning
// To Do: Make a better way of handling the drafts modal.
function barley_add_editorHTML($content=null)
{

    global $post;

    // Barley Editor & YouTube Modal
    include BARLEY_PLUGIN_PATH . '/barley_editor/barley-editor.html';

    // Drafts Modal
    print '<div id="barley-drafts-modal" class="barley-modal"><div class="barley-modal-heading"><h3>Unpublished Post Drafts</h3><a class="barley-bar-close-modal" href="#"><i class="barley-icon-remove"></i></a></div><div class="barley-modal-content"><div class="barley-modal-content-wrap"><div class="barley-modal-sub-heading"><span>Title</span><span class="barley_modal_sh_date">Date</span></div><div class="barley-modal-content-area"><i class="barley-icon-spinner barley-bar-animate-spin"></i></div></div></div></div>';


    // Page Attributes
    if (is_page()) {

        $barley_page_attributes = '<div id="barley_page_attributes_wrap"><div class="barley_page_attributes">';

        $dropdown_args = array(
            'post_type'        => $post->post_type,
            'exclude_tree'     => $post->ID,
            'selected'         => $post->post_parent,
            'name'             => 'barley_page_parent',
            'show_option_none' => __('(no parent)'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 0,
        );

        $pages = wp_dropdown_pages($dropdown_args);
        $count = count(get_pages($dropdown_args));

        if ($count > 0) {
            $barley_page_attributes .= '<label for="barley_page_parent">Parent</label>';
            $barley_page_attributes .= $pages . '<span class="sort-icon"><i class="barley-icon-caret-down"></i></span>';
        }

        $barley_page_attributes .= '<label for="barley_page_template">Template</label><select id="barley_page_template"><option name="default">Default Template</option>';

        $templates = wp_get_theme()->get_page_templates();
        $current_template = get_post_meta( $post->ID, '_wp_page_template', true );
        foreach ( $templates as $template_name => $template_filename ) {
            if ($current_template == $template_name) {
                $barley_page_attributes .= '<option value="' . $template_name . '" selected>' . $template_filename . '</option>';
            } else {
               $barley_page_attributes .= '<option value="' . $template_name . '">' . $template_filename . '</option>'; 
            }
        }

        $barley_page_attributes .= '</select><span class="sort-icon-last"><i class="barley-icon-caret-down"></i></span><label for="barley_page_order">Order</label><input type="text" id="barley_page_order" value="';
    
        $barley_page_attributes .= get_post_field('menu_order', $post->ID);

        $barley_page_attributes .= '" /></div></div>';

        print $barley_page_attributes;

    }
    
	return;
}

function barley_update_post_attributes() {

    // Update Post
    $page = array(
        'ID'            => $_POST['id'],
        'post_parent'   => $_POST['parent'],
        'menu_order'    => $_POST['post_order'],
        'page_template' => $_POST['page_template']
    );

    // Update the post into the database
    $response = wp_update_post($page);

    // Respond
    header('Content-Type: application/json');
    print json_encode($page);
    exit();

}
add_action( 'wp_ajax_barley_update_post_attributes', 'barley_update_post_attributes' );


// Add Barley Editor HTML in footer
add_filter( 'wp_footer', 'barley_add_editorHTML', 10, 2 ); // HTML for Barley Editor
add_filter( 'wp_head', 'barley_add_upload_functionality', 10, 2 ); // Uploading AJAX for WordPress Media Library
add_filter( 'wp_head', 'barley_add_editorJScss', 10, 2 ); // JavaScript, CSS for Barley Editor

// function: barley_wrap_the_title
// Wraps the_title in a Barley editable field
function barley_wrap_the_title($content=null) {
    if ( barley_check_conditions() ) {
        return barley_wrapper( 'the_title', $content );
    }
return $content;
}

// function: barley_wrap_the_content
// Wraps the_content in a Barley editable field
function barley_wrap_the_content($content=null) {
    if ( barley_check_conditions() ) {
        remove_filter( 'the_content', 'do_shortcode', 11 ); // Do not parse shortcodes in the_content
        remove_filter( 'the_content', 'wp_insert_inpostads_filter_the_content', 100 ); // Do not add ads inside the content with wp-insert plugin
        return barley_wrapper( 'the_content', $content );
    }
return $content;
}

// function: barley_wrap_acf
// Wraps Advanced Custom Fields in Barley editable fields
// Only works on text field types
function barley_wrap_acf( $value, $post_id, $field ) {
    if ( barley_check_conditions() ) {

        if ( $field['type'] == 'text' ) {
            return barley_wrapper( 'acf_text', $value, $field['key'] );
        }
        
        if ( $field['type'] == 'textarea' && $field['formatting'] == 'html' ) {
            return barley_wrapper( 'acf_textarea', $value, $field['key'] );
        }

        if ( $field['type'] == 'number' ) {
            return barley_wrapper( 'acf_number', $value, $field['key'] );
        }

        if ( $field['type'] == 'email' ) {
            return barley_wrapper( 'acf_email', $value, $field['key'] );
        }

        if ( $field['type'] == 'wysiwyg' ) {
            return barley_wrapper( 'acf_wysiwyg', $value, $field['key'] );
        }

    }

return $value;
}

barley_plugin_compatibility_fixes(); // Avoid plugin conflicts. Because, zombies!!
add_filter( 'the_title', 'barley_wrap_the_title', 1 );
add_filter( 'single_post_title', 'barley_wrap_the_title', 1 );
add_filter( 'the_content', 'barley_wrap_the_content', 1 );

// Support for Advanced Custom Fields
if ( function_exists('update_field') ) {
    add_filter( 'acf/load_value/type=text', 'barley_wrap_acf', 10, 3 );
    add_filter( 'acf/load_value/type=textarea', 'barley_wrap_acf', 10, 3 );
    add_filter( 'acf/load_value/type=number', 'barley_wrap_acf', 10, 3 );
    add_filter( 'acf/load_value/type=email', 'barley_wrap_acf', 10, 3 );
    add_filter( 'acf/load_value/type=wysiwyg', 'barley_wrap_acf', 10, 3 );
}


// Function to check the conditions in which we're currently at.
// The following conditions must be met for Barley to wrap the content and title
    // Is a page, or a post
    // Is is_main_query (typically the original loop)
    // The Barley editor isn't being "turned off" to view shortcodes
    // If the current user is allowed to edit any post OR they own the post
// If the conditions are met, we check compatibility, turn off shortcodes, wrap with Barley
function barley_check_conditions() {
    global $current_user, $post, $barley_editor_currently_on;

    if ( !$barley_editor_currently_on )
        return false;
    
    if ( (is_single() || is_page()) && (is_main_query()) && (in_the_loop()) ) {

        if ( current_user_can( 'edit_others_posts' ) || ($post->post_author == $current_user->ID) ) { 
            return true;
        }
    }
return false;
}


/**
 * In some themes wp_title returns the <span> used for data-barley.
 * (but only when logged in) This filter will, if found, remove those.
 */
function barley_wp_title( $title ) {

	$title = str_replace( '</div>', '', html_entity_decode($title));
	$title = preg_replace( '/<div[^<]+?>/', '', html_entity_decode($title));

	return $title;
}
add_filter( 'wp_title', 'barley_wp_title', 1, 1 );
add_filter( 'get_sidebar', 'barley_wp_title', 1, 1 );


// Get all categories for blog and also
// mark the one(s) that the current post is in
// Returns: If ID included array with current_cat = true on the post
// Returns: If no ID included array with just categories
// Returns: false if error
function barley_get_categories() {

	header('Content-Type: application/json');

	if (isset($_POST['id'])) {
		$barley_current_category = get_the_category( $_POST['id'] );
		$barley_all_categories = get_categories( array('hide_empty' => 0) );
        foreach ($barley_all_categories as $category){
            for($i=0;$i<count($barley_current_category);$i++) { 
                if ( $barley_current_category[$i]->cat_ID == $category->cat_ID ) {
                    $category->current_cat = true;
                }
            }
        }
		print json_encode($barley_all_categories);
	} else {
		print json_encode(get_categories());
	}

  	exit();

	return false;
}

add_action( 'wp_ajax_barley_get_categories', 'barley_get_categories' );

// Function to update the categories... if passed in catetory does not exisit this will create it.
function barley_update_categories(){
    
    // Set up JSON Headers for proper response
    header('Content-Type: application/json');

    // First Check for new category
    $category_id = get_cat_ID($_POST['category']);

    // If category does not exist, lets add it and assing it to this post
    if($category_id == 0){

        // Add Category to Database
        $add_category = array('cat_name' => $_POST['category'] );
        wp_insert_category($add_category);

        // Now get that added category ID
        $category = get_cat_ID( $_POST['category'] );

        // Set category active to Post ID
        wp_set_object_terms( $_POST['id'], intval($category), 'category', true);

        print json_encode(array('success'=>true, 'message'=>'Success, new category added and associated.'));

        exit(); // Kill the process here since this is complete.

    }

    // Category Already exists, so let's see if we need to attach it or remove it

    // Load current categories
    $categories = wp_get_post_categories($_POST['id']);
    $category = get_category($category_id);
    
    // Search for Category in current assigned categories, to not confuse things
    $index = array_search($category->cat_ID, $categories);

    // See if we should remove it
    if ($_POST['remove'] == 'true') {
        if ($index !== false) {
            unset($categories[$index]);
        }
    } else {
        if ($index === false) {
            $categories[] = $category->cat_ID;
        }
    }
    
    // Update the array of categories 
    $result = wp_set_post_categories($_POST['id'], $categories);

    $output = implode(",", $result);
    
    print json_encode(array('success'=>true, 'message'=>'Categories have been updated.', 'categories_in'=>$output));

    //print json_encode($result);

    exit();

}
add_action('wp_ajax_barley_update_categories', 'barley_update_categories');

// Get the tags for a current post. ID required
function barley_get_tags() {
	header('Content-Type: application/json');

	if (isset($_POST['id'])) {
		$the_tags = get_the_tags($_POST['id']);
		
		if ($the_tags) {
			print json_encode($the_tags); exit;
		}
	}
	
	print json_encode(array('success'=>false));
	exit();
}
add_action( 'wp_ajax_barley_get_tags', 'barley_get_tags' );


// Update tags for current post
function barley_update_tags(){
	header('Content-Type: application/json');

	if (isset($_POST['id']) && isset($_POST['tags'])) {
		wp_set_post_tags( $_POST['id'], $_POST['tags'], false);
		print json_encode(array('success'=>true));
	} else {
		print json_encode(array('success'=>false, 'message'=>'Error, post tags could not be saved.'));
	}
	exit();
}
add_action( 'wp_ajax_barley_update_tags', 'barley_update_tags' );


// Retrieve a list of posts that are in draft status for modal.
function barley_get_post_drafts(){
    global $current_user, $user_level;

    $args = array(
        'post_type' => 'any',
        'orderby'   => 'date',
        'order'     => 'DESC',
        'post_status' => 'draft',
        'posts_per_page' => -1,
    );

    // For anything below Administrators.
    if ( !current_user_can('edit_others_posts') ) {
        $args['author'] = $current_user->ID;
    }

    $query = new WP_Query($args);
    $draft_list = "<ul>";

    //print_r($query);
    while ($query->have_posts()) : $query->the_post();

        $tempTitle = get_the_title();
        if($tempTitle == ''){
            $tempTitle = "(no title)";
        }
        $tempTitle = substr( $tempTitle, 0, 73 ) . '...';

        (get_post_type() == 'page') ? $tempPrefix = '<strong>Page:</strong> ' : $tempPrefix = '';

        $draft_list .= '<li><a href="'.get_permalink().'"><span class="draft-title">'.$tempPrefix.$tempTitle.'</span><span class="draft-create-date">'.get_the_date().'</span></a></li>';

    endwhile;

    $draft_list .= "</ul>";

    print $draft_list;

    exit();

}
add_action( 'wp_ajax_barley_get_post_drafts', 'barley_get_post_drafts' );


// Get a list of all custom post types.
function barley_post_types() {

    $types = array_merge( array_keys(get_post_types(array("public" => true, "_builtin" => false))), array('post', 'page') );

    $post_type_list = '<div id="barley-post-types" style="display:none;"><div class="barley-links-wrapper" style="display:none;"><div class="barley-links-visible"><ul class="barley-links-categories">';
    $post_type_list .= '<li><input type="text" id="barley-search-links" placeholder="Search Posts..." /><i class="barley-icon-remove-sign" id="barley-link-search-reset"></i></li>';

    foreach($types as $key=>$value)
    {

        $obj = get_post_type_object( $value );

        $post_type_list .= '<li data-barley-linkid="'. $value .'">' . $obj->labels->name . '</li>';

    }

    $post_type_list .= '</ul></div></div><i id="barley-search-spin" class="barley-icon-spinner barley-bar-animate-spin"></i></div>';

    print $post_type_list;

    exit();

}
add_action( 'wp_ajax_barley_post_types', 'barley_post_types' );


// Barley Link Search
function barley_link_search() {
    global $wpdb;

    if (strlen($_POST['search_query'])>2) {
        $limit=20;
        $s=strtolower(addslashes($_POST['search_query']));
        $querystr = "
            SELECT $wpdb->posts.*
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_type='post'
            AND $wpdb->posts.post_status='publish'
            AND lower($wpdb->posts.post_title) like '%$s%'
            ORDER BY $wpdb->posts.post_date DESC
            LIMIT $limit;
         ";

        $pageposts = $wpdb->get_results($querystr, OBJECT);
        $search_results = '<ul id="barley-lsearch-res" class="barley-link-block">';

        if (!empty($pageposts)) {
            $x=0;
            while ($pageposts[$x]) {
                $post=$pageposts[$x];
                $search_results .= '<li>';
                $search_results .= '<a href="'.$post->guid.'">'.$post->post_title.'</a>';
                $search_results .= '</li>';
                $x++;
            }
        } else {
            $search_results .= '<li>Sorry, no results</li>';
        }

        $search_results .= '</ul>';

        print $search_results;

    }
    else print '<ul id="barley-lsearch-res" class="barley-link-block"><li>Sorry, no results</li></ul>';
    die();
}
add_action( 'wp_ajax_barley_link_search', 'barley_link_search' );


// Function to get all pages inside of a custom post type
function barley_link_list() {

    $type = $_POST['content_type'];
    $title = '<li class="barley-link-block-header">'. $_POST['content_title'] .' <i class="barley-icon-remove"></i></li>';
    $loop = new WP_Query( array( 'post_type' => $type, 'posts_per_page' => 20, 'order' => 'ASC', 'orderby' => 'title', 'post_status' => 'publish' ) );

    $link_list = '<ul id="barley_'. $type .'_list" class="barley-link-block" data-barley-link-offset="20">';
    $link_list .= $title;

    while ( $loop->have_posts() ) : $loop->the_post(); 

        $tempTitle = get_the_title();
        if($tempTitle == ''){
            $tempTitle = "(no title)";
        }

        $link_list .= '<li><a href="'. get_permalink() .'">'. $tempTitle .'</a></li>';

    endwhile;

    $link_list .= '</ul>';

    print $link_list;

    exit();

}
add_action( 'wp_ajax_barley_link_list', 'barley_link_list' );

function barley_link_list_offset() {

    $type = $_POST['content_type'];
    $offset = $_POST['number_offset'];
    $loop = new WP_Query( array( 'post_type' => $type, 'posts_per_page' => 20, 'order' => 'ASC', 'orderby' => 'title', 'post_status' => 'publish', 'offset' => $offset ) );

    $link_list = '';

    while ( $loop->have_posts() ) : $loop->the_post(); 

        $tempTitle = get_the_title();
        if($tempTitle == ''){
            $tempTitle = "(no title)";
        }

        $link_list .= '<li><a href="'. get_permalink() .'">'. $tempTitle .'</a></li>';

    endwhile;

    print $link_list;

    exit();   

}
add_action( 'wp_ajax_barley_link_list_offset', 'barley_link_list_offset' );


// Server helper to process short codes
function barley_process_audio(){
    header('Content-Type: application/json');

    if (isset($_POST['audio'])){
        $output = wp_audio_shortcode($_POST['audio']);
        print json_encode(array('success'=>true, 'html'=>$output));
    } else {
        print json_encode(array('success'=>false, 'message'=>'This audio could not be added at this time. Please try again.'));
    }

    exit();
}
add_action( 'wp_ajax_barley_process_audio', 'barley_process_audio' );


// Update WordPress Post Meta - can be used for a variety of things
// To Do : Add in success
function barley_get_featured_image(){

    header('Content-Type: application/json');

    if($_POST['id']) {

        $thumb = get_the_post_thumbnail( $_POST['id'], array(201,201) );

        print json_encode(array('success'=>true, 'message'=>$thumb));

    } else {
        print json_encode(array('success'=>false, 'message'=>'Could not check for post thumbnail'));
    }

    exit();

}
add_action( 'wp_ajax_barley_get_featured_image', 'barley_get_featured_image' );

// Update WordPress Post Meta - can be used for a variety of things
function barley_update_post_meta(){

    header('Content-Type: application/json');

    if($_POST['id']) {

        $id = $_POST['id'];
        $key = $_POST['key'];
        $value = $_POST['value'];

        update_post_meta($id, $key, $value);

        print json_encode(array('success'=>true, 'message'=>'Post meta has been updated'));

    } else {
        print json_encode(array('success'=>false, 'message'=>'Post meta could not be updated.'));
    }

    exit();

}
add_action( 'wp_ajax_barley_update_post_meta', 'barley_update_post_meta' );


// Function to remove any actions or filters that conflict with Barley for Wordpress
function barley_plugin_compatibility_fixes() {

    // Compatibility fix for Facebook Meta Tags plugin
    if ( function_exists('insert_facebook_metatags') ) {
        remove_action( 'wp_head', 'insert_facebook_metatags' );
    }

}

// Simple array search function to loop through debug_backtrace
// This determines if the plugin filter to wrap Barley content in editable divs
// is being called by the sidebar or not. We wouldn't want that.
function barley_determine_scope($array, $key, $value){
	foreach ($array as $k => $arr) {
		if ($arr[$key] == $value) { 
			return true; 
		}
	}
return false;
}
?>