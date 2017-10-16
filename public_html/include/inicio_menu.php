<?php
/************************************************
Archivo que contiene el menu para inicio

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
************************************************/

// Seguridad
if(empty($_SESSION['logged_in'])){
	$_SESSION['logged_in'] = false;
}
if(empty($_SESSION['nivel'])){
	$_SESSION['nivel'] = 1;
}

// Incluimos el archivo de configuracion
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
?>

<header>
	<div id="header">
		<?php
		// Verificamos si estamos mostrando un tema
		if(empty($id_tema)){
			// Si no mostramos tema
			?>
			<h1><a href="/">CODEANDO</a></h1>
			<?php
		} else {
			// Si mostramos un tema
			?>
			<p><a href="/">CODEANDO</a></p>
			<?php
		}
		?>
	</div>
	<nav>
		<ul>
			<li id="nav1"><a href="/">Inicio</a></li>
			<li id="nav2"><a href="/cursos/">Cursos</a></li>
			<li><a href="http://blog.codeando.org">Blog</a></li>
			<li id="nav4"><a href="/contacto/">Contactanos</a></li>
			<?php
			// Verificamos si esta logueado
			if($_SESSION['logged_in']){
				// Mostramos url al admin y logout
				?>
				<li><a href="/admin-co/">Admin</a></li>
				<li><a href="#">Sign Out</a></li>
				<?php
			} else {
				?>
				<li><a href="#">Sign in</a></li>
				<?php
			}
			?>
		</ul>
	</nav>
</header>