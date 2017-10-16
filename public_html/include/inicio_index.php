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

<div id="presentacion">
	<div id="box">
		<div id="box-1">
			<h2>Aprende a diseñar tu web, programar y mucho más.</h2>
			<p>Ingresa a la plataforma es gratuita.</p>
		</div>
		<div id="box-2">
			<?php
			// Verificamos si esta logueado
			if($_SESSION['logged_in']){
				// Si estamos logueados
				?>
				<img src="/img/editor.png" alt="Cursos gratuitos">
				<?php
			} else {
				// Si no estamos logueados
				?>
				<div id="login">
					<div id="login-head">
						<p>Empieza a aprender hoy</p>
					</div>
					<div id="login-body">
						<form name="form_contacto" novalidate>
							<p><input type="text" ng-model="form1.userName" name="userName" placeholder="UserName" ng-required="vm.formConfig.required"></p>
							<p><input type="password" ng-model="form1.password" name="password" placeholder="Password" ng-required="vm.formConfig.required"></p>
							<hr>
							<p><input type="submit" ng-click="vm.getUser(form1)" value="Ingresar" ng-disabled="form_contacto.$invalid"></p>
						</form>
					</div>
					<div id="login-footer">
						<p><a href="">Olvide mi password.</a></p>
						<p><a href="">Quiero registrarme.</a></p>
						<p><a href="#" ng-click="vm.getUserFb()">Ingresar con facebook.</a></p>
					</div>
				</div>
				<?php
				// Cerramos si no estamos logueados
			}
			?>
		</div>
	</div>
</div>
<div id="wrapper">
	<div id="home">
		<h2>Que encontraras en codeando</h2>

		<div class="home-box">
			<img src="/img/learn.png" alt="Aprender">
		</div>
		<div class="home-box left">
			<p>Cursos de programación en video con los que podras aprender a tu propio ritmo, a la hora que puedas y con una gran comunidad dispuesta a ayudarte a resolver tus dudas.</p>
		</div>
		<div class="home-box">
			<img src="/img/forums.png" alt="Sistema de discusiones">
		</div>
		<div class="home-box left">
			<p>Cada curso cuenta con un poderoso sistema de discusiones, para aclarar todas las dudas que te puedan surgir, además de un sistema de notas para respaldar tus aprendizajes.</p>
		</div>
		<div class="home-box">
			<img src="/img/cloud.png" alt="Plataforma">
		</div>
		<div class="home-box left">
			<p>Acceso directo y gratuito a la plataforma online de codeando donde encontraras infinidad de cursos y temas para aprender las nuevas tecnologias en el desarrollo web.</p>
		</div>

		<h2>En codeando aprenderaz</h2>

		<div id="home-content">
			<div class="content">
				<h3>Leer y entender código</h3>
				<img src="/img/editor.png" alt="Editor de código">
				<div>
					<p>El 80% del tiempo lo invertimos leyendo y entendiendo código, en codeando te enseñaremos a programar con buenas practicas para reducir esta estadistica.</p>
				</div>
			</div>
			<div class="content">
				<h3>Crear páginas web</h3>
				<img src="/img/web.png" alt="Crear páginas web">
				<div>
					<p>Aprenderaz a construir tus propios sitios web con las nuevas tecnologías, buenas practicas y con las herramientas más utilizadas actualmente.</p>
				</div>
			</div>
			<div class="content">
				<h3>Crear APP</h3>
				<img src="/img/movil.png" alt="Crear app moviles">
				<div>
					<p>Las app moviles son indispensables hoy en dia, en codeando aprenderaz desde los conceptos basicos para crear app hasta la forma de distribuirlas.</p>
				</div>
			</div>
		</div>
	</div>
</div>