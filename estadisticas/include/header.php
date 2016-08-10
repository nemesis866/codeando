<?PHP
/************************************************
Cabecera del sistema de estadisticas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

// Verifica si el usuario que esta ingresando esta logeado
if ($_SESSION['logged_in'] != true){
	header('Location: ../');
}

if(empty($_GET['page'])){ $page = '';} else { $page = addslashes($_GET['page']);}

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Estadisticas - Codeando.org</title>
	<link href="/estadisticas/chilistats.css" rel="stylesheet" type="text/css" />
	<base href='/'>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="/estadisticas/js/jquery-1.10.2.min.js"><\/script>')</script>
	<script type="text/javascript" src="/js/fnc.js"></script>
	<script src="/estadisticas/js/stats.js"></script>
</head>
<body id="<?php echo $page; ?>">
	<header>
		<div id="header">
			<div id="head_1">
				<img src="/img/logo_opt.png">
				<h1>Codeando.org</h1>
				<h2>Sistema de estadisticas</h2>
			</div>
		</div>
	</header>
	<nav>
		<ul>
			<li id="nav1"><a href="/estadisticas/inicio/">Inicio</a></li>
			<li id="nav2"><a href="/estadisticas/visitantes/">Visitantes</a></li>
			<li id="nav3"><a href="/estadisticas/historial/">Historial</a></li>
			<li><a href="#" onclick="javascript:cerrar();">Cerrar</a></li>
		</ul>
	</nav>
	<div id="container">