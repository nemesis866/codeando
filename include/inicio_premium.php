<?php
/************************************************
Mostramos el contenido premium de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
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

<div id="presentacion_premium" class="center parallax">
	<h2>Contenido Exclusivo</h2>
	<p>En esta seccion encontraras contenido de pago.</p>
</div>

<section id="wrapper">
<div id="content">

<div id="rutas">
	<p class="icon-home"><a href="/">Inicio</a> / <a href="/premium/">Contenido Premium</a><span id="rutas_info">Usted esta aqui</span></p>
</div>

<?php
// Mostramos redes sociales
$template->mostrar_redes();
?>

<p>Contenido Premium</p>