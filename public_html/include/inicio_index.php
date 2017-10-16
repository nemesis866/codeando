<?php
/************************************************
Archivo con contenido de la pagina de inicio

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compugmail.com
Web: http://www.pauloandrade1.com
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

<div id="presentacion" class="center parallax">
	<div id="texto">
		CURSOS EN LÍNEA
	</div>
	<div id="descripcion">
		<p>Tu donación nos ayudara a seguir con este proyecto.</p>
	</div>
	<div id="paypal">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="T46F9NWENQ86A">
			<button class="icon-paypal">Paypal<br><span>Clic para donar</span></button>
		</form>
	</div>
</div>

<section id="wrapper">
<div id="content">

<div id="rutas">
	<p class="icon-home"><a href="/">Inicio</a><span id="rutas_info">Usted esta aqui</span></p>
</div>

<?php
// Mostramos redes sociales
$template->mostrar_redes();
?>

<div class="center">
	<?php
	if(!$_SESSION['logged_in']){
		?>
		<button id="login_button" class="icon-usuario button login_button large left">Inicio de sesion</button>
		<button id="login_init" class="icon-platform button login_in large left" onclick="javascript:router('cursos',0)">Ver cursos disponibles</button>
		<?php
	} else {
		?>
		<button class="icon-platform button login_in large left" onclick="javascript:router('cursos',0)">Ver cursos disponibles</button>
		<?php
	}
	?>
</div>

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

	<h2>En codeando aprenderas</h2>

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
				<p>Aprenderas a construir tus propios sitios web con las nuevas tecnologías, buenas practicas y con las herramientas más utilizadas actualmente.</p>
			</div>
		</div>
		<div class="content">
			<h3>Crear APP</h3>
			<img src="/img/movil.png" alt="Crear app moviles">
			<div>
				<p>Las app moviles son indispensables hoy en dia, en codeando aprenderas desde los conceptos basicos para crear app hasta la forma de distribuirlas.</p>
			</div>
		</div>
	</div>
</div>