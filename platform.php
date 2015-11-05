<?php
/************************************************
Archivo principal de la plataforma, gestiona la
visualizacion de un curso

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

// Incluimos las clases necesarias para trabajar
require_once 'config.php';
require_once 'include/Fnc.php';
require_once 'include/Db.php';
require_once 'include/Page.php';
require_once 'include/Social.php';

// Para crear diferentes paginas en el sitio es necesario que el contenido de la funcion display_content sea independiente
class PageB extends Page
{
	public function display_content()
	{
		// Inicializamos los objetos
		$social = new Social();
		$fnc = new Fnc();
		?>

		<div id="wrapper_1"></div>
		<div id="wrapper_2"></div>
		<div id="wrapper_3">
			{{ counterStats }}
		</div>
		<div id="wrapper_4"></div>
		<?php
	}
	public function display()
	{
		self::display_head();
		$this->display_header();
		?>
		<section id="wrapper">
			<div id="content" ng-controller="initController"><?php
				$this->display_content();
			?></div><?php
			self::display_footer();
	}
}

// Creamos el objeto pagina
$index = new PageB($email);

$index->set_site_name($site_name); // Asignamos el titulo de la pagina
$index->set_title($title_index.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$index->set_description('Descripcion de la pagina principal'); // Asignamos una descripcion a la pagina (opcional)
$index->set_page("plataforma"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$index->set_analytics($analytics); // Codigo de seguimiento de google analytics
$index->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$index->display(); // Mostramos la pagina