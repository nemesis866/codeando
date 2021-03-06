<?php
/************************************************
Archivo en el que mostramos los cursos de la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
************************************************/

session_start();

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
		// Incluimos el contenido de la pagina
		require_once 'include/inicio_cursos.php';
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
$cursos = new PageB();

// Descripcion de la pagina
$descripcion = 'Nuestros cursos estan en video, para que los puedas ver las veces que sean necesarias para que entiendas el concepto del tema que estas viendo, cada tema esta documentado y con sus codigos de ejemplo.';

$cursos->set_site_name($site_name); // Asignamos el titulo de la pagina
$cursos->set_title($title_cursos.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$cursos->set_description($descripcion); // Asignamos una descripcion a la pagina (opcional)
$cursos->set_page("cursos"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$cursos->set_analytics($analytics); // Codigo de seguimiento de google analytics
$cursos->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$cursos->display(); // Mostramos la pagina