<?php
/************************************************
Archivo que contiene el formulario de login

Proyecto: Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

session_start();

if(isset($_GET["logout"]) && $_GET["logout"] == 1){
	//Al utilizar el boton de logout de facebook, destruimos la session.
	session_destroy();
	header("Location: /");
}

// Verificamos que el usuario no este logueado
if(empty($_SESSION['logged_in'])) { $_SESSION['logged_in'] = false; }
if($_SESSION['logged_in'] == true){
	// Si esta logueado lo mandamos al area de cursos
	header("Location: /cursos/");
}

// Incluimos las clases necesarias para trabajar
require_once 'config.php';
require_once 'include/Db.php';
require_once 'include/Fnc.php';
require_once 'include/Template.php';
require_once 'include/page.php';

class Pageb extends Page
{
	public function display_header()
	{
		// Incluimos el header
		require_once 'include/inicio_header.php';
	}
	public function display_content()
	{
	?>
        <div id="login">
			<?php require_once 'include/html_login.php'; ?>
		</div>
	<?php
	}
	public function display_nav()
	{
		// Incluimos el menu de navegacion
		require_once 'include/inicio_menu.php';
	}
}

// Creamos el objeto pagina
$index = new PageB();

// Descripcion de la pagina
$descripcion = 'Sistema de login - codeando.org';

$index->set_site_name($site_name); // Asignamos el titulo de la pagina
$index->set_title($title_index.' | '.$site_name); // Asignamos un titulo a la pagina (title)
$index->set_description($descripcion); // Asignamos una descripcion a la pagina (opcional)
$index->set_page("inicio"); // ID de la pagina, sirve para resaltar en el menu en que seccion se encuentra (opcional)
$index->set_analytics($analytics); // Codigo de seguimiento de google analytics
$index->set_fb($appId, $return_url, $fbPermissions); // Opciones para iniciar Facebook Connect

$index->display(); // Mostramos la pagina