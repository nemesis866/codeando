<?php
/************************************************
Archivo para mostrar los temas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
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
		// Incluimos el contenido
		require_once 'include/inicio_temas.php';
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
$db = new Db();
$fnc = new Fnc();

// Obtenemos id del tema
$id_tema = (empty($_GET['id_tema'])) ? '' : $_GET['id_tema'];

// Obtenemos informacion sobre el tema
$result = $db->mysqli_select("SELECT titulo,doc FROM temas WHERE id_tema='$id_tema'");
while($row = $result->fetch_assoc()){
	$titulo = $row['titulo'];
	$doc = (empty($row['doc'])) ? '' : $row['doc'];
}
$result->close();

// Creamos una descripcion para el tema
if(!empty($doc)){
	// Damos formato al contenido
	$doc = $fnc->mostrar_html($doc);
	$doc = strip_tags($doc); // Retira las etiquetas php y html
	$doc = substr($doc, 0, 150);
	$doc .= " ...";
	$doc = ucfirst($doc);
	$doc = $fnc->code($doc);
}

$index->set_site_name($site_name); // Asignamos el titulo de la pagina
$index->set_title($titulo.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$index->set_description($doc); // Asignamos una descripcion a la pagina (opcional)
$index->set_page("cursos"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$index->set_analytics($analytics); // Codigo de seguimiento de google analytics
$index->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$index->display(); // Mostramos la pagina