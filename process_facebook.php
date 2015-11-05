<?php 
/************************************************
Archivo para cargar los datos de cuenta de los
usuarios (Facebook Connect)

Proyecto: Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

session_start();

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

require_once 'config.php';
require_once 'include/Db.php';

$db = new Db();

// Obtenemos las variables
$me['first_name'] = $_POST['first_name'];
$me['last_name'] = $_POST['last_name'];
$me['email'] = $_POST['email'];
$me['gender'] = $_POST['gender'];
$me['id'] = $_POST['uid'];

// Detalles del usuario
$fullname = $me['first_name'].' '.$me['last_name']; // Nombre completo
$email = $me['email']; // Email
$nivel = 1; // Asignamos un nivel 1 para usuarios nuevos
$uid = $me['id'];
$gender = $me['gender'];

// Consultamos si el usuario se encuentra en la base de datos
$result = $db->mysqli_select("SELECT Count(id) FROM usuarios WHERE email='$email'");	
$UserCount = $result->fetch_row();
$result->close(); 
	
// Verificamos si el usuario se encuentra en la base de datos
if($UserCount[0] > 0){	
	// Si se encuentra obtenemos el nombre del usuario de la base de datos
	$res = $db->mysqli_select("SELECT nombre,nivel_user,id,fbid FROM usuarios WHERE email='$email' LIMIT 1");
	while($row = $res->fetch_assoc()){
		$id = $row['id'];
		$name = $row['nombre'];
		$nivel = $row['nivel_user'];
		$fbid = (empty($row['fbid'])) ? '' : $row['fbid'];
	}
	$res->close();

	// Verificamos si cuenta con el registro de fb
	if(empty($fbid) || $fbid == 0){
		// Si no cuenta lo registramos
		// Actualizamos los datos del usuario en la base de datos
		$update = $db->mysqli_action("UPDATE usuarios SET fbid='$uid', nombre='$fullname', ultimo_acceso=NOW() WHERE email='$email'");
	} else {
		// Si cuenta solo actualizamos algunos datos
		// Verificamos si el nombre es igual a nombre completo
		if($name == $fullname){
			// Si lo es actualizamos el ultimo acceso del usuario
			$update = $db->mysqli_action("UPDATE usuarios SET ultimo_acceso=NOW() WHERE email='$email'");
		} else {
			// Si el nombre es diferente lo actualizamos y tambien el ultimo acceso
			$update = $db->mysqli_action("UPDATE usuarios SET nombre='$fullname',ultimo_acceso=NOW() WHERE email='$email'");
		}
	}

	// Mandamos los datos a la funcion de asignacion de sesion
	login_user(true,$me['first_name'].' '.$me['last_name'],$me['gender'],$me['id'],$me['email'],$nivel, $id);
} else {
	// Insertamos los datos del usuario en la base de datos
	$insert = $db->mysqli_action("INSERT INTO usuarios (fbid, nombre, email, nivel_user, fecha, ultimo_acceso) VALUES ('$uid', '$fullname','$email','$nivel',NOW(),NOW())");

	// Obtenemos el id asignado al usuario
	$id = $insert;
	
	// Mandamos los datos a la funcion de asignacio de sesion
	login_user(true,$me['first_name'].' '.$me['last_name'],$me['gender'],$me['id'],$me['email'],$nivel, $id);
}

function login_user($loggedin,$user_name,$gender,$user_id,$email,$nivel, $id)
{
	// Insertamos los datos obtenidos en una sesion para trabajar con ellos

	$_SESSION['logged_in'] = $loggedin;
	$_SESSION['logged_fb'] = true;
	$_SESSION['user_name'] = $user_name;
	$_SESSION['nombre'] = $user_name;
	$_SESSION['gender'] = $gender;
	$_SESSION['user_id'] = $user_id;
	$_SESSION['email'] = $email;
	$_SESSION['nivel'] = $nivel;
	$_SESSION['id'] = $id;
}

// Segun el genero mostramos el texto
$text = '';
if($_SESSION['gender'] == 'male'){
	$text = 'Bienvenido';
} else {
	$text = 'Bienvenida';
}

$result = $text." ".$_SESSION['nombre'].", Por favor seleccione un curso para ingresar a la plataforma.";

// Despues de todo el proceso regresamos el nombre completo del usuario
echo json_encode(array('status'=>'Inicio de sesion exitoso','result'=>$result,"id_user"=>$id));
exit();