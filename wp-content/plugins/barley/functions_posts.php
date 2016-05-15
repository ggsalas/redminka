<?php
// If barley_action found
if (isset($_GET['barley_action'])) {

  // Used to allow wp_redirect to work (Hack found on WordPress forums)
  function barley_output_buffer() {
    switch ($_GET['barley_action']) {

      // Create a new blog post
      // Redirect to blog post draft preview
      case 'new-blogpost':
        // Default blog post data
        $barley_defaultData_BlogPost = array(
          'post_title'    => 'Título',
          'post_content'  => '<p>Comenzar a escribir</p> <p></p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_draft_id = wp_insert_post( $barley_defaultData_BlogPost );
        if ($barley_draft_id > 0) {
          wp_redirect( site_url().'/?p='.$barley_draft_id ); exit; // Redirect to draft
        } else {
          echo 'Post may not be created.';
        }
      break;

      // Create a new Post Format
      // All WordPress Post Formats accepted
      // Redirect to post format draft preview
      case 'new-posttype':
        $barley_defaultData = array(
          'post_title'    => 'Tap here to edit your '.$_GET['type'],
          'post_content'  => '<p>Begin editing your post content.</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_draft_id = wp_insert_post( $barley_defaultData );
        set_post_type( $barley_draft_id , $_GET['type']); // Sets the post format for the just created post
        if ($barley_draft_id > 0) {
          wp_redirect( site_url().'/?p='.$barley_draft_id ); exit; // Redirect to draft
        } else {
          echo 'Post may not be created.';
        }
      break;
      case 'new-postformat':
        $barley_defaultData_Format = array();
        $barley_defaultData_Format['aside'] = array(
          'post_title'    => '',
          'post_content'  => '<p>An aside is typically styled without a title. Similar to a Facebook note update. Simply replace this text with your own.</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['audio'] = array(
          'post_title'    => 'Your New Audio File Title',
          'post_content'  => '<p>Describe your audio file. Is it a podcast episode? New track for your album? Remix?</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['chat'] = array(
          'post_title'    => 'Your New Chat Title',
          'post_content'  => '<p>A chat transcript, like so:</p><p><b>Colin:</b> So, what do you think of the Barley editor for WordPress?<br><b>Kyle:</b> The best thing to happen to WordPress since jazz.<br><b>Tim:</b> Better than that. It is like sliced bread.<br><b>Jeff:</b> It is nice. But I use the MetaWeblogAPI to post from a bash script.<br><b>Chris:</b> Nerd.</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['gallery'] = array(
          'post_title'    => 'Your New Image Gallery Title',
          'post_content'  => '<p><img class="photo-right hide-for-small" src="http://placehold.it/640x480" width="640"></p><p><img class="photo-right hide-for-small" src="http://placehold.it/640x480" width="640"></p><p>Describe your photo gallery. What is it of? Where was it taken? Who was there?</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['image'] = array(
          'post_title'    => 'Your New Image Title',
          'post_content'  => '<p><img class="photo-right hide-for-small" src="http://placehold.it/640x480" width="640"></p><p>Replace the above placeholder image or delete it to insert your own.</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['link'] = array(
          'post_title'    => 'Your New Link Title',
          'post_content'  => '<p><a href="http://getbarley.com">Download Barley for WordPress today!</a></p><p>What an incredible content editor for WordPress! Just $1 a month!</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['quote'] = array(
          'post_title'    => 'Your New Quote Title',
          'post_content'  => '<blockquote><p>"I saw the world. I learnt of new cultures. I flew across an ocean. I wore women\'s clothing. Made a friend. Fell in love. Who cares if I lost a wager?" -- <cite>Jules Verne, <i>Around the World in Eighty Days</i></cite></p></blockquote>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['status'] = array(
          'post_title'    => '',
          'post_content'  => '<p>Writing on my WordPress-powered site using <a href="http://getbarley.com/">Barley for WordPress</a>.</p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_defaultData_Format['video'] = array(
          'post_title'    => 'Your New Video Title',
          'post_content'  => '<p><iframe width="640" height="480" src="//www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe></p><p>Sorry, we sort of had to.</p><p>Sincerely,<br><a href="http://plainmade.com/">The Plain team</a></p>',
          'post_status'   => 'draft',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_draft_id = wp_insert_post( $barley_defaultData_Format[$_GET['format']] );
        set_post_format( $barley_draft_id , $_GET['format']); // Sets the post format for the just created post
        if ($barley_draft_id > 0) {
          wp_redirect( site_url().'/?p='.$barley_draft_id ); exit; // Redirect to draft
        } else {
          echo 'Post may not be created.';
        }
      break;

      // Create a new Page
      case 'new-page':
        $barley_defaultData_Page = array(
          'post_title'    => 'Your New Page',
          'post_content'  => '<p><img src="http://placehold.it/640x480"></p><p>You can add as many pages to your site as you\'d like and fill each page with text, images and other embed-able elements (maps, videos, etc.). Use the Barley Editor to <a href="http://getbarley.com/">add links to your text</a>, or use <b>bold</b> and <i>italics</i>. In addition you can change your text to be a heading, list and more. To pull up the Barley Editor, just highlight the text you would like to edit. Add images or video by hovering over an element or hitting the "Enter" key after a paragraph.</p><p><img src="http://placehold.it/260x130"></p><p>Images can be aligned to the left, right, or center by using the image editor. Simply bring up the image editor by clicking on the image > edit icon ( <i class="barley-icon-edit"></i> ) and type either "alignleft", "alignright", or "aligncenter" in the class field. You can also change the size of an image and the title here as well. An existing image, such as the one to the right, can be replaced or removed. Replacing an image will cause the new image to be cropped to the dimensions of the original. This can come in handy for certain situations. To place an image on the page with different dimensions, simply remove the original and add the new one.</p><p>To publish this page, click the "Publish" button in the WordPress Admin Bar. Prior to clicking the publish button, this page will be saved as a draft. You can access drafts by clicking "View Drafts" in the WordPress Admin Bar or directly from the WordPress Admin. To delete this page entirely, click the "Delete" button in the WordPress Admin Bar, just right of the "Publish" button. After publishing this page, it will be added to your site\'s navigation.</p>',
          'post_status'   => 'draft',
          'post_type'   => 'page',
          'post_author'   => get_current_user_id(),
          'post_category' => array(get_cat_ID( 'Uncategorized' ))
        );
        $barley_draft_id = wp_insert_post( $barley_defaultData_Page );
        if ($barley_draft_id > 0) {
          wp_redirect( site_url().'/?p='.$barley_draft_id ); exit;
        } else {
          echo 'Post may not be created.';
        }
      break;

      // ### Publish Post
      case 'publish-post':
        $barley_post_id = wp_update_post( array('ID'=>$_GET['p'],'post_status'=>'publish') );
        if ($barley_post_id > 0 ) {
          wp_redirect(site_url().'/?p='.$barley_post_id); exit;
        }
      break;

      // ### Delete Post
      case 'delete-post':
        $barley_post_id = wp_delete_post( $_GET['p'] );
        if (!$barley_post_id) {
          wp_redirect(site_url().'/?p='.$barley_post_id); exit;
        } else {
          wp_redirect(site_url().'/'); exit;
        }
      break;

      // ### Delete Page
      case 'delete-page':
        $barley_post_id = wp_delete_post( $_GET['p'] );
        if (!$barley_post_id) {
          wp_redirect(site_url().'/?p='.$barley_post_id); exit;
        } else {
          wp_redirect(site_url().'/'); exit;
        }
      break;

      // ### Publish Page.
      case 'publish-page':
        $barley_post_id = wp_update_post( array('ID'=>$_GET['page_id'],'post_status'=>'publish') );
        if ($barley_post_id > 0 ) {
          wp_redirect(site_url().'/?page_id='.$barley_post_id); exit;
        }
      break;

      case 'update-tags':
          // Tag draft as "untagged" so that it has a default tag
          $barley_post_id = $_GET['p'];
          wp_set_post_tags( $barley_post_id, $_POST['barley_tag_list'], false ); // Updates tags to be exactly what is submitted
          wp_redirect(site_url().'/?p='.$barley_post_id); exit;
      break;

      case 'get-categories':
        barley_get_categories();
      break;

      case 'get-tags':
        barley_get_tags();
      break;

    } // end switch
  } // end function

  add_action( 'init', 'barley_output_buffer' ); // This is used so that wp_redirect will work. The header of the site isn't published if there is a barley_action in $_GET
}

// Update posts
function barley_update_post()
{
  $json            = array();
  $json['success'] = false;
  $columns         = array(
                      'the_title'   => 'post_title',
                      'the_content' => 'post_content');

  // Only proceed if we have a post_id
  if ( isset($_POST['p']) && ! empty($_POST['p']) ) {
    $k               = trim(urldecode($_POST['k']));
    $v               = trim(urldecode($_POST['v']));
    $pid             = trim(urldecode($_POST['p']));

    // Strip trailing BR tag in FireFox
    if ( $k === 'the_title' ) {
      $v = preg_replace('/(.*)<br[^>]*>/i', '$1', $v);
    }

    // For the_title and the_content only
    if (array_key_exists($k, $columns)) {
        $res = wp_update_post(array(
            'ID'         => $pid,
            $columns[$k] => $v
        ));
    }

    // Save an Advanced Custom Field
    if ( strpos($k, 'field_') !== false ) {
      $res = update_field($k,$v,$pid);
    }

    // Save a WordPress Custom Field
    if ( strpos($k, 'field_') === false && !array_key_exists($k, $columns) ) {
      $res = update_post_meta($pid,$k,$v);
    }

    // Good? No? Yes?
    $json['success'] = ($res > 0) ? true : false;

  } // end post_id

  header('Content-Type: application/json');
  print json_encode($json);
  exit();
}
add_action( 'wp_ajax_barley_update_post', 'barley_update_post' );

?>
