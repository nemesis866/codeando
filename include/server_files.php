<?php
/************************************************
Archivo servidor para los archivos

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

set_time_limit(0);

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

require_once '../config.php';
require_once 'Fnc.php';
require_once 'Db.php';

$fnc = new Fnc();
$db = new Db();

if(empty($_POST['type'])){ $type = '';} else { $type = addslashes($_POST['type']);}

// Router del server
switch($type){
	case 'delete': // OK
		delete($db,$fnc);
		break;
	case 'delete_dis': // OK
		delete_dis($db,$fnc);
		break;
	case 'file_delete': // OK
		file_delete($db,$fnc);
		break;
	case 'file_delete_edit': // OK
		file_delete_edit($db,$fnc);
		break;
	case 'file_delete_dis': // OK
		file_delete_dis($db,$fnc);
		break;
	case 'file_mostrar': // OK
		file_mostrar($db,$fnc);
		break;
	case 'file_temp': // OK
		file_temp($db,$fnc);
		break;
	case 'file_temp_dis': // OK
		file_temp_dis($db,$fnc);
		break;
}

// Eliminamos todos los archivos temporales
function delete($db,$fnc)
{
	$user = $_SESSION['id']; // ID user

	// Eliminamos los archivos temporales
	$delete = $db->mysqli_action("DELETE FROM files_temp WHERE user='$user'");

	// Regresamos informacion
	echo json_encode(array('status'=>'Se eliminaron todos los archivos'));
	exit();
}

// Eliminamos todos los archivos temporales de las discusiones
function delete_dis($db,$fnc)
{
	$user = $_SESSION['id']; // ID user

	// Eliminamos los archivos temporales
	$delete = $db->mysqli_action("DELETE FROM files_temp_dis WHERE user='$user'");

	// Regresamos informacion
	echo json_encode(array('status'=>'Se eliminaron todos los archivos'));
	exit();
}

// Eliminamos un archivo de la base de datos temporal
function file_delete($db,$fnc)
{
	$id = $fnc->secure_sql($_POST['id']); // ID file

	// Eliminamos el archivo
	$delete = $db->mysqli_action("DELETE FROM files_temp WHERE id_file='$id'");

	// Regresamos informacion
	echo json_encode(array('status'=>'El archivo se elimino con exito','id'=>$id));
	exit();
}
// Eliminamos un archivo de la base de datos temporal
function file_delete_edit($db,$fnc)
{
	$id = $fnc->secure_sql($_POST['id']); // ID file

	// Eliminamos el archivo
	$delete = $db->mysqli_action("DELETE FROM files WHERE id_file='$id'");

	// Regresamos informacion
	echo json_encode(array('status'=>'El archivo se elimino con exito','id'=>$id));
	exit();
}
// Eliminamos un archivo de la base de datos temporal
function file_delete_dis($db,$fnc)
{
	$id = $fnc->secure_sql($_POST['id']); // ID file

	// Eliminamos el archivo
	$delete = $db->mysqli_action("DELETE FROM files_temp_dis WHERE id_file='$id'");

	// Regresamos informacion
	echo json_encode(array('status'=>'El archivo se elimino con exito','id'=>$id));
	exit();
}

// Mostramos un archivo
function file_mostrar($db,$fnc)
{
	$id = $fnc->secure_sql($_POST['id']); // ID file
	$tipo = $fnc->secure_sql($_POST['tipo']); // Tipo file
	$datos = '';

	if($tipo == 'temp'){
		// Cargamos el archivo a mostrar
		$result = $db->mysqli_select("SELECT contenido,name,ext FROM files_temp WHERE id_file='$id' LIMIT 1");
		while($row = $result->fetch_assoc()){
			$contenido = $row['contenido'];
			$name = $row['name'];
			$ext = $row['ext'];
		}
		$result->close();

		$back = "<div class='w_back icon-left_back' onclick='javascript:back_files()'>Regresar</div>";

		$datos .= $back;
		$datos .= "<div id='f_content'>
			<h2 class='icon-$ext'>$name</h2>
			<div class='code'>".$fnc->mostrar_html($contenido)."</div>
		</div>";
		$datos .= $back;
	} else if($tipo == 'file'){
		// Cargamos el archivo a mostrar
		$result = $db->mysqli_select("SELECT contenido,name,ext FROM files WHERE id_file='$id' LIMIT 1");
		while($row = $result->fetch_assoc()){
			$contenido = $row['contenido'];
			$name = $row['name'];
			$ext = $row['ext'];
		}
		$result->close();

		$back = "<div class='w_back icon-left_back' onclick='javascript:back_files()'>Regresar</div>";

		$datos .= $back;
		$datos .= "<div id='f_content'>
			<h2 class='icon-$ext'>$name</h2>
			<div class='code'>".$fnc->mostrar_html($contenido)."</div>
		</div>";
		$datos .= $back;
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Archivo cargado','datos'=>$datos,'id'=>$id));
	exit();
}

// Guardamos el archivo en la base de datos temporal
function file_temp($db,$fnc)
{
	$ext = $fnc->secure_sql($_POST['ext']); // Extencion del archivo
	$name = $fnc->secure_sql($_POST['name']);	// Nombre del archivo
	$size = $fnc->secure_sql($_POST['size']);	// Tamaño del archivo
	$contenido = $_POST['contenido'];	// Contenido del archivo
	$control = $fnc->secure_sql($_POST['control']); // Total de archivos
	$user = $_SESSION['id']; // ID user
	$tipo = $fnc->secure_sql($_POST['tipo']); // Donde se publico el archivo

	// Obtenemos los valores reales habiendo pasado el filtro mod_security
	$contenido = $fnc->html_replace($contenido);

	// Consultamos que no exista el archivo en la base de datos
	$result = $db->mysqli_select("SELECT Count(id_file) FROM files_temp WHERE name='$name' AND control='$control' AND user='$user'");
	$count = $result->fetch_row();
	$result->close();

	// Verificamos que no exista el archivo en la base de datos
	if($count[0] == 0){
		// Guardamos en la base de datos
		$insert = $db->mysqli_action("INSERT INTO files_temp (name,size,ext,contenido,control,type,fecha,user) VALUES ('$name', '$size', '$ext', '$contenido', '$control', '$tipo', NOW(), '$user')");

		echo json_encode(array('status'=>'Archivo cargado con exito','id'=>$insert));
	} else {
		// Avizamos que el archivo ya existe en la base de datos
		echo json_encode(array('error'=>'El archivo esta duplicado.'));
	}

	exit();
}

// Guardamos el archivo en la base de datos temporal
function file_temp_dis($db,$fnc)
{
	$ext = $fnc->secure_sql($_POST['ext']); // Extencion del archivo
	$name = $fnc->secure_sql($_POST['name']);	// Nombre del archivo
	$size = $fnc->secure_sql($_POST['size']);	// Tamaño del archivo
	$contenido = $_POST['contenido'];	// Contenido del archivo
	$control = $fnc->secure_sql($_POST['control']); // Total de archivos
	$id = $fnc->secure_sql($_POST['id']); // ID discusion
	$user = $_SESSION['id']; // ID user
	$tipo = $fnc->secure_sql($_POST['tipo']); // Donde se publico el archivo

	// Obtenemos los valores reales habiendo pasado el filtro mod_security
	$contenido = $fnc->html_replace($contenido);

	// Consultamos que no exista el archivo en la base de datos
	$result = $db->mysqli_select("SELECT Count(id_file) FROM files WHERE name='$name' AND id_discucion='$id'");
	$count = $result->fetch_row();
	$result->close();

	// Verificamos que no exista el archivo en la base de datos
	if($count[0] == 0){
		// Guardamos en la base de datos
		$insert = $db->mysqli_action("INSERT INTO files (name,size,ext,contenido,type,fecha,user,id_discucion) VALUES ('$name', '$size', '$ext', '$contenido', '$tipo', NOW(), '$user', '$id')");

		echo json_encode(array('status'=>'Archivo cargado con exito','id'=>$insert,'id_dis'=>$id));
	} else {
		// Avizamos que el archivo ya existe en la base de datos
		echo json_encode(array('error'=>'El archivo esta duplicado.'));
	}

	exit();
}