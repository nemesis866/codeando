<?php
/********************************************************************
Pagina de registro de categorias

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

// obtenemos valores
if(empty($_GET['id'])){ $id_category = '';} else { $id_category = $_GET['id'];}

// Iniciamos la base de datos
$db = new Db();

$nivel = (empty($_SESSION['nivel'])) ? '' : $_SESSION['nivel'];
$nombre = '';

// Para tener permisos de edicion debe tener un nivel de 10
if($nivel == 10){
	?>

	<div class="cargando"></div>
	<?php

	if($sub == 'edit'){
		// Si esta en modo edicion obtenemos el nombre de la categoria por medio del ID
		$result = $db->mysqli_select("SELECT nombre FROM categorias WHERE id_categoria='$id_category'");
		while($row = $result->fetch_assoc()){
			$nombre = $row['nombre'];
		}
		$result->close();
		?>
		<form id="form_categoria_edit">
			<input type="hidden" id="id" value="<?php echo $id_category; ?>" />
		<?php
	} else {
		?>
		<form id="form_categoria">
		<?php
	}
	?>
		<label>Nombre de la categoria:</label>
		<p><input type="text" id="name" class="input" maxlength="20" value="<?php echo $nombre; ?>" placeholder="Escriba el nombre de la categoria" required>
			<span id="count_name" class="count">20</span></p>
		<?php
		if($sub == 'edit'){
			?><p><input type="submit" class="submit" value="Editar categoria"></p><?php
		} else {
			?><p><input type="submit" class="submit" value="Crear categoria"></p><?php
		}
		?>
	</form>
	<?php
} else {
	// Si no tiene permisos mostramos mensaje
	?><p>No tiene permisos para ver esta sección</p><?php
}