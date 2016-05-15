<?php
/**
* Plugin Name: [redminka] Login personalizado
* Plugin URI: 
* Description: Login con logo y colores de redminka
* Version: 1.0
* Author: Gabriel
* Author URI:
* License: GPL2
*/


// Login logo + background
function my_login_logo() { ?>
    <style type="text/css">
			body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri()?>/images/icono-redminka.svg);
            background-size: 84px 84px;
            margin-bottom: 0;
        	}
			body.login, html { background-color: transparent;	}
html { background:url('http://redminka.com/wp-content/themes/redminka-parallax-2/images/bg-3.jpg');	}

			.login #nav a, .login #backtoblog a{
				color: #ffffff !important;
				text-shadow: 0 0px 0 #222222 !important;		
				font-weight: bold;
				}
			.login #nav a:hover, .login #backtoblog a:hover{
				color: #009B77 !important;
				}
			.login #nav, .login #backtoblog{
				margin: 0 !important;
				text-align: center;		
				background-color: #333;		
				padding: 6px;
				}
			.login h1 a{
				width: 274px;
				}
			/*a:hover, a:active{color: #D8A729 !important;	} */
			.login #loginform{
				padding-bottom: 72px !important;
				}
			.wp-core-ui .button-primary{
				background: #333 !important;	
				border:0px solid #009B77 !important;
				box-shadow: 0 0 0 #009B77  !important;
				border-radius: 0;
				/*font-size: 16px !important; 
				height: 44px !important;
				margin-bottom: 1rem !important;
				line-height: 16px !important;
				margin-top: 0.5rem;
				padding: 0 20px !important; */
				}
			.wp-core-ui .button-primary:hover, .wp-core-ui .button-primary:focus, .wp-core-ui .button-primary:active{
				background: #009B77 !important;	
				border: 0px solid #009B77 !important;
				}
			#facebook-btn-wrap {
			   display: inline-block !important;
			   margin-bottom: 16px !important;
    			}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'redminka - Red Social Sustentable';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );





?>
