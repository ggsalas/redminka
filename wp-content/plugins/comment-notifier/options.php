<?php
$options = get_option('cmnt');

// Protect all the action from cross-site request forgery (http://en.wikipedia.org/wiki/Cross-site_request_forgery)
if (wp_verify_nonce($_POST['_wpnonce'], 'update-comment-notifier-options')) {

    $options = stripslashes_deep($_POST['options']);
    update_option('cmnt', $options);

    if (isset($_POST['savetest'])) {
        $cmnt_data = new stdClass();
        $cmnt_data->author = 'Author name';
        $cmnt_data->link = get_option('home');
        $cmnt_data->comment_link = get_option('home');
        $cmnt_data->title = 'A wonderful post title';
        $cmnt_data->content = 'This is a long comment. I like to write unuseful words with my poor english, but I hope you all can understand me. If you can\'t i can speak italian too.';
        $message = cmnt_replace($options['message'], $cmnt_data);

        $message = str_replace('{name}', 'Subscriber name', $message);

        $message = str_replace('{unsubscribe}', get_option('home') . '/?cmnt_id=0&cmnt_t=fake', $message);


        $subject = $options['subject'];
        $subject = str_replace('{title}', $cmnt_data->title, $subject);
        $subject = str_replace('{author}', $cmnt_data->author, $subject);
        $subject = str_replace('{name}', 'Subscriber name', $subject);

        cmnt_mail($options['test'], $subject, $message);
    }

    if (isset($_POST['savethankyou'])) {
        $cmnt_data = new stdClass();
        $cmnt_data->author = 'Author';
        $cmnt_data->link = get_option('home');
        $cmnt_data->comment_link = get_option('home');
        $cmnt_data->title = 'The post title';
        $cmnt_data->content = 'This is a long comment. I like to write unuseful words with my poor english, but I hope you all can understand me. If you can\'t i can speak italian too.';
        $message = cmnt_replace($options['ty_message'], $cmnt_data);

        $subject = $options['ty_subject'];
        $subject = str_replace('{title}', $cmnt_data->title, $subject);
        $subject = str_replace('{author}', $cmnt_data->author, $subject);

        cmnt_mail($options['test'], $subject, $message, isset($options['ty_html']));
    }
}

// Removes a single email for all subscriptions
if (isset($_POST['remove_email'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'remove_email')) die('Securety violated');
    $email = strtolower(trim($_POST['email']));
    $wpdb->query($wpdb->prepare("delete from " . $wpdb->prefix . "comment_notifier where email=%s", $email));
}

if (isset($_POST['remove'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'remove')) die('Securety violated');
    $query = "delete from " . $wpdb->prefix . "comment_notifier where id in (" . implode(',', $_POST['s']) . ")";
    $wpdb->query($query);
}
?>

<script type="text/javascript">
    function cmnt_preview()
    {
        var m = document.getElementById("message").value;
        m = m.replace("{content}", "I totally agree with you opinion about Satollo, he's really...");
        var h = window.open("", "cmnt","status=0,toolbar=0,height=400,width=550");
        var d = h.document;
        d.write('<html><head><title>Email preview</title>');
        d.write('</head><body>');
        d.write('<table width="100%" border="1" cellspacing="0" cellpadding="5">');
        d.write('<tr><td align="right"><b>Subject</b></td><td>' + document.getElementById("subject").value + '</td></tr>');
        d.write('<tr><td align="right"><b>From</b></td><td>' + document.getElementById("from_name").value + ' &lt;' + document.getElementById("from_email").value + '&gt;</td></tr>');
        d.write('<tr><td align="right"><b>To</b></td><td>User name &lt;user@email&gt;</td></tr>');
        d.write('<tr><td align="left" colspan="2">' + m + '</td></tr>');
        d.write('</table>');
        d.write('</body></html>');
        d.close();
        return false;
    }
</script>

