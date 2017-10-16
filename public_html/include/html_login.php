<?php
/************************************************
Pagina de login del sitio

Proyecto: Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

// Añadimos el archivo de configuracion
include $_SERVER['DOCUMENT_ROOT'].'/config.php';

// Inicializamos los objetos
$db = new Db();
$fnc = new Fnc();
?>

<div id="form">
	<form id="form_login">
		<div class="logo">
			<img src="/img/logo_opt.png" alt="avatar">
		</div>
		<p><label>Member Login</label></p>
		<p><input type="text" id="user" placeholder="UserName" required></p>
		<p><input type="password" id="pass" placeholder="Password" required></p>
		<p><input type="submit" id="submit_login" value="LOGIN"></p>
		<div class="cargando cargando_login"></div>
	</form>
	<div class="login_option">
		<p><span onclick="javascript:login_register();">Registrarse</span> - 
			<span onclick="javascript:login_recover();">Olvide mi contraseña</span></p>
	</div>
	<?php
	// Mostramos el login de facebook solo si esta activada la opcion
	if(!empty($appId)){
		?>
		<div id="login_redes">
			<p>- OR -</p>
			<button id="button_fb" onclick="javascript:checkLoginState();">FACEBOOK</button>
			<div class="cargando cargando_fb"></div>
		</div>
		<?php
	}
	?>
</div>

<div id="register">
	<form id="form_register">
		<div class="logo">
			<img src="/img/logo_opt.png" alt="avatar">
		</div>
		<p><label>Ingrese Username:</label><br>
			<input type="text" id="register_username" placeholder="Ingrese un username valido" required></p>
		<p><label>Ingrese Contraseña:</label><br>
			<input type="password" id="register_pass1" placeholder="Ingrese su contraseña" required></p>
		<p><label>Ingrese nuevamente su contraseña</label><br>
			<input type="password" id="register_pass2" placeholder="Ingrese nuevamente su contraseña" required></p>
		<p><label>Ingrese email:</label><br>
			<input type="text" id="register_email" placeholder="Ingrese su email" required></p>
		<p><input type="submit" id="submit_register" value="REGISTRAR"></p>
		<div class="cargando cargando_register"></div>
		<div id="register_msg"></div>
	</form>
	<div id="register_cancel" class="login_option">
		<p><span onclick="javascript:login_register_cancel();">Cancelar</span></p>
	</div>
</div>

<div id="recover">
	<form id="form_recover">
		<div class="logo">
			<img src="/img/logo_opt.png" alt="avatar">
		</div>
		<p><label>Ingrese su email:</label><br>
			<input type="text" id="recover_email" placeholder="Ingrese su email" required></p>
		<p><input type="submit" id="submit_recover" value="RECUPERAR"></p>
		<div class="cargando cargando_recover"></div>
		<div id="recover_msg"></div>
	</form>
	<div id="recover_cancel" class="login_option">
		<p><span onclick="javascript:login_recover_cancel();">Cancelar</span></p>
	</div>
</div>