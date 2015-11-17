<?php
/************************************************
Archivo para mostrar resultados de busqueda

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

if(empty($_GET['logout'])){ $logout = '';} else { $logout = addslashes($_GET['logout']);}
if(isset($logout) && $logout == 1){
	//Al utilizar el boton de logout de facebook, destruimos la session.
	session_destroy();
	header("Location: /");
}

// Incluimos las clases necesarias para trabajar
require_once 'config.php';
require_once 'include/Db.php';
require_once 'include/Page.php';
require_once 'include/Social.php';
require_once 'include/Template.php';

// Para crear diferentes paginas en el sitio es necesario que el contenido de la funcion display_content sea independiente
class PageB extends Page
{
	public function display_header()
	{
		// Incluimos el header
		require_once 'include/inicio_header.php';
	}
	public function display_content()
	{
		// Incluimos contenido de la pagina
		require_once 'include/inicio_buscador.php';
	}
	public function display_nav()
	{
		// Incluimos el menu de navegacion
		require_once 'include/inicio_menu.php';
	}
}

// Creamos el objeto pagina
$contacto = new PageB();

// Descripcion de la pagina
$descripcion = 'Buscador de codeando.org';

$contacto->set_site_name($site_name); // Asignamos el titulo de la pagina
$contacto->set_title($title_contact.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$contacto->set_description($descripcion); // Asignamos una descripcion a la pagina (opcional)
$contacto->set_page(""); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$contacto->set_analytics($analytics); // Codigo de seguimiento de google analytics
$contacto->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$contacto->display(); // Mostramos la pagina