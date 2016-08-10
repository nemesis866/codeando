<?php
/************************************************
Archivo con contenido de la pagina de contacto
Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Inicializamos los objetos
$social = new Social();
$fnc = new Fnc();
$template = new Template();
?>

<div id="rutas">
	<p class="icon-home"><a href="/">Inicio</a> / <a href="/contacto/">Contacto</a><span id="rutas_info">Usted esta aqui</span></p>
</div>

<?php
// Mostramos redes
$template->mostrar_redes();
?>

<p>Tienes algun comentario o sugerencia? contactanos</p>
<form id="form_contacto">
	<p><label>Nombre:</label></p>
	<p><input type="text" id="name" placeholder="Ingrese su nombre" required></p>
	<p><label>Email:</label></p>
	<p><input type="text" id="email" placeholder="Ingrese su email"></p>
	<p><label>Asunto:</label></p>
	<p><select id="asunto" required>
		<option></option>
		<option value="1">Sugerencia</option>
		<option value="2">Bug/Error</option>
		<option value="3">Comentario</option>
	</select></p>
	<p><label>Comentario:</label></p>
	<p><textarea id="contenido" placeholder="Ingrese su comentario" required></textarea></p>
	<p><input type="submit" id="submit_contacto" value="Enviar"></p>
</form>
<div class="cargando"></div>