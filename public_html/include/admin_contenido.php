<?php
/********************************************************************
en este archivo concentramos el sistema de paginas del Admin

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

// Obtenemos las variables por parametro
if(empty($_GET['category'])){ $category = '';} else { $category = addslashes($_GET['category']);}

// Router del admin
switch ($category) {
	// curso
	case 'course':
		course();
		break;
	// Categorias
	case 'category':
		category();
		break;
	// Avisos de cursos
	case 'notices':
		notices();
		break;
	// Perfil de usuario
	case 'profile':
		profile();
		break;
	// Pagina principal
	default:
		principal();
		break;
}

// Muestra el contenido proncipal del panel
function principal()
{
	?>
	<h2 class="pprincipal">Avisos de la plataforma</h2>
	<p>* Codeando.org estrena Panel de administración, aun hay mucho que mejorar!!!</p>
	<?php
}
// Funcion que muestra la categoria cursos
function course()
{
	// Obtenemos los valores
	if(empty($_GET['action'])){ $action = '';} else { $action = addslashes($_GET['action']);}
	if(empty($_GET['sub'])){ $sub = '';} else { $sub = addslashes($_GET['sub']);}
	$nivel = $_SESSION['nivel'];

	// Definimos los titulos
	switch ($sub) {
		case 'edit':
			echo '<h2 class="pprincipal">Editar curso</h2>';
			break;
		case 'new':
			echo '<h2 class="pprincipal">Crear curso</h2>';
			break;
		case 'preview':
			echo '<h2 class="pprincipal">Informacion del curso</h2>';
			break;
		case 'review':
			echo '<h2 class="pprincipal">Cursos en revision</h2>';
			break;
		default:
			echo '<h2 class="pprincipal">Cursos</h2>';		
			break;
	}
	?>
	<!-- Menu de la seccion //-->
	<ul class="menu_articulos">
		<li><a href="/admin-co/?category=course">Cursos</a></li>
		<li><a href="/admin-co/?category=course&sub=new">Nuevo</a></li>
		<?php
		if($nivel == 10){
			?><li><a href="/admin-co/?category=course&sub=review">Revisión</a></li><?php
		}
		if($sub == 'preview' || $sub == 'edit'){
			if(empty($_GET['item'])){ $item = '';} else { $item = addslashes($_GET['item']);}
			if($item == 'review'){
				?><li><a href="/admin-co/?category=course&sub=review" class='icon-back'></a></li><?php
			} else {
				?><li><a href="/admin-co/?category=course" class='icon-back'></a></li><?php
			}
		}
		?>
	</ul>
	<?php
	// Router de la seccion
	switch ($sub) {
		case 'edit':
			require_once "include/admin_cursos_nuevo.php";
			break;
		case 'new':
			require_once "include/admin_cursos_nuevo.php";
			break;
		case 'preview':
			require_once "include/admin_cursos_preview.php";
			break;
		case 'review':
			require_once 'include/admin_cursos_review.php';
			break;
		default:
			require_once "include/admin_cursos.php";
			break;
	}
}
function category()
{
	// Obtenemos los valores
	if(empty($_GET['action'])){ $action = '';} else { $action = addslashes($_GET['action']);}
	if(empty($_GET['sub'])){ $sub = '';} else { $sub = addslashes($_GET['sub']);}

	// Definimos los titulos
	switch ($sub) {
		case 'new':
			echo '<h2 class="pprincipal">Crear categoria</h2>';
			break;
		case 'edit':
			echo '<h2 class="pprincipal">Editar categoria</h2>';
			break;
		case 'apply_for':
			echo '<h2 class="pprincipal">Solitar categorias</h2>';
			break;
		default:
			echo '<h2 class="pprincipal">Categorias</h2>';		
			break;
	}
	?>
	<!-- Menu de la seccion //-->
	<ul class="menu_articulos">
		<?php
		if($_SESSION['nivel'] == 10){
			?>
			<li><a href="/admin-co/?category=category">Categorias</a></li>
			<li><a href="/admin-co/?category=category&sub=new">Nuevo</a></li>
			<?php
			if($sub == 'edit'){
				?><li><a href="/admin-co/?category=category" class='icon-back'></a></li><?php
			}
		}
		?>
		<li><a href="/admin-co/?category=category&sub=apply_for">Solicitar</a></li>
	</ul>
	<?php
	// Router de la seccion
	switch ($sub) {
		case 'apply_for':
			require_once 'include/admin_categorias_apply_for.php';
			break;
		case 'edit':
			require_once "include/admin_categorias_nuevo.php";
			break;
		case 'new':
			require_once "include/admin_categorias_nuevo.php";
			break;
		default:
			require_once "include/admin_categorias.php";
			break;
	}
}
function notices()
{
	// Obtenemos los valores
	if(empty($_GET['action'])){ $action = '';} else { $action = addslashes($_GET['action']);}
	if(empty($_GET['sub'])){ $sub = '';} else { $sub = addslashes($_GET['sub']);}

	// Definimos los titulos
	switch ($sub) {
		default:
			echo '<h2 class="pprincipal">Avisos para cursos</h2>';		
			break;
	}
	
	// Router de la seccion
	switch ($sub) {
		default:
			require_once "include/admin_notices.php";
			break;
	}
}
function profile()
{
	// Obtenemos los valores
	if(empty($_GET['action'])){ $action = '';} else { $action = addslashes($_GET['action']);}
	if(empty($_GET['sub'])){ $sub = '';} else { $sub = addslashes($_GET['sub']);}

	// Definimos los titulos
	switch ($sub) {
		default:
			echo '<h2 class="pprincipal">Perfil de usuario</h2>';		
			break;
	}

	// Router de la seccion
	switch ($sub) {
		default:
			require_once "include/admin_profile.php";
			break;
	}
}