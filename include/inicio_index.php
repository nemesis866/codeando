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

<div id="contenido">
	<h2>CURSOS 100% GRATUITOS</h2>
	<div class='contenido'>
		<img data-tooltip="Cursos divididos por capitulos" src="https://lh4.googleusercontent.com/-59IzQ_9Jccg/VJJKWWr2juI/AAAAAAAAB8I/oxZ3dXQnAE8/w480-h270-no/Untitled1111.gif" alt="Cursos gratis">
	</div>
	<div class="contenido texto">
		<p>Te gusta la programación? en codeando.org ofrecemos cursos gratuitos con los que podras aprender a programar y/o a desarrollar tus propios proyectos, si ya cuentas con bases en la programación podras ampliar tus conociemientos, la plataforma se encuentra en fase beta, si presentas alguna falla técnica o visual por favor póngase en contacto con el <a href="/contacto/">equipo de codeando.org</a> para corregir el problema a la brevedad posible.</p>
	</div>

	<h2>EXCELENTE DOCUMENTACIÓN</h2>
	<div class="contenido texto">
		<p>Todos los temas de cada uno de los cursos cuenta con su propia documentación, con esto tratamos de que el aprendizaje sea lo más simple y explicito posible tanto para usuarios novatos como experimentados, además cada tema cuenta con los ejemplos (codigos) finales para que puedas comparar tu código con el explicado en el tema.</p>
	</div>
	<div class='contenido'>
		<img src="https://lh6.googleusercontent.com/-7iE0lk3LL6E/VJJO_uV7i-I/AAAAAAAAB8g/t8mY4EaRJ9c/w480-h270-no/Untitled6.gif" alt="Documentación" data-tooltip="Documentacion personalizada por temas">
	</div>

	<h2>SISTEMA DE DISCUSIONES</h2>
	<div class='contenido'>
		<img src="https://lh4.googleusercontent.com/-e71trD0LQt0/VJJNebXQXFI/AAAAAAAAB8Y/vTzYR8XZaoQ/w480-h270-no/Untitled3.gif" alt="Sistema de discusiones" data-tooltip="Sistema de discusiones para resolver tus dudas">
	</div>
	<div class="contenido texto">
		<p>Te quedaron dudas? no te preocupes, codeando.org cuenta con un potente sistema de discusiones en el que podrás publicar dudas, aportes, además cuentas con la opción de subir tus archivos a la plataforma ya sea para aportar o para recibir ayuda. Con este sistema los usuarios de la plataforma se podran ayudar entre si para mejorar su experiencia de aprendizaje, ayudanos a seguir mejorando.</p>
	</div>

	<h2>Codeando.org esta disponible para los navegadores</h2>
	<div id="navegadores">
		<p>IE 10+, Google Chrome, Mozilla Firefox, Opera y Safari.</p>
		<div id="nave">
			<div class="navegador">
				<img src="https://lh6.googleusercontent.com/-1BAjJfiarBg/VCwYxAr4nlI/AAAAAAAABwo/gB_OoKIxAb0/s256-no/ie-logo.png" alt="IE" data-tooltip="Internet Explorer">
			</div>
			<div class="navegador">
				<img src="https://lh4.googleusercontent.com/-RVewxLFMN2U/VCwYws2DkTI/AAAAAAAABwg/imqpq5g_fyE/w400-h300-no/chrome_logo.png" alt="Chrome" data-tooltip="Google Chrome">
			</div>
			<div class="navegador">
				<img src="https://lh5.googleusercontent.com/-17qktDHFdNs/VCwYwjz9ExI/AAAAAAAABwk/49rlgdGQRGI/s256-no/Firefox_logo.png" alt="Firefox" data-tooltip="Mozilla Firefox">
			</div>
			<div class="navegador">
				<img src="https://lh5.googleusercontent.com/-1PoAM9YVVbo/VCwYx21SY9I/AAAAAAAABww/SB7cbiQOgkc/s500-no/opera_Logo.png" alt="Opera" data-tooltip="Opera">
			</div>
			<div class="navegador">
				<img src="https://lh5.googleusercontent.com/-htXtdAUQBF8/VCwYy4BcpMI/AAAAAAAABw4/YZJSU0UBvao/w265-h300-no/safari_logo.png" alt="Safari" data-tooltip="Safari">
			</div>
		</div>
	</div>
</div>