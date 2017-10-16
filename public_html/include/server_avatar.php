<?php
/************************************************
Servidor encargado de subir imagenes (avatar)

Proyecto:Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

session_start();

set_time_limit(0);

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

// Importamos las clases
require_once '../config.php';
require_once 'Fnc.php';
require_once 'Db.php';

$fnc = new Fnc();
$db = new Db();

// Verificamos que se trate de una peticion ajax
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

	$id = $_SESSION['id'];
	$destino = '../avatar/'; // Carpeta donde se guardara 

	// Obtenemos el id del usuario
	$result = $db->mysqli_select("SELECT id FROM usuarios WHERE email='$email' LIMIT 1");
	while($row = $result->fetch_assoc()){
		$id = $row['id'];
	}
	$result->close();

	// Comprobamos si existe el directorio para subir las imagenes
	if(!is_dir($destino)){
		mkdir($destino, 0777);
	}

	// Separamos el tipo de la imagen
	$sep = explode('image/', $_FILES['file']['type']);

	// Optenemos el tipo de imagen
	$type = $sep[1]; 

	// Armamos el nombre de la imagen
	$avatar = $id.'.'.$type;

	// Actualizamos el avatar en la base de datos
	$update = $db->mysqli_action("UPDATE usuarios SET avatar='$avatar' WHERE id='$id'");

	// Subimos la imagen
	if(move_uploaded_file ($_FILES[ 'file' ][ 'tmp_name' ], $destino.''.$avatar)){
		// Actualizamos la ruta de la imagen
		$_SESSION['avatar'] = $avatar;

		// Regresamos la ruta de la imagen
		echo $avatar;
		exit();
	}
} else {
	throw new Exception("Error Processing Request", 1);	
}