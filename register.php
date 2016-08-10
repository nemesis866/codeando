<?php
/************************************************
Archivo para autorizar el registro

Proyecto: Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

session_start();

set_time_limit(0);

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

// Importamos las clases
require_once 'config.php';
require_once 'include/Db.php';

// Obtenemos los parametros
if(empty($_GET['email'])){ $email = '';} else { $email = addslashes($_GET['email']);}

$db = new Db();

// Verificamos si se recibieron parametros
if(empty($email)){
	echo 'SUCCESSFUL REGISTRACION!!!';
} else {
	// Verificamos que exista el email en la base de datos temporal
	$result = $db->mysqli_select("SELECT username,password FROM usuarios_temp WHERE email='$email' LIMIT 1");
	$count = $result->num_rows;

	if($count != 0){
		// Obtenemos los datos del registro
		while($row = $result->fetch_assoc()){
			$username = $row['username'];
			$password = $row['password'];
		}
		$result->close();

		// Url para el avatar por defecto
		$avatar = 'avatar.PNG';

		// Verificamos si el email ya existe en la base de datos original
		$result1 = $db->mysqli_select("SELECT Count('id') FROM usuarios WHERE email='$email'");
		$count1 = $result1->fetch_row();
		$result1->close();

		if($count1[0] == 0){
			// Si no existe
			// Almacenamos el registro en la base de datos original
			$insert = $db->mysqli_action("INSERT INTO usuarios (email,nombre,username,password,nivel_user,fecha,ultimo_acceso,registro,avatar) VALUES ('$email','$username','$username','$password',1,NOW(),NOW(),'YES','$avatar')");
		} else {
			// Si existe
			// Actualizamos el registro en la base de datos original
			$update = $db->mysqli_action("UPDATE usuarios SET password='$password',username='$username',ultimo_acceso=NOW(),registro='YES',avatar='$avatar' WHERE email='$email'");
		}

		// Eliminamos el registro de la base de datos temporal
		$delete = $db->mysqli_action("DELETE FROM usuarios_temp WHERE email='$email'");

		// Redireccionamos
		header("Location: /register.php");
	} else {
		echo 'ERROR: Email not found!!!';
	}
}