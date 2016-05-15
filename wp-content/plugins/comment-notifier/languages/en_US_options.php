<?php
$default_options['message'] =  '<p>Hi {name},</p>
<p>{author} has just written a new comment on "{title}". Here an excerpt:</p>
<p>{content}</p>
<p>To read more <a href="{comment_link}">click here</a>.</p>
<p>Bye.</p>
<p>To unsubscribe this notification service, <a href="{unsubscribe}">click here</a>.</p>';
$default_options['label'] = 'Notify me when new comments are added.';
$default_options['subject'] = 'A new comment from {author} on "{title}"';
$default_options['thankyou'] = 'Your subscription has been removed. You\'ll be redirect to the home page within few seconds';
$default_options['name'] = get_option('blogname');
$default_options['from'] = get_option('admin_email');
$default_options['checkbox'] = '1';
//$default_options['checked'] = '0';
$default_options['ty_html'] = '1';
$default_options['html'] = '1';

$default_options['ty_subject'] = 'Thank you for your first comment';
$default_options['ty_message'] =
'<p>Hi {author},</p>
<p>I received and published your first comment on my blog on the
article:</p>
<p><a href="{link}">{title}</a></p>
<p>Have a nice day!</p>
';

?>
