<?php
/************************************************
Servidor encargado de subir imagenes para los
cursos

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
require_once '../config.php';
require_once 'Fnc.php';
require_once 'Db.php';

$fnc = new Fnc();
$db = new Db();

// Verificamos que se trate de una peticion ajax
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

	$destino = '../img_curso/'; // Carpeta donde se guardara 

	// Obtenemos el id del curso
	$id_curso = $fnc->secure_sql($_POST['id_curso']);

	// Comprobamos si existe el directorio para subir las imagenes
	if(!is_dir($destino)){
		mkdir($destino, 0777);
	}

	// Separamos el tipo de la imagen
	$sep = explode('image/', $_FILES['file']['type']);

	// Optenemos el tipo de imagen
	$type = $sep[1]; 

	// Armamos el nombre de la imagen
	$img = $id_curso.'.'.$type;

	// Actualizamos el logo en la base de datos
	$update = $db->mysqli_action("UPDATE cursos SET img='$img' WHERE id_curso='$id_curso'");

	// Subimos la imagen
	if(move_uploaded_file ($_FILES[ 'file' ][ 'tmp_name' ], $destino.''.$img)){
		// Regresamos la ruta de la imagen
		echo $img;
		exit();
	}
} else {
	throw new Exception("Error Processing Request", 1);	
}