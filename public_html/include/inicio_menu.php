<?php
/************************************************
Archivo que contiene el menu para inicio

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
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

<div id="nav">
	<div id="nav_wrapper">
		<div id="nav_1">
			<div id="buscador" class="icon-find">
				<input type="text" id="q" placeholder="Buscar en codeando.org">
			</div>
		</div>
		<div id="nav_2">
			<nav>
				<ul>
					<li id="nav1"><a href="/">Inicio</a></li>
					<li id="nav2"><a href="/cursos/">Cursos</a></li>
					<li><a href="http://blog.codeando.org">Blog</a></li>
					<?php
					// Verificamos que el contenido premium este activado
					if($premium){
						?>
						<li id="nav3"><a href="/premium/">Contenido exclusivo</a></li>
						<?php
					}
					?>
					<li id="nav4"><a href="/contacto/">Contactanos</a></li>
					<?php
					// Mostramos url al admin
					if($_SESSION['logged_in']){
						?>
						<li><a href="/admin-co/">Admin</a></li>
						<?php
					}
					?>
					<li id="button_admin" class="none"><a href="/admin-co/">Admin</a></li>
				</ul>
			</nav>
		</div>
	</div>
</div>
<span id="display-menu" class="icon-menu-inicio"></span>