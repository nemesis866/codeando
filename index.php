<?php
/************************************************
Archivo index de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

if(empty($_GET['logout'])){ $logout = '';} else { $logout = addslashes($_GET['logout']);}
if(isset($logout) && $logout == 1){
	// Al utilizar el boton de logout de facebook, destruimos la session.
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
		// Incluimos el contenido
		require_once 'include/inicio_index.php';
	}
	public function display_nav()
	{
		// Incluimos el menu de navegacion
		require_once 'include/inicio_menu.php';
	}
	public function display()
	{
		self::display_head();
		$this->display_nav();
		$this->display_header();
		$this->display_content();
		?></div><?php
		self::display_footer();
	}
}

// Creamos el objeto pagina
$index = new PageB();

// Descripcion de la pagina
$descripcion = 'Te gustaria aprender a programar?, Entonces vive la experiencia de aprendizaje en linea con la plataforma Codeando.org, tenemos cursos totalmente gratuitos de programacion en video y documentados, un sistema de discusiones para resolver tus dudas de inmediato.';

$index->set_site_name($site_name); // Asignamos el titulo de la pagina
$index->set_title($title_index.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$index->set_description($descripcion); // Asignamos una descripcion a la pagina (opcional)
$index->set_page("inicio"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$index->set_analytics($analytics); // Codigo de seguimiento de google analytics
$index->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$index->display(); // Mostramos la pagina