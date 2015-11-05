<?php
/********************************************************************
Clase principal del sistema de Administracion

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

// Configuramos la zona horaria
date_default_timezone_set('America/Mexico_City');

class Admin
{
	private $_title;
	
	public function set_title($title){
		$this->_title = $title;
	}
	public function html_header()
	{
		//funcion que muestra el contenido de las etiquetas header
		?>
		<!DOCTYPE html>
		<html lang="es">
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo $this->_title; ?></title>
			<!--<link href='http://fonts.googleapis.com/css?family=Chivo' rel='stylesheet' type='text/css'>-->
			<link rel="stylesheet" href="/css/min/admin.css">
			<link rel="SHORTCUT ICON" href="/favicon.ico">
			<base href="/">
		</head>
	    <body>
		<?php
	}
	public function html_encabezado()
	{
		// Muestra el titulo de la pagina
		if($_SESSION['gender'] == 'male'){
			$return = 'Bienvenido';
		} else {
			$return = 'Bienvenida';
		}
		?>
		<header>
			<?php
			// Verificamos si iniciamos sesion con facebook
			if($_SESSION['logged_fb']){
				?><img src="http://graph.facebook.com/<?php echo $_SESSION['user_id']; ?>/picture?type=large" alt="avatar"><?php
			} else {
				?><img src="/avatar/<?php echo $_SESSION['avatar']; ?>" id="header_avatar" alt="avatar"><?php
			}
			?>
			<h1><?php echo $return.' '.$_SESSION['nombre']; ?></h1>
		</header>
		<section id="wrapper">
	    <?php
	}
	public function html_contenido()
	{
		// Muestra el area de contenido
		$db = new Db();
		$user = $_SESSION['id'];
		?>
	    <section id="contenido">
	        <aside id="contenido_a">
	        	<h2 class="pprincipal">Menu</h2>
				<ul id="menu">
					<li><a href="/admin-co/">Inicio</a></li>
					<li><a href="/admin-co/?category=course">Cursos</a></li>
					<?php
					// Verificamos que el usuario tenga cursos en la plataforma
					$result = $db->mysqli_select("SELECT Count(id_curso) FROM cursos WHERE autor='$user'");
					$count = $result->fetch_row();
					$result->close();

					if($count[0] > 0){
						?><li><a href="/admin-co/?category=notices">Avisos</a></li><?php
					}
					?>
					<li><a href="/admin-co/?category=profile">Perfil</a></li>
					<li><a href="/admin-co/?category=category">Categorias</a></li>
					<?php
					if($_SESSION['nivel'] == 10){
						?><li><a href="/estadisticas/inicio/" target="_blank">Estadisticas</a></li><?php
					} ?>
					<li><a href="/">Salir</a></li>
				</ul>
	        </aside>
	        <article id="contenido_b">
	            <?php require_once 'include/admin_contenido.php'; ?>
	        </article>
	    </section>
	    </section>
	    <?php
	}
	public function html_footer()
	{
		// Muestra el footer
		?>
		<footer>
			<p>Sistema de Administración - Codeando.org <?php echo date('Y'); ?></p>
		</footer>
		<div class="error"></div>
		<div class="success"></div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
		<script src="/js/vendor/modernizr-2.6.2.min.js"></script>
		<script src="/js/min/plugins.js"></script>
		<script src="/js/min/fnc.js"></script>
		<script src="/js/min/drag.js"></script>
		<script src="/js/min/admin.js"></script>
	    </body>
	    </html>
	    <?php
	}
	public function mostrar_contenido()
	{
		// Muestra el contenido
		self::html_header();
		self::html_encabezado();
		self::html_contenido();
		self::html_footer();
	}
	public function verificacion(){
		//funcion que verifica si el usuario que esta ingresando esta logeado

		// Seguridad
		if(empty($_SESSION['logged_in'])){
			$_SESSION['logged_in'] = false;
		}

		if ($_SESSION['logged_in'] != true){
			header('Location: ../');
		}
	}
}
?>