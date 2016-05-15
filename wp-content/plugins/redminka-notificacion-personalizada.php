<?php
/*
Plugin Name: [redminka] Notificación de comentarios Personalizada
Plugin URI: http://www.jonathanpenny.co.uk
Description: Hides private details from comment notification emails, such as IP address and email.
Author: Jonathan Penny y GGsalas
Version: 1.0
Author URI: http://www.jonathanpenny.co.uk
*/

// Agregar el checkbox de suscripción a los comentarios SOLO si no es el autor del post

add_action('comment_form', 'suscribirse_comentarios');
function suscribirse_comentarios(){
	global $post,$current_user;
	get_currentuserinfo();
	if ($post->post_author == $current_user->ID ){
		echo 'Estás suscripto a las notificaciones de comentarios para esta publicación porque sos el autor';
	} else {
		echo '<input type="checkbox" value="1" name="subscribe" id="subscribe" checked="checked"/> Enviarme un correo electrónico si se agregan nuevos comentarios en esta publicación';
	}
}
 
 
if ( ! function_exists('wp_notify_postauthor') ) :
/**
 * Notify an author of a comment/trackback/pingback to one of their posts.
 *
 * @since 1.0.0
 *
 * @param int $comment_id Comment ID
 * @param string $comment_type Optional. The comment type either 'comment' (default), 'trackback', or 'pingback'
 * @return bool False if user email does not exist. True on completion.
 */
function wp_notify_postauthor($comment_id, $comment_type='') {
	$comment = get_comment($comment_id);
	$post    = get_post($comment->comment_post_ID);
	$user    = get_userdata( $post->post_author );
 
	if ('' == $user->user_email) return false; // If there's no email to send the comment to
 
	$comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
 
	$blogname = get_option('blogname');
 
	if ( empty( $comment_type ) ) $comment_type = 'comment';
 
	if ('comment' == $comment_type) {
		$notify_message = '<body style="margin: 0px;">
										<table cellspacing="0" cellpadding="0" border="0" width="100%">
											<tr>
												<td style="background-color:#009B77; color:#fff; text-align:center; height:63px; padding:6px; font-size:30px; line-height:30px;">
													  <a style="color:#fff; text-decoration:none;" href="http://redminka.com">
													    <img border="0" style="border:0px solid #009B77;" height="35px" src="http://redminka.com/wp-content/themes/redminkaepik/img/login-logo.png" alt="redminka" />
													  </a>
												</td>
											</tr>
											<tr>
												<td style="background-color:#E9E9D2; font-size: 18px; line-height: 1.5; padding:25px 15%; color:#5F5741">
									';
		$notify_message .= '<p style="color:#5F5741; font-size:80%;">'.sprintf( __('Hola, hay un nuevo comentario en tu publicación "%1$s"'), $post->post_title ) . "\r\n".'</p>';
		$notify_message .= '<p style="color:#5F5741;">'.sprintf( __('%1$s dice:'), $comment->comment_author ) . "\r\n".'</p>';
		$notify_message .= '<p style="color:#5F5741; background:#fff; padding:15px; border: 1px solid #D3D3BB; border-bottom: 2px solid #D3D3BB;">'. __('') . "\r\n" . $comment->comment_content . "\r\n\r\n".'</p>';
		$notify_message .=  '<p style="text-align:right;"> <a style="background-color: #009B77; display: inline-block; color: #fff; padding:5px 12px; border-radius:3px; text-decoration: none;" href="'
									.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.' ">Responder</a></p>';
//		$notify_message .= __('Responder y ver todos los comentarios de esta publicación en: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Nuevo comentario en tu publicación "%2$s"'), $blogname, $post->post_title );
	} elseif ('trackback' == $comment_type) {
		$notify_message  = sprintf( __('New trackback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Website: %1$s'), $comment->comment_author ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
		$notify_message .= __('You can see all trackbacks on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Trackback: "%2$s"'), $blogname, $post->post_title );
	} elseif ('pingback' == $comment_type) {
		$notify_message  = sprintf( __('New pingback on your post #%1$s "%2$s"'), $comment->comment_post_ID, $post->post_title ) . "\r\n";
		$notify_message .= sprintf( __('Website: %1$s'), $comment->comment_author ) . "\r\n";
		$notify_message .= __('Excerpt: ') . "\r\n" . sprintf('[...] %s [...]', $comment->comment_content ) . "\r\n\r\n";
		$notify_message .= __('You can see all pingbacks on this post here: ') . "\r\n";
		$subject = sprintf( __('[%1$s] Pingback: "%2$s"'), $blogname, $post->post_title );
	}
//	$notify_message .= get_permalink($comment->ID) . "\r\n\r\n";
	
	$notify_message .= '		</td>
										</tr>
										<tr>
											<td style="background-color:#fff; color:#5F5741; font-size: 14px; border-top:4px solid #009B77; line-height: 1.5; padding-top:15px; text-align:center;">
												<p>Estás recibiendo este correo porque sos el autor de la publicación</p>
											</td>
										</tr>
									</table>
									</body>
								';
								
//	$notify_message .= sprintf( __('Delete it: %s'), admin_url("comment.php?action=cdc&amp;c=$comment_id") ) . "\r\n";
//	$notify_message .= sprintf( __('Spam it: %s'), admin_url("comment.php?action=cdc&amp;dt=spam&amp;c=$comment_id") ) . "\r\n";
 
	$wp_email = 'wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
 
	if ( '' == $comment->comment_author ) {
		$from = "From: \"$blogname\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: $comment->comment_author_email";
	} else {
		$from = "From: \"$comment->comment_author\" <$wp_email>";
		if ( '' != $comment->comment_author_email )
			$reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
	}
 
	$message_headers = "$from\n"
		. "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
 
	if ( isset($reply_to) )
		$message_headers .= $reply_to . "\n";
 
	$notify_message = apply_filters('comment_notification_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_notification_subject', $subject, $comment_id);
	$message_headers = apply_filters('comment_notification_headers', $message_headers, $comment_id);
 
	@wp_mail($user->user_email, $subject, $notify_message, $message_headers);
 
	return true;
}
endif;
 
?>