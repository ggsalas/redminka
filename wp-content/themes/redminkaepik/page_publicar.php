<?php

// This file adds the Landing template to the Epik Theme.

// Template Name: Publicar c-menu

// Add custom body class to the head

//add_action ('genesis_before_post_content', 'menu_publicar',10);
add_action( 'genesis_before_post_content', 'menu_template_publicar_insertar' );

function menu_template_publicar_insertar() {
      genesis_widget_area( 'menu-template-publicar', array(
         'before' => '<div id="menu-publicar-widget">',
          'after' => '</div>'
      )	);
}

add_action ('genesis_after_post_content', 'lista_borradores',10);
function lista_borradores(){
	global $bp;
	if ( is_user_logged_in() ) { 
	
		echo '<div class="pagina-borradores"><h2>Borradores</h2>';
		mostrar_borradores();
		echo '</div>';
	}else {}
	}

genesis();


