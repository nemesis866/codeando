<?php
/************************************************
Archivo que contiene el header para inicio

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
************************************************/

// Seguridad
if(empty($_SESSION['logged_in'])){
	$_SESSION['logged_in'] = false;
}

// Obtenemos el id del tema a mostrar
$id_tema = (empty($_GET['id_tema'])) ? '' : $_GET['id_tema'];

// Obtenemos el head de la pagina (para ver que slider mostrar)
$id_head = (empty($_GET['head'])) ? '' : $_GET['head'];

// Verificamos el head del index
if(empty($id_head) && $id_head == ''){
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
							<p><a href="#" ng-click="vm.getUserFb()">Ingresar con Facebook.</a></p>
						</div>
					</div>
					<?php
					// Cerramos si no estamos logueados
				}
				?>
			</div>
		</div>
	</div>
	<?php
}

// Verificamos el head de cursos
if(!empty($id_head) && $id_head == 'cursos'){
	?>
	<div id="presentacion">
		<div id="box">
			<div id="box-1">
				<h2>Codeando tiene los siguientes cursos para ti.</h2>
				<p>Proximamente nuevos cursos.</p>
			</div>
			<div id="box-2">
				<img src="/img/devices.png" alt="Cursos gratuitos">
			</div>
		</div>
	</div>
	<?php
}

// verificamos el head de contacto
if(!empty($id_head) && $id_head == 'contacto'){
	?>
	<div id="presentacion">
		<div id="box">
			<div id="box-1">
				<h2>Tienes alguna propuesta interesante?</h2>
				<p>Alguna duda, comentario o sugerencia?</p>
			</div>
			<div id="box-2">
				<img src="/img/contacto.png" alt="Cursos gratuitos">
			</div>
		</div>
	</div>
	<?php
}