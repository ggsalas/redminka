<?php
/*
Plugin Name: Comment Notifier
Plugin URI: http://www.satollo.net/plugins/comment-notifier
Description: Enable reader to subscribe to post's comments to follow discussions (increase blog traffic and participation)
Version: 2.2.9
Author: Stefano Lissa
Author URI: http://www.satollo.net
*/

/*  Copyright 2013 Stefano Lissa  (email : stefano@satollo.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

@include_once(dirname(dirname(__FILE__)) . '/comment-notifier-extras.php');

add_action('admin_head', 'cmnt_admin_head');
function cmnt_admin_head()
{
    if (isset($_GET['page']) && strpos($_GET['page'], basename(dirname(__FILE__)) . '/') === 0) {
        echo '<link type="text/css" rel="stylesheet" href="' . plugins_url('admin.css', __FILE__) . '">';
    }
}

/**
 * Called when a comment is added to a post with its status: '0' - in moderation,
 * '1' - approved, 'spam' if it is spam. The comment_id is the database id of the
 * comment which is already saved a this point.
 */
function cmnt_comment_post($comment_id, $status)
{
    //cmnt_log('New comment posted, id: ' . $comment_id . ', status: ' . $status);
    //cmnt_log('Subscribe checkbox status (empty -> not set): ' . $_POST['subscribe']);

    // Checks if there is a subscription request, comments are always sent with
    // POST requests. The '$POST' contains all the values sent by commenters:
    // 'email', 'author', 'comment_post_ID', ...
    if (($status === 0 || $status === 1) && isset($_POST['subscribe']))
    {
        //cmnt_log('Going on with subscription');

        $comment = get_comment($comment_id);
        // Get the post id: it is in the POST parameter set as "comment_post_ID". There is no need to execute queries!
        // Email is already valid, Wordpress checks it before to add a comment to the db...)
        $email = strtolower(trim($comment->comment_author_email));
        $post_id = $comment->comment_post_ID;
        $name = $comment->comment_author;

        cmnt_subscribe($post_id, $email, $name);
    }

    // Notify if the comment is already approved
    if ($status == 1)
    {
        cmnt_thankyou($comment_id);
        cmnt_notify($comment_id);
    }
}

/**
 * Called when a comment is changed of status (usually by an action of the administrator).
 * The status is a string of 'hold', 'approve', 'spam', or 'delete'.
 */
function cmnt_wp_set_comment_status($comment_id, $status)
{
    //cmnt_log('Comment ' . $comment_id . ' status changed to: ' . $status);
    // When a comment is approved the subscribers are notified
    if ($status === 'approve')
    {
        cmnt_thankyou($comment_id);
        cmnt_notify($comment_id);
    }
}

function cmnt_thankyou($comment_id)
{
    global $wpdb;

    $options = get_option('cmnt');

    //cmnt_log('Thank you message start');

    if (!isset($options['ty_enabled']))
    {
        //cmnt_log('Thank you message not enabled');
        return;
    }

    //cmnt_log('Checking comment id ' . $comment_id);
    $comment = get_comment($comment_id);
    //cmnt_log('Comment data: ' . print_r($comment, true));

    $query = $wpdb->prepare("select count(*) from " . $wpdb->comments . " where comment_approved='1' and lower(comment_author_email)=%s", strtolower($comment->comment_author_email));
    //cmnt_log($query);
    $count = $wpdb->get_var($query);

    //cmnt_log('Found ' . $count . ' comment with email ' . $comment->comment_author_email);
    if ($count != 1) return;

    $post = get_post($comment->comment_post_ID);
    $data->post_id = $comment->comment_post_ID;
    $data->title = $post->post_title;
    $data->link = get_permalink($comment->comment_post_ID);
    $data->comment_link = $data->link . '#comment-' . $comment_id;
    $data->author = $comment->comment_author;
    $data->content = $comment->comment_content;

    $message = $message = cmnt_replace($options['ty_message'], $data);

    // Fill the message subject with same for all data.
    $subject = $options['ty_subject'];
    $subject = str_replace('{title}', $post->post_title, $subject);
    $subject = str_replace('{author}', $comment->comment_author, $subject);

    cmnt_mail($comment->comment_author_email, $subject, $message, isset($options['ty_html']));
}

/**
 * Add a subscribe checkbox after the form content if the theme calls the right function
 * and the user want the checkbox automatically added. The options panel checks the theme
 * compatibility.
 */
