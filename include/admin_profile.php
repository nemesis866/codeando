<?php
/************************************************
Archivo para guardar la configuracion del perfil

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compugmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Iniciamos la base de datos
$db = new Db();
$user = $_SESSION['id'];

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

// Obtenemos los detalles del usuario
$result = $db->mysqli_select("SELECT avatar,google,twitter,bio FROM usuarios WHERE id='$user'");
while($row = $result->fetch_assoc()){
	$acerca = (empty($row['bio'])) ? '' : $row['bio'];
	$google = (empty($row['google'])) ? '' : $row['google'];
	$twitter = (empty($row['twitter'])) ? '' : $row['twitter'];
	$avatar = (empty($row['avatar'])) ? '' : $row['avatar'];
}
$result->close();

// Obtenemos la longitud de las variables
	$count_acerca = 500 - strlen($acerca);
	$count_google = 30 - strlen($google);
	$count_twitter = 30 - strlen($twitter);
?>

<h3>Opciones Generales</h3>
<div class="cargando cargando_perfil"></div>
<form id="form_perfil">
	<p><label>Acerca de usted:</label><br>
		<textarea id="acerca" class="input" maxlength="500" placeholder="Ingrese una descripcion breve sobre usted"><?php echo $acerca; ?></textarea>
		<span id="count_acerca" class="count"><?php echo $count_acerca; ?></span></p>
	<p><label>ID de google plus:</label><br>
		<input type="text" id="google" class="input" maxlength="30" placeholder="Ingrese su ID de Google+" value="<?php echo $google; ?>">
		<span id="count_google" class="count"><?php echo $count_google; ?></span></p>
	<p><label>Usuario de twitter:</label><br>
		<input type="text" id="twitter" class="input" maxlength="30" placeholder="@ejemplo" value="<?php echo $twitter; ?>">
		<span id="count_twitter" class="count"><?php echo $count_twitter; ?></span></p>
	<p><input type="submit" class="submit" value="Guardar perfil"></p>
</form>

<?php
// Mostramos el formulario de avatar si no iniciamos con facebook
if(!$_SESSION['logged_fb']){
	?>
	<h3 class="subtitulo">Cambiar contraseña</h3>

	<form id="form_password">
		<p><label>Ingrese su password actual:</label><br>
			<input type="password" id="perfil_pass" class="input" placeholder="Password actual" required></p>
		<p><label>Ingrese su nuevo password:</label><br>
			<input type="password" id="perfil_pass1" class="input" placeholder="Password nuevo" required></p>
		<p><label>Ingrese nuevamente su password nuevo:</label><br>
			<input type="password" id="perfil_pass2" class="input" placeholder="Ingrese nuevamente password nuevo" required></p>
		<p><input type="submit" id="submit_password" class="submit" value="Cambiar password"></p>
		<div class="cargando cargando_password"></div>
		<div id="password_msg"></div>
	</form>
	<?php
}

// Mostramos el formulario de avatar si no iniciamos con facebook
if(!$_SESSION['logged_fb']){
	?>
	<h3 class="subtitulo">Avatar</h3>

	<div id="show_avatar">
		<img src="/avatar/<?php echo $_SESSION['avatar']; ?>" id="avatar_img" alt="Avatar" title="Avatar">
		<p><span id="avatar_msg">Avatar Actual</span></p>
	</div>
	<form id="form_avatar" enctype="multipart/form-data">
		<input type="hidden" id="avatar_email" value="<?php echo $_SESSION['email']; ?>">
		<p><label>Seleccione la imagen a subir: (Tamaño maximo 10 Kb)</label><br>
			<input type="file" id="avatar_avatar" name="file" required></p>
		<p><input type="submit" id="submit_avatar" class="submit" value="Cambiar avatar"></p>
		<div class="cargando cargando_avatar"></div>
	</form>
	<?php
}