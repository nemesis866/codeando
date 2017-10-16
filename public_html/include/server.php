<?php
/************************************************
Archivo servidor princiapl

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

set_time_limit(0);

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

// Importamos las clases
require_once '../config.php';
require_once '../phpmailer/PHPMailerAutoload.php';
require_once '../include/Secure.php';
require_once '../include/Db.php';
require_once '../include/Fnc.php';
require_once '../include/Template.php';

// Inicializamos los objetos
$db = new Db();
$fnc = new Fnc();
$mail = new PHPMailer;
$sec = new Secure();
$template = new Template();

// Evitamos ataques sql
$sec->secureGlobals();

if(empty($_POST['type'])){ $type = '';} else { $type = addslashes($_POST['type']);}

// Router del server
switch($type){
	case 'form_contacto':
		form_contacto($db, $fnc);
		break;
	case 'form_login':
		form_login($db, $fnc, $mail, $data_email);
		break;
	case 'form_recover':
		form_recover($db, $fnc, $mail, $data_email,$template);
		break;
	case 'form_register':
		form_register($db, $fnc, $mail, $data_email,$template);
		break;
	case 'suscribir':
		suscribir($db, $fnc);
		break;
}

// Procesamos formulario de contacto
function form_contacto($db, $fnc)
{
	$name = $_POST['name'];
	$email = $_POST['email'];
	$asunto = $_POST['asunto'];
	$contenido = $_POST['contenido'];

	if($asunto == 1){
		$asunto = 'Sugerencia';
	} else if($asunto == 2){
		$asunto = 'Bug / Error';
	} else if($asunto == 3){
		$asunto = 'Comentario';
	}

	$insert = $db->mysqli_select("INSERT INTO contacto (name,email,asunto,contenido,leido,fecha) VALUES ('$name','$email','$asunto','$contenido','NO',NOW())");

	echo json_encode(array('status'=>'El formulario proceso con exito'));
	exit();
}

// Procesamos el formulario de login
function form_login($db, $fnc, $mail, $data_email)
{
	$pass = md5($_POST['pass']);
	$user = strtolower($_POST['user']);

	// Verificamos que el usuario exista
	$result = $db->mysqli_select("SELECT id,fbid,email,nombre,username,nivel_user,avatar FROM usuarios WHERE username='$user' AND password='$pass'");
	$count = $result->num_rows;

	if($count > 0){
		// Si existe
		while($row = $result->fetch_assoc()){
			$email = (empty($row['email'])) ? '' : $row['email'];
			$_SESSION['logged_in'] = true;
			$_SESSION['logged_fb'] = false;
			$_SESSION['email'] = (empty($row['email'])) ? '' : $row['email'];
			$_SESSION['nombre'] = (empty($row['nombre'])) ? '' : $row['nombre'];
			$_SESSION['user_name'] = (empty($row['username'])) ? '' : $row['username'];
			$_SESSION['nivel'] = (empty($row['nivel_user'])) ? '' : $row['nivel_user'];
			$_SESSION['id'] = (empty($row['id'])) ? '' : $row['id'];
			$_SESSION['user_id'] = (empty($row['fbid'])) ? '' : $row['fbid'];
			$_SESSION['avatar'] = (empty($row['avatar'])) ? '' : $row['avatar'];
			$_SESSION['gender'] = 'male';
		}
		$result->close();

		// Actualizamos la fecha de ultimo acceso
		$update = $db->mysqli_action("UPDATE usuarios SET ultimo_acceso=NOW() WHERE email='$email'");

		// Generamos texto a mostrar
		$result = "Bienvenid@ ".ucwords($_SESSION['nombre']).", Por favor seleccione un curso para ingresar a la plataforma.";

		echo json_encode(array('status'=>'Inicio de sesion exitoso','result'=>$result));
	} else {
		// No existe
		echo json_encode(array('error'=>'Usuario y/o contraseña invalidos, intente nuevamente'));
	}

	exit();
}

// Procesamos el formulario de recover
function form_recover($db, $fnc, $mail, $data_email,$template)
{
	$email = $_POST['email'];

	// Verificamos que exista en la base de datos
	$result = $db->mysqli_select("SELECT nombre,username FROM usuarios WHERE email='$email' AND registro='YES' LIMIT 1");
	$count = $result->num_rows;

	if($count > 0){
		// Si existe
		while($row = $result->fetch_assoc()){
			$nombre = (empty($row['nombre'])) ? '' : $row['nombre'];
			$username = (empty($row['username'])) ? '' : $row['username'];
		}
		$result->close();

		// Generamos una password aleatorio
		$str = 'abcdefghkmpqrstwxyz1234567890';
		$pass = '';
		for($i = 0; $i < 10; $i++){
			$pass .= substr($str,rand(0,36),1);
		}
		$md5 = md5($pass);
		// md5 de admin 21232f297a57a5a743894a0e4a801fc3

		// Actualizamos el password en la base de datos
		$insert = $db->mysqli_action("UPDATE usuarios SET password='$md5' WHERE email='$email'");

		// Generamos respuesta
		$respuesta = "<p>$nombre: Recibe este mensaje por que solicito la recuperacion de su password del sitio ".$data_email['site_name']."</p>
					<p>Datos de ingreso:</p>
					<p>Username: $username<br>
					Password: $pass</p>
					<p><strong>No olvides</strong> ingresar al sistema de administracion y cambiar el password temporal.</p>";

		$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

		// Configuracion del servidor SMTP para el envio de email
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP();
		$mail->Host = $data_email['host'];  // Indico el servidor para SMTP
		$mail->SMTPAuth = true;  // Debo de hacer autenticación SMTP
		$mail->Username = $data_email['user'];  // Indico un usuario
		$mail->Password = $data_email['pass'];  // clave de un usuario
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;  // Puerto por defecto del servidor SMTP

		// Datos del remitente
		$mail->From = $data_email['email']; // Email remitente
		$mail->FromName = $data_email['site_name']; // Nombre remitente

		// Indicamos el destinatario
		$mail->AddAddress($email, $nombre); // Email y nombre del destinatario
		// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
		$mail->Subject = 'Recuperacion de password - '.$data_email['site_name'];
		$mail->MsgHTML($respuesta);

		// Procesamos el envio de email
		if(!$mail->Send()) {
			// Error
			echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente'));
		} else {
			// Exito

			echo json_encode(array('status'=>'Su contraseña se envio a su email'));
		}
	} else {
		// No existe
		echo json_encode(array('error'=>'El email no existe en la base de datos'));
	}

	exit();
}

// Procesamos el formulario de registro
function form_register($db, $fnc, $mail, $data_email,$template)
{
	$username = strtolower($_POST['user']);
	$pass = $_POST['pass'];
	$email = $_POST['email'];
	$md5 = md5($pass);

	// Verificamos que no exista en la base de datos
	$result = $db->mysqli_select("SELECT id FROM usuarios WHERE email='$email' AND registro='YES' LIMIT 1");
	$count = $result->num_rows;
	$result->close();

	if($count == 0){
		// No existe

		// Verificamos que el username no este ocupado
		$result1 = $db->mysqli_select("SELECT id FROM usuarios WHERE username='$username' LIMIT 1");
		$count1 = $result1->num_rows;
		$result1->close();

		if($count1 == 0){
			// Verificamos que el registro no exista en la base de datos temporal
			$result2 = $db->mysqli_select("SELECT id FROM usuarios_temp WHERE email='$email' LIMIT 1");
			$count2 = $result2->num_rows;
			$result2->close();

			if($count2 == 0){
				// Insertamos el registro en la base de datos temporal
				$insert = $db->mysqli_action("INSERT INTO usuarios_temp (email,username,password) VALUES ('$email','$username','$md5')");
			} else {
				// Actualizamos el registro en la base de datos
				$update = $db->mysqli_action("UPDATE usuarios_temp SET username='$username',password='$md5' WHERE email='$email'");
			}

			// Generamos respuesta
			$respuesta = "<p>$username: Recibe este mensaje por que solicito registrarse en el sitio ".$data_email['site_name']."</p>
						<p>Sus datos de ingreso son los siguientes:</p>
						<p>Username: $username<br>
						Password: $pass</p>
						<div style='background-color: #FFFFCC;border: 1px solid #CCC;border-radius: 3px;margin-bottom: 10px;min-height: 50px;padding: 10px;'>
							<img src='https://lh3.googleusercontent.com/-CvxSSHW4l9w/Ve45wpdGEFI/AAAAAAAACK8/qatJNgjqTFY/s128-Ic42/alert.png' style='float:left;width:50px;'>
							<p><strong>Atencion</strong> para finalizar su registro de clic en el siguiente enlace:</p>
						</div>
						<p><a href='http://codeando.org/register.php?email=$email&temp=$md5'>Finalizar su registro</a></p>";

			$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

			// Configuracion del servidor SMTP para el envio de email
			$mail->CharSet = "UTF-8";
			$mail->IsSMTP();
			$mail->Host = $data_email['host'];  // Indico el servidor para SMTP
			$mail->SMTPAuth = true;  // Debo de hacer autenticación SMTP
			$mail->Username = $data_email['user'];  // Indico un usuario
			$mail->Password = $data_email['pass'];  // clave de un usuario
			$mail->SMTPSecure = 'ssl';
			$mail->Port = 465;  // Puerto por defecto del servidor SMTP

			// Datos del remitente
			$mail->From = $data_email['user']; // Email remitente
			$mail->FromName = $data_email['site_name']; // Nombre remitente

			// Indicamos el destinatario
			$mail->AddAddress($email, $username); // Email y nombre del destinatario
			// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
			$mail->Subject = 'Autorizacion de registro - '.$data_email['site_name'];
			$mail->MsgHTML($respuesta);

			// Procesamos el envio de email
			if(!$mail->Send()) {
				// Error
				echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente'));
			} else {
				// Exito

				echo json_encode(array('status'=>'Registro exitoso'));
			}
		} else {
			// Si existe
			echo json_encode(array('error'=>'El username ya esta registrado en la base de datos'));
		}
	} else {
		// Si existe
		echo json_encode(array('error'=>'El email ya esta registrado en la base de datos'));
	}

	exit();
}

// Suscribir al curso
function suscribir($db, $fnc)
{
	$id = $_POST['id'];
	$user = $_SESSION['id'];

	// Agregamos al usuario a la suscrpcion del curso
	$insert = $db->mysqli_action("INSERT INTO suscripcion (id_curso,user,fecha) VALUES ('$id','$user',NOW())");

	// Obtenemos el total de suscriptores del curso
	$result = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE id_curso='$id'");
	$count = $result->fetch_row();
	$result->close();

	// Asignamosel contador a una variable provicional
	$contador = $count[0];

	echo json_encode(array('status'=>'Se suscribio al curso con exito!','id'=>$id,'count'=>$contador));
	exit();
}