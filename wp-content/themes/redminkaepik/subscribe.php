<?php
/*
Template Name: Subscribe To Comments
*/

if (isset($wp_subscribe_reloaded)){ global $posts; $posts = $wp_subscribe_reloaded->subscribe_reloaded_manage(); }
genesis();

?>