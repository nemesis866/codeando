<?php
/********************************************************************
Solicitar categorias

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

// Iniciamos la base de datos
$db = new Db();

?>
<div class="alert">
	<img src="/img/alert.png">
	<h1>Bienvenido aqui podra solicitar una categoria nueva.</h1>
	<p>Envie el siguiente formulario para solicitar la categoria nueva, a la brevedad posible obtendra respuesta a su solicitud.</p>
</div>

<div class="cargando"></div>

<form id="form_solicitud">
	<label>Ingrese la categoria a solicitar:
	<p><input type="text" id="name" class="input" maxlength="20" required>
		<span id="count_name" class="count">20</span></p>
	<p><input type="submit" class="submit" value="Solicitar categoris"></p>
</form>