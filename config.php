<?php
/************************************************
Archivo de configuracion general

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compug@mail.com
Web: http://www.pauloandrade1.com
************************************************/

########## Opciones generales ################
$email = 'source.compu@gmail.com'; // Email del Administrador (Contacto en caso de algun problema)
$site_domain = $_SERVER['SERVER_NAME']; // Dominio del sitio web
$site_name = 'Codeando'; // Nombre del sitio web
$localhost = 'codeando.dev'; // Host virtual para pruebas
$login_show = true;  // Muestra el formulario de login en el header 'true = si, false = no'
$login_type = true;  // Tipo de login, false = normal, true = dinamico (javascript)
$title_index = 'Cursos online gratuitos'; // Titulo de la pagina principal
$title_cursos = 'Cursos online disponibles'; // Titulo de la pagina quienes somos
$title_service = 'Servicios'; // Titulo de la pagina servicios
$title_contact = 'Contactanos'; // Titulo de la pagina de contacto
$title_detalles = ''; // Titulos de la pagina de detalles
$analytics = ''; // Codigo de google analytics
$premium = false; // Activa zona premium, true = activado, false = desactivado

// Datos para conectar la base de datos
if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == $localhost){
	// En local
	$data_db = array(
		'server'=>'127.0.0.1:3306', // Servidor
		'user'=>'paulo', // Ususario
		'pass'=>'paulo866', // Password
		'db'=>'codeando', // Base de datos
		'email'=>$email // Email de contacto del admin
		);
} else {
	// En servidor
	$data_db = array(
		'server'=>'localhost', // Servidor
		'user'=>'progra11_paulo86', // Usuario
		'pass'=>'@,!CExNOF-dh', // Password
		'db'=>'progra11_codeando', // Base de datos
		'email'=>$email // Email de contacto del admin
		);
}

########## Configuracion envio de email ##########
$data_email = array(
	'host'=>'servidor2202.el.controladordns.com', // Servidor SMTP
	'user'=>'admin@programacionazteca.mx', // Usuario (correo electronico)
	'pass'=>'IXW#S@QV?JFN', // Password
	'email'=>$email, // Email de contacto
	'site_name'=>$site_name, // Nombre del sitio
	'site_address'=>$site_domain, // Direccion del sitio web
	'name'=>'Paulo Andrade', // Nombre del administrador
	);

########## Configuracion Facebook Connect #############
$appId = '346247455575774'; // Facebook App ID
$appSecret = '29e2744d38c77ed1b9b5ac51e746151d'; // Facebook App Secret
$return_url = 'http://codeando.org';  // Url principal del sitio (Root)
$fbPermissions = 'email,public_profile'; // Permisos, mas permisos: ht