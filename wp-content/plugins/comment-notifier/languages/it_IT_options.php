<?php
$default_options['html'] = '1';
$default_options['message'] =  '<p>Ciao {name},</p>
<p>{author} ha appena lasciato un commento su "{title}". Ecco un estratto:</p>
<p>{content}</p>
<p><strong>Per leggere il resto o rispondere<a href="{comment_link}">clicca qui</a>.</strong></p>
<p>Ciao!</p>
<p>Per non ricevere più notifiche, <a href="{unsubscribe}">clicca qui</a>.</p>';
$default_options['label'] = 'Avvisami delle risposte';
$default_options['subject'] = 'Un nuovo commento da {author} on "{title}"';
$default_options['thankyou'] = 'La tua iscrizione è stata rimossa. Tra tre secondi sarai rimandato alla pagina principale.';
$default_options['name'] = get_option('blogname');
$default_options['from'] = get_option('admin_email');
$default_options['checkbox'] = '1';
//$default_options['checked'] = '0';
$default_options['ty_html'] = '1';

$default_options['ty_subject'] = 'Grazie per il tuo primo commento';
$default_options['ty_message'] =
'<p>Ciao {author},</p>
<p>ho ricevuto e pubblicato il tuo primo commento sul mio blog all\'articolo:</p>
<p><a href="{link}">{title}</a></p>
<p>Grazie e arrivederci!</p>
';

?>
