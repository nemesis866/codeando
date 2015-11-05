<?php
/************************************************
Gestion de cursos en la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Verificamos si el usuario tiene cursos en la plataforma
$user = $_SESSION['id'];

// Iniciamos la base de datos
$db = new Db();

// Consultamos si existen cursos registrados
$result = $db->mysqli_select("SELECT Count(id_curso) FROM cursos WHERE autor='$user'");
$count = $result->fetch_row();
$result->close();

// Verificamos si el usuario tiene cursos en la plataforma
if($count[0] == 0){
	// Si no tiene mostramos mensaje
	?><p>No tiene cursos en la plataforma</p><?php
} else {
	// Si tiene cursos

	// Incluimos la paginacion
	require_once 'include/admin_paginacion_cursos.php';
	?>
	<div class="paginacion">
		<p><?php echo $_pagi_info; ?></p>
		<p><?php echo $_pagi_navegacion; ?></p>
	</div>

	<div class="table_index">
		<div class="table_index_nombre">
			Titulo del curso
		</div>
		<div class="table_index_accion">
			Acciones
		</div>
	</div>

	<?php
	// Obtenemos los cursos del usuario
	while($row = $_pagi_result->fetch_assoc()){
		$id_curso = $row['id_curso'];
		$categoria = $row['categoria'];
		$titulo = $row['titulo'];
		$publicado = $row['public'];
		$url = $row['url'];
		$revicion = $row['revicion'];
		$instruccion = (empty($row['instruccion'])) ? '' : $row['instruccion'];

		// Creamos la url para ver el curso
		$url = "/$categoria/$url/$id_curso/";

		// Mostramos mensaje segun el status del curso
		if($publicado == 'NO'){
			$public = 'OffLine';
		} else {
			$public = 'OnLine';
		}
		?>

		<div id="info_<?php echo $id_curso; ?>" class="table_conten_index" style="display:none">
		</div>
		<div id="curso_<?php echo $id_curso ;?>" class="table_conten_index">
			<div class="table_conten_nombre">
				<?php echo $titulo.' ('.$public.')';?>
			</div>
			<div class="table_conten_accion_a">
				<?php
				// Si no esta en revicion mostramos las siguientes opciones
				if($revicion == 'NO'){
					?>
					<a href="/admin-co/?category=course&sub=preview&id=<?php echo $id_curso; ?>" class="icon-ver" title="Vista Previa"></a>
					<a href="/admin-co/?category=course&sub=edit&id=<?php echo $id_curso; ?>" class="icon-editar" title="Editar Curso"></a>
					<?php
					// Verificamos si el curso esta publicado para mostrar estadisticas
					if($publicado == 'YES'){
						?>
						<a class="icon-estadistica" title="Estadisticas" onclick="javascript:curso_estadistica('<?php echo $id_curso; ?>')"></a>
						<?php
					}

					// Si el curso no esta publicado mostramos opcion para publicar
					if($publicado == 'NO'){
						?><a class="icon-public" title="Publicar curso" onclick="javascript:curso_public('<?php echo $id_curso; ?>','<?php echo $revicion; ?>')"></a><?php
					}
					// Si hay mensajes sobre el status del curso los mostramos
					if(!empty($instruccion)){
						?>
						<a class="icon-mensaje" title="Mensajes sobre el curso" onclick="javascript:curso_mensaje('<?php echo $id_curso; ?>')"></a>
						<?php
					}
					
					// Si el curso no esta publicado el usuario puede eliminar el curso
					if($publicado == 'NO'){
						?><a class="icon-eliminar" onclick="javascript:curso_delete(<?php echo $id_curso ;?>)" title="Eliminar"></a><?php
					} else {
						if($_SESSION['nivel'] == 10){
							?><a class="icon-eliminar" onclick="javascript:curso_delete(<?php echo $id_curso ;?>)" title="Eliminar"></a><?php
						}
					}
				} else {
					// Si el curso esta en revicion mostramos mensaje
					?>Curso en revision . . .<?php
				}
				?>
			</div>
		</div>
	<?php
	}
	$_pagi_result->close();
	?>
	</div>
	<?php
}