<?php 
$url = site_url();
global $bp;

if ( is_user_logged_in() ) { ?>

<ul class="menu genesis-nav-menu sf-js-enabled">
	<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home ">
		<a href=<?php echo bp_core_get_user_domain(bp_loggedin_user_id()); ?> >
		<span class="avatar-menu"> <?php	
		echo bp_core_fetch_avatar( array(
			'item_id' => bp_loggedin_user_id(),
			'width' => 30, 
			'height' => 30,
			)
		);
		?> 
		</span>
		Hola <?php echo bp_core_get_user_displayname(bp_loggedin_user_id()); ?> 
		<!-- the_author_meta('display_name', bp_loggedin_user_id()) -->
		</a>
		<ul class="sub-menu">	
			<li class="menu-item  ">
				<a href=<?php echo bp_core_get_user_domain(bp_loggedin_user_id()); ?>profile/>Perfil</a>
			</li>
			<li class="menu-item  ">
				<a href=<?php echo bp_core_get_user_domain(bp_loggedin_user_id()); ?>publicaciones/todas>Mis Publicaciones</a>
			</li>
			<li class="menu-item  ">
				<a href=<?php echo bp_core_get_user_domain(bp_loggedin_user_id()); ?>messages>Mensajes</a>
			</li>
			<li class="menu-item  ">
				<a href=<?php echo bp_core_get_user_domain(bp_loggedin_user_id()); ?>settings>Configuración</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/wp-login.php?action=logout">[x] Salir</a>
			</li>
		</ul>	
	</li>		
	<li class="menu-item  ">	
	<?php bp_adminbar_notifications_menu(); ?>
	</li>
	<li class="menu-item  ">
		<a href="<?php echo $url; ?>/publicaciones">Publicaciones</a>
		<ul class="sub-menu" >
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/notas">Notas</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/enlaces">Enlaces</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/fotos">Fotos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/videos">Videos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/documentos">Documentos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/articulos">Artículos</a>
			</li>					
		</ul>
	</li>
	<li class="menu-item  ">
		<a href="<?php echo $url; ?>/miembros">Miembros</a>
	</li> 
		<li class="menu-item">
		<a class="publicar-boton-header" title="Form Publicar" href="<?php echo $url; ?>/publicar-articulo" >Crear una Publicación ...</a>
		<ul class="sub-menu" >
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/publicar-articulo" >Artículo</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/publicar-enlace" >Enlace</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/publicar-fotos" >Fotos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/publicar-video" >Video</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/publicar-documento" >Documento</a>
			</li>
		</ul>
		</li>		
	
</ul>

<?php } else { ?>

<ul class="menu genesis-nav-menu sf-js-enabled">
	
	<li class="menu-item  ">
		<a href="<?php echo $url; ?>/publicaciones">Publicaciones</a>
		<ul class="sub-menu" >
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/notas">Notas</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/enlaces">Enlaces</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/fotos">Fotos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/videos">Videos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/documentos">Documentos</a>
			</li>
			<li class="menu-item  ">
				<a href="<?php echo $url; ?>/pub/articulos">Artículos</a>
			</li>					
		</ul>
	</li>
	<li class="menu-item  ">	
		<a href="<?php echo $url; ?>/wp-login.php">Ingresar</a> 
	</li>
	<li class="menu-item  ">	
		<a href="<?php echo $url; ?>/registrarse/">REGISTRARSE</a> 
	</li>				
	
</ul>

<!-- <input type="text"  style='height:2em; margin:0.6rem 0.875rem 0.6rem 0; border-radius: 1em;' value="Buscar..." name="s" class="s search-input" onfocus="if (this.value == 'Search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search...';}"> -->
<!--	<div style="font-size: 13px; " class="menu genesis-nav-menu widget widget_nav_menu" > 
		<form id="login" method="post" action="<?php echo wp_login_url( get_permalink() ) ?>">
			<fieldset>
				<label> 
				<input type="text" value="Usuario" name="log" size="6" class="sf-with-ul" style='width:6em; height:2em; margin:0.6rem 0.875rem 0.6rem 0 ' /></label>
				<label> 
				<input type="password" value="Contraseña" name="pwd" size="6" class="sf-with-ul" style='width:6em; margin:0.6rem 0.875rem 0.6rem 0' /></label>
				<input type="Submit" value="Acceder" class="sf-with-ul" style=' height:2em;  padding:0.6rem 0.875rem 0.6rem 0' />
			</fieldset>
		</form>
	</div>
-->
<?php } ?>
	