function cmnt_comment_form()
{
    $options = get_option('cmnt');
    if (isset($options['checkbox']))
    {
        echo '<p style="clear:both"><input style="width: 20px" type="checkbox" value="1" name="subscribe" id="subscribe"';
        if (isset($options['checked']))
        {
            echo ' checked="checked"';
        }
        echo '/>&nbsp;<label style="margin:0; padding:0; position:relative; left:0; top:0;" for="subscribe">' . $options['label'] . '</label></p>';
    }
}

/** Replace placeholders in body message with subscriber data and post/comment
 * data.
 *
 * @param <type> $message
 * @param <type> $data
 * @return <type>
 */
function cmnt_replace($message, $data)
{
    $options = get_option('cmnt');

    $message = str_replace('{title}', $data->title, $message);
    $message = str_replace('{link}', $data->link, $message);
    $message = str_replace('{comment_link}', $data->comment_link, $message);
    $message = str_replace('{author}', $data->author, $message);

    $temp = strip_tags($data->content);
    if ($options['length'] && strlen($temp) > $options['length'])
    {
        $x = strpos($temp, ' ', $options['length']);
        if ($x !== false) $temp = substr($temp, 0, $x) . '...';
    }
    $message = str_replace('{content}', $temp, $message);

    if (defined('COMMENT_NOTIFIER_EXTRAS'))
    {
        $message = cmnt_replace_extra($message, $data);
    }

    return $message;
}

/**
 * Sends out the notification of a new comment for subscribers. This is the core function
 * of this plugin. The notification is not sent to the email address of the author
 * if the comment.
 */
function cmnt_notify($comment_id)
{
    global $wpdb;

    //@set_time_limit(0);

    $options = get_option('cmnt');
    $comment = get_comment($comment_id);

    if ($comment->comment_type == 'trackback' || $comment->comment_type == 'pingback')
    {
        //cmnt_log('Notify blocked, comment type: ' . $comment->comment_type);
        return;
    }

    $post_id = $comment->comment_post_ID;
    if (empty($post_id)) {
        //cmnt_log('Empty post ID on comment ID ' . $comment_id);
        return;
    }
    $email = strtolower(trim($comment->comment_author_email));

    //cmnt_log('Sending notifications for post: ' . $post_id);

    $subscriptions = $wpdb->get_results(
        $wpdb->prepare("select * from " . $wpdb->prefix . "comment_notifier where post_id=%d and email<>%s",
        $post_id, $email) );

    if (!$subscriptions)
    {
        //cmnt_log('No suscriptions found for post ' . $post_id);
        return;
    }


    // Fill the message body with same for all data.
    $post = get_post($post_id);
    if (empty($post)) {
        //cmnt_log('Not able to retrieve the post from post ID ' . $post_id);
        return;
    }
    
    $data = new stdClass();
    $data->post_id = $post_id;
    $data->title = $post->post_title;
    $data->link = get_permalink($post_id);
    $data->comment_link = $data->link . '#comment-' . $comment_id;

    //cmnt_log('Post title: ' . $post->post_title);

    $comment = get_comment($comment_id);
    $data->author = $comment->comment_author;
    $data->content = $comment->comment_content;

    $message = cmnt_replace($options['message'], $data);

    // Fill the message subject with same for all data.
    $subject = $options['subject'];
    $subject = str_replace('{title}', $post->post_title, $subject);
    $subject = str_replace('{author}', $comment->comment_author, $subject);

    $url = get_option('home') . '/?';

    if (!empty($options['copy']))
    {
        $fake->token = 'fake';
        $fake->id = 0;
        $fake->email = $options['copy'];
        $fake->name = 'Test subscriber';

        $subscriptions[] = $fake;
    }

    $idx = 0;
    $ok = 0;
    foreach ($subscriptions as $subscription)
    {
        $idx++;
        $m = $message;
        $m = str_replace('{name}', $subscription->name, $m);
        $m = str_replace('{unsubscribe}', $url . 'cmnt_id=' . $subscription->id . '&cmnt_t=' . $subscription->token, $m);

        $s = $subject;
        $s = str_replace('{name}', $subscription->name, $s);

        if (cmnt_mail($subscription->email, $s, $m)) $ok++;
    }
    //cmnt_log('Sent ' . $idx . ' emails. ' . $ok . ' with success');
}

/**
 * Subscribe for a post the user with th email and name passed as parameters.
 */
