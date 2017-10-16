<?php
/********************************************************************
Pagina principal del menu categorias

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

// Iniciamos la base de datos
$db = new Db();

$nivel = (empty($_SESSION['nivel'])) ? '' : $_SESSION['nivel'];

// Para ver opciones de categoria debe tener un nivel de 10
if($nivel == 10){
	// Consultamos si existen categorias registradas
	$result = $db->mysqli_select("SELECT nombre,id_categoria FROM categorias ORDER BY nombre");
	$count = $result->num_rows;

	// Verificamos si hay categorias registradas
	if($count == 0){
		// Si no hay mostramos mensaje
		?>
		<div class="center"><p>No tiene categorias en la plataforma</p></div>
		<?php
	} else {
		// Si hay categorias las mostramos
		?>
		<div class="table_index">
			<div class="category_name">Categoria</div>
			<div class="category_action">Estadisticas y acciones</div>
		</div>
		<?php
		// Obtenemos las categorias
		while($row = $result->fetch_assoc()){
			$category = $row['nombre'];
			$id_category = $row['id_categoria'];

			// Obtenemos el total de cursos de cada categoria
			$result1 = $db->mysqli_select("SELECT * FROM cursos WHERE categoria='$category'");
			$count_art = $result1->num_rows;
			$result1->close();
			?>
			<div id="cat_<?php echo $id_category; ?>" class="table_conten_index">
				<div class="category_name"><?php echo $category ?></div>
				<div class="category_action"><?php if($count_art == 0){?> <a href="/admin-co/?category=category&sub=edit&id=<?php echo $id_category; ?>">Editar</a> <a onclick="javascript:delete_category(<?php echo $id_category ;?>)" title="Eliminar Categoria">Eliminar</a> - <?php } ?> Contiene <?php echo $count_art; ?> cursos <?php if($count_art != 0){ ?>(No se puede editar y eliminar) <?php } ?></div>
			</div>
		<?php
		}
		$result->close();
	}
} else {
	// SI no tiene permisos mostramos mensaje
	?><p>No tiene permisos para ver esta sección</p><?php
}