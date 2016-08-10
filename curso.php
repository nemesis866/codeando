<?php
/************************************************
Archivo en el que mostramos los detalles de un
curso en concreto

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

// Obtenemos variables por url
if(empty($_GET['id_curso'])){ $id_curso = '';} else { $id_curso = addslashes($_GET['id_curso']);}

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
		require_once 'include/inicio_curso.php';
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
$db = new Db();

// Obtenemos la descripcion breve del curso y el titulo
$result = $db->mysqli_select("SELECT titulo,subtitulo FROM cursos WHERE id_curso='$id_curso' LIMIT 1");
while($row = $result->fetch_assoc()){
	// Descripcion de la pagina
	$descripcion = $row['subtitulo'];
	$title_detalles = $row['titulo'];
}
$result->close();

$cursos->set_site_name($site_name); // Asignamos el titulo de la pagina
$cursos->set_title($title_detalles.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$cursos->set_description($descripcion); // Asignamos una descripcion a la pagina (opcional)
$cursos->set_page("cursos"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$cursos->set_analytics($analytics); // Codigo de seguimiento de google analytics
$cursos->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$cursos->display(); // Mostramos la pagina