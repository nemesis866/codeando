<?php
/************************************************
Mostramos el contenido premium de la plataforma

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
		require_once 'include/inicio_premium.php';
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
$premium = new PageB();

// Descripcion de la pagina
$descripcion = 'Codeando.org cuenta con esta sección de contenido exclusivo para ti, encontraras tcursos de programación, tutorias, ayuda y mucho mas';

$premium->set_site_name($site_name); // Asignamos el titulo de la pagina
$premium->set_title($title_index.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$premium->set_description($descripcion); // Asignamos una descripcion a la pagina (opcional)
$premium->set_page("premium"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$premium->set_analytics($analytics); // Codigo de seguimiento de google analytics
$premium->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$premium->display(); // Mostramos la pagina