<?php
/************************************************
Archivo con contenido de la pagina de inicio

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compugmail.com
************************************************/

// Inicializamos los objetos
$social = new Social();
$fnc = new Fnc();
$db = new Db();
$template = new Template();

// Seguridad
if(empty($_SESSION['logged_in'])){
	$_SESSION['logged_in'] = false;
}
?>

<div id="wrapper">
	<div id="home">
		<h2>Que encontraras en codeando</h2>

		<div class="home-box">
			<img src="/img/learn.png" alt="Aprender">
		</div>
		<div class="home-box justify">
			<p>Cursos de programación en video con los que podrás aprender a tu propio ritmo, a la hora que puedas y con una gran comunidad dispuesta a ayudarte a resolver tus dudas.</p>
		</div>
		<div class="home-box">
			<img src="/img/forums.png" alt="Sistema de discusiones">
		</div>
		<div class="home-box justify">
			<p>Cada curso cuenta con un poderoso sistema de discusiones, para aclarar todas las dudas que te puedan surgir, además de un sistema de notas para respaldar tus aprendizajes.</p>
		</div>
		<div class="home-box">
			<img src="/img/cloud.png" alt="Plataforma">
		</div>
		<div class="home-box justify">
			<p>Acceso directo y gratuito a la plataforma online de codeando donde encontraras infinidad de cursos y temas para aprender las nuevas tecnologías en el desarrollo web.</p>
		</div>

		<h2>En codeando aprenderás</h2>

		<div id="home-content">
			<div class="content">
				<h3>Leer y entender código</h3>
				<img src="/img/editor.png" alt="Editor de código">
				<div>
					<p>El 80% del tiempo lo invertimos leyendo y entendiendo código, en codeando te enseñaremos a programar con buenas practicas para reducir esta estadística.</p>
				</div>
			</div>
			<div class="content">
				<h3>Crear páginas web</h3>
				<img src="/img/web.png" alt="Crear páginas web">
				<div>
					<p>Aprenderás a construir tus propios sitios web con las nuevas tecnologías, buenas practicas y con las herramientas más utilizadas actualmente.</p>
				</div>
			</div>
			<div class="content">
				<h3>Crear APP</h3>
				<img src="/img/movil.png" alt="Crear app moviles">
				<div>
					<p>Las APPs móviles son indispensables hoy en día, en codeando aprenderás desde los conceptos básicos para crear APP hasta la forma de distribuirlas.</p>
				</div>
			</div>
		</div>
	</div>
</div>