function cmnt_subscribe($post_id, $email, $name)
{
    global $wpdb;

    //cmnt_log('Start a new subscription');

    // Checks if the user is already subscribed to this post
    $subscribed = $wpdb->get_var(
        $wpdb->prepare("select count(*) from " . $wpdb->prefix . "comment_notifier where post_id=%d and email=%s",
        $post_id, $email));

    // Akready subscribed, go out of there...
    if ($subscribed > 0)
    {
        //cmnt_log('This address is already suscribed');
        return;
    }
    // The random token for unsubscription
    $token = md5(rand());
    $res = $wpdb->insert($wpdb->prefix ."comment_notifier", array(
        'post_id' => $post_id,
        'email' => $email,
        'name' => $name,
        'token' => $token ));
}

add_action('init', 'cmnt_init');
function cmnt_init()
{
    $options = get_option('cmnt');

    if (is_admin())
    {
        add_action('admin_menu', 'cmnt_admin_menu');
        add_filter("plugin_action_links_comment-notifier/plugin.php", 'cmnt_plugin_action_links');
    }

    add_action('comment_form', 'cmnt_comment_form', 99);
    add_action('wp_set_comment_status', 'cmnt_wp_set_comment_status', 10, 2);
    add_action('comment_post', 'cmnt_comment_post', 10, 2);

    if (empty($_GET['cmnt_id'])) return;

    $token = $_GET['cmnt_t'];
    $id = $_GET['cmnt_id'];


    cmnt_unsubscribe($id, $token);

    if ($options['unsubscribe_url']) header('Location: ' . $options['unsubscribe_url']);
    else
    {
        echo '<html><head>';
        echo '<meta http-equiv="refresh" content="3;url=' . get_option('home') . '"/>';
        echo '</head><body>';
        echo $options['thankyou'];
        echo '</body></html>';
    }

    flush();

    die();
}

/**
 * Removes a subscription.
 */
function cmnt_unsubscribe($id, $token)
{
    global $wpdb;

    $wpdb->query($wpdb->prepare("delete from " . $wpdb->prefix . "comment_notifier where id=%d and token=%s", $id, $token));

    //cmnt_log('Removed suscription id ' . $id);
}

function cmnt_mail(&$to, &$subject, &$message, $html=null)
{
    $options = get_option('cmnt');

    if ($html == null) $html = isset($options['html']);

    if ($html)
        $headers .= "Content-type: text/html; charset=UTF-8\n";
    else
        $headers .= "Content-type: text/plain; charset=UTF-8\n";

    $headers .= 'From: "' . $options['name'] . '" <' . $options['from'] . ">\n";

    return wp_mail($to, $subject, $message, $headers);
}


add_action('activate_comment-notifier/plugin.php', 'cmnt_activate');
function cmnt_activate()
{
    global $wpdb;
    //   $wpdb->query("RENAME TABLE " . $wpdb->prefix . "subscriptions TO " . $wpdb->prefix . "comment_notifier");

    // SQL to create the table
    $sql = 'create table if not exists ' . $wpdb->prefix . 'comment_notifier (
        `id` int unsigned not null AUTO_INCREMENT,
        `post_id` int unsigned not null default 0,
        `name` varchar (100) not null default \'\',
        `email` varchar (100) not null default \'\',
        `token` varchar (50) not null default \'\',
        primary key (`id`),
        unique key `post_id_email` (`post_id`,`email`),
        key `token` (`token`)
        )';

    @$wpdb->query($sql);

    @include_once(dirname(__FILE__) . '/languages/en_US_options.php');
    if (WPLANG != '') @include_once(dirname(__FILE__) . '/languages/' . WPLANG . '_options.php');

    $options = get_option('cmnt', array());
    $options = array_merge($default_options, $options);
    update_option('cmnt', $options);
}

function cmnt_admin_menu()
{
    add_options_page('Comment Notifier', 'Comment Notifier', 'manage_options', 'comment-notifier/options.php');
}

function cmnt_plugin_action_links($links)
{
    $settings_link = '<a href="options-general.php?page=comment-notifier/options.php">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}


/**
 * Append a line to the log file
 */
function cmnt_log($text)
{
    $options = get_option('cmnt');
    if (!isset($options['logs'])) return;
    $file = fopen(dirname(__FILE__) . '/comment-notifier.log', 'a');
    fwrite($file, $text . "\n");
    fclose($file);
}

?>