<div class="wrap">

    <div id="satollo-header">
        <a href="http://www.satollo.net/plugins/comment-notifier" target="_blank">Get Help</a>
        <a href="http://www.satollo.net/forums" target="_blank">Forum</a>

        <form style="display: inline; margin: 0;" action="http://www.satollo.net/wp-content/plugins/newsletter/do/subscribe.php" method="post" target="_blank">
            Subscribe to satollo.net <input type="email" name="ne" required placeholder="Your email">
            <input type="hidden" name="nr" value="comment-notifier">
            <input type="submit" value="Go">
        </form>
        <!--
        <a href="https://www.facebook.com/satollo.net" target="_blank"><img style="vertical-align: bottom" src="http://www.satollo.net/images/facebook.png"></a>
        -->
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5PHGDGNHAYLJ8" target="_blank"><img style="vertical-align: bottom" src="http://www.satollo.net/images/donate.png"></a>
        <a href="http://www.satollo.net/donations" target="_blank">Even <b>1$</b> helps: read more</a>
    </div>

    <h2>Comment Notifier</h2>
    
    <p>
        <a href="http://www.satollo.net/plugins/comment-plus" target="_blank">Comment Plus</a> is an extended version of Comment Notifier with
        much more options, integrated Comment Image plugin and Smilies for your comments.
    </p>

    <form action="" method="post">
        <?php wp_nonce_field('remove_email') ?>
        <h3>Email management</h3>
        <table class="form-table">
            <tr>
                <th></th>
                <td>
                    Remove this email: <input type="text" name="email" size="30"/>
                    <input type="submit" name="remove_email" class="button-secondary" value="Remove"/>
                </td>
            </tr>
        </table>
    </form>

    <form action="" method="post">
        <?php wp_nonce_field('update-comment-notifier-options') ?>
        <h3>Subscription checkbox configuration</h3>
        <table class="form-table">
            <tr>
                <th>Auto add the checkbox</th>
                <td>
                    <input type="checkbox" name="options[checkbox]" value="1" <?php echo isset($options['checkbox']) ? 'checked' : ''; ?> />
                    if checked the "notify me" checkbox will be inserted in the comment form by Comment Notifier (not all themes
                    support this function, read below)

                    <br /><br />
                    <?php
                    $commentsphp = @file_get_contents(get_template_directory() . '/comments.php');
                    if (strpos($commentsphp, 'comment_form') === false) {
                        echo '<strong>Your theme seems to NOT have the "comment_form" action call. Read below.</strong><br /><br />';
                    }
                    ?>

                    Your theme needs to have the call <code>&lt;?php do_action('comment_form', $post->ID); ?&gt;</code> (usually on comments.php
                    theme file). Not all themes have it. If you want to add manually the subscription checkbox use the code an unchecked checkbox<br />
                    &nbsp;&nbsp;&nbsp;<code>&lt;input type="checkbox" value="1" name="subscribe" id="subscribe"/&gt;</code><br />
                    or the one below for a checked checkbox<br />
                    &nbsp;&nbsp;&nbsp;<code>&lt;input type="checkbox" value="1" name="subscribe" id="subscribe" checked="checked"/&gt;</code>
                </td>
            </tr>

            <tr>
                <th>Subscription checkbox label</th>
                <td>
                    <input name="options[label]" type="text" size="50" value="<?php echo htmlspecialchars($options['label']) ?>"/>
                    <br />
                    Label to be displayed near the subscription checkbox
                </td>
            </tr>
            <tr>
                <th>Checkbox default status</th>
                <td>
                    <input type="checkbox" name="options[checked]" value="1" <?php echo isset($options['checked']) ? 'checked' : ''; ?> />
                    if checked the "notify me" checkbox will be cheked by default
                </td>
            </tr>
        </table>

        <h3>Notification email settings</h3>
        <table class="form-table">
            <tr>
                <th>Notification sender name</th>
                <td>
                    <input name="options[name]" id="from_name" type="text" size="50" value="<?php echo htmlspecialchars($options['name']) ?>"/>
                </td>
            </tr>

            <tr>
                <th>Notification sender email</th>
                <td>
                    <input name="options[from]" id="from_email" type="text" size="50" value="<?php echo htmlspecialchars($options['from']) ?>"/>
                </td>
            </tr>
            <tr>
                <th>Notification subject</th>
                <td>
                    <input name="options[subject]" id="subject" type="text" size="70" value="<?php echo htmlspecialchars($options['subject']) ?>"/>
                    <br />
                    Tags: {title} - the post title, {name} - the subscriber name, {author} - the commenter name
                </td>
            </tr>

            <tr>
                <th>Notification message body</th>
                <td>
                    <input type="checkbox" name="options[html]" value="1" <?php echo $options['html'] != null ? 'checked' : ''; ?> /> send emails as html
                    (<a href="javascript:void(cmnt_preview());">preview</a>)
                    <br />
                    <textarea name="options[message]" id="message" wrap="off" rows="10" style="width: 100%"><?php echo htmlspecialchars($options['message']) ?></textarea>
                    <br />
                    Tags: {name} - the subscriber name, {unsubscribe} - the unsubscription link, {title} - the post title,
                    {author} - the commenter name, {link} - the post link, {content} - the comment text (eventually
                    truncated).<br /><br />
                    If you have the "<a href="http://www.satollo.net/plugins/comment-notifier#extras">Comment Notifier Extras</a>" installed
                    there are other tags available: [last_posts] - a list
                    of last blog posts, [ads] - a randomly ad text or affiliate link, [related_posts] - a list
                    of post related to the one commented. See below for detailed
                    configuration of those tags.
                </td>
            </tr>
            <tr>
                <th>Comment excerpt length</th>
                <td>
                    <input name="options[length]" type="text" size="5" value="<?php echo htmlspecialchars($options['length']) ?>"/> characters
                    <br />
                    Sets the length of the comment excerpt to be inserted in the notification.
                </td>
            </tr>
        </table>

        <h3>Unsubscription setting</h3>
        <p>
            Here you can configure what to show to unsubscribing users. Set an "Unsunscription page URL"
            to send the user on a specific blog page or post or configure a specific message.
        </p>

        <table class="form-table">
            <tr>
                <td>
                    <label>Unsubscription page URL</label><br />
                    <input name="options[unsubscribe_url]" type="text" size="50" value="<?php echo htmlspecialchars($options['unsubscribe_url']) ?>"/>
                    <br />
                    (if you want to create a page with your content to say "ok, you're unsubscribed" put here its
                    URL otherwise leave this field blank and the subsequent configurations will be used).
                </td>
            </tr>
            <tr>
                <td>
                    <label>Unsubscription message</label><br />
                    <textarea name="options[thankyou]" wrap="off" rows="7" style="width: 500px"><?php echo htmlspecialchars($options['thankyou']) ?></textarea>
                    <br />
                    (Example: You have unsubscribe successfully. Thank you. I'll be send to the home page in 3 seconds.)<br />
                </td>
            </tr>
        </table>

        <p class="submit">
            <input class="button-primary" type="submit" name="save" value="Save"/>
            <input class="button-primary" type="submit" name="savetest" value="Save and send a test email"/>
        </p>


        <h3>Thank you message settings</h3>
        <p>Configure a thank you message for <strong>first time commenters</strong>.
            Messages are sent when comments are approved.</p>

        <table class="form-table">
            <tr>
                <td>
                    <input type="checkbox" name="options[ty_enabled]" value="1" <?php echo isset($options['ty_enabled']) ? 'checked' : ''; ?> />
                    enable first comment thank you message
                </td>
            </tr>
            <tr>
                <th>Subject</th>
                <td>
                    <input name="options[ty_subject]" type="text" size="70" value="<?php echo htmlspecialchars($options['ty_subject']) ?>"/>
                    <br />
                    Tags: {title} - the post title, {author} - the commenter name
                </td>
            </tr>
            <tr>
                <th>Notification message body</th>
                <td>
                    <input type="checkbox" name="options[ty_html]" value="1" <?php echo isset($options['ty_html']) ? 'checked' : ''; ?> /> send emails as html
                    <br />
                    <textarea name="options[ty_message]" wrap="off" rows="10" cols="70" style="width: 500px"><?php echo htmlspecialchars($options['ty_message']); ?></textarea>
                    <br />
                    Tags: {title} - the post title,
                    {author} - the commenter name, {link} - the post link, {content} - the comment text.<br /><br />
                    If you have the "<a href="http://www.satollo.net/plugins/comment-notifier#extras">Comment Notifier Extras</a>" installed
                    there are other tags available: [last_posts] - a list
                    of last blog posts, [ads] - a randomly ad text or affiliate link, [related_posts] - a list
                    of post related to the one commented.
                </td>
            </tr>
        </table>

        <p class="submit">
            <input class="button-primary" type="submit" name="save" value="Save"/>
            <input class="button-primary" type="submit" name="savethankyou" value="Save and send a thank you test email"/>
        </p>

        <?php if (defined('COMMENT_NOTIFIER_EXTRAS')) cmnt_options_extra(); ?>

        <h3>Advanced settings</h3>
        <table class="form-table">
            <tr>
                <td>
                    <label>Email address where to send a copy of EACH notification</label><br />
                    <input name="options[copy]" type="text" size="50" value="<?php echo htmlspecialchars($options['copy']) ?>"/>
                    <br />
                    (Leave empty to disable. Do not use the sender address, some mail servers don't accept "from" and "to" setted
                    to the same value)
                </td>
            </tr>
            <tr>
                <td>
                    <label>Email address where to send test emails</label><br />
                    <input name="options[test]" type="text" size="50" value="<?php echo htmlspecialchars($options['test']) ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="options[logs]" value="1" <?php echo $options['logs'] != null ? 'checked' : ''; ?> />
                    <label for="options[logs]">enable logs (used by me when developing)</label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input class="button button-primary" type="submit" name="save" value="Save"/>
        </p>

    </form>


    <form action="" method="post">
        <?php wp_nonce_field('remove') ?>
        <h3>Long list of subscribers</h3>
        <p>This section is under developement.</p>
        <ul>
            <?php
            $list = $wpdb->get_results("select distinct post_id, count(post_id) as total from " . $wpdb->prefix . "comment_notifier group by post_id order by total desc");

            foreach ($list as $r) {
                $post = get_post($r->post_id);
                $link = get_permalink($r->post_id);
                echo '<li><a href="' . $link . '" target="_blank">' . $post->post_title . '</a> (id: ' . $r->post_id . ', subscribers: ' . $r->total . ')</li>';
                $list2 = $wpdb->get_results("select id,email,name from " . $wpdb->prefix . "comment_notifier where post_id=" . $r->post_id);
                echo '<ul>';
                foreach ($list2 as $r2) {
                    echo '<li><input type="checkbox" name="s[]" value="' . $r2->id . '"/> ' . $r2->email . '</li>';
                }
                echo '</ul>';
                echo '<input type="submit" name="remove" value="Remove"/>';
            }
            ?>
        </ul>

    </form>

</div>
