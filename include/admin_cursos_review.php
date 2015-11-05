<?php
/************************************************
Archivo para revisar cursos

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compugmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Obtenemos el id del usuario
$user = $_SESSION['id'];

// Iniciamos la base de datos
$db = new Db();

// Consultamos si el usuario tiene cursos
$result_temp = $db->mysqli_select("SELECT Count(id_curso) FROM cursos WHERE revicion='YES'");
$count = $result_temp->fetch_row();
$result_temp->close();

// Verificamos si hay cursos para revicion
if($count[0] == 0){
	// Si no hay cursos mostramos mensaje
	?><p>No hay cursos para revisión</p><?php
} else {
	// Si hay cursos
	// Cargamos el paginador de cursos
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
		while($row = $_pagi_result->fetch_assoc()){
			$id_curso = $row['id_curso'];
			$categoria = $row['categoria'];
			$titulo = $row['titulo'];
			$publicado = $row['public'];
			$url = $row['url'];
			$revicion = $row['revicion'];
			$content = $row['instruccion'];

			// Creamos la url para ver el curso
			$url = "/$categoria/$url/$id_curso/";

			if($publicado == 'NO'){
				$public = 'OffLine';
			} else {
				$public = 'OnLine';
			}
			?>
			<div id="info_<?php echo $id_curso; ?>" class="table_conten_index" style="display:none">
			</div>
			<div id="publicar_<?php echo $id_curso; ?>" class="table_conten_index" style="display:none">
				<div id="publicar_cargando_<?php echo $id_curso; ?>">
					<p>Instrucciones para publicar el curso (Si no se publica, al usuario le sera notificado el motivo):</p>
					<textarea id="content_<?php echo $id_curso; ?>" class="input"></textarea>
					<p style="margin-top:10px;">
						<button class="submit" onclick="javascript:curso_publicar_yes('<?php echo $id_curso; ?>')">Publicar</button> 
						<button class="submit" onclick="javascript:curso_publicar_no('<?php echo $id_curso; ?>')">No publicar</button>
						<button class="submit" onclick="javascript:curso_publicar_cancel('<?php echo $id_curso; ?>')">Cancelar</button>
					</p>
					<?php
					// Si hay avizos anteriores los mostramos
					if(!empty($content)){
						?>
						<div id="content<?php echo $id_curso; ?>"></div>
							<h3>Ultimos avizos</h3>
							<p><?php echo $content; ?></p>
						</div>
						<?php
					}
					?>
				</div>
				<div class="cargando cargando<?php echo $id_curso; ?>"></div>
			</div>
			<div id="curso_<?php echo $id_curso ;?>" class="table_conten_index">
				<div class="table_conten_nombre">
					<?php echo $titulo;?>
				</div>
				<div class="table_conten_accion_a">
					<a href="/admin-co/?category=course&sub=preview&id=<?php echo $id_curso; ?>&item=review" class="icon-ver" title="Vista Previa"></a>
					<a href="/admin-co/?category=course&sub=edit&id=<?php echo $id_curso; ?>&item=review" class="icon-editar" title="Editar Curso"></a>
					<a class="icon-public" title="Publicar curso" onclick="javascript:curso_publicar('<?php echo $id_curso; ?>')"></a>
					<?php
					// Si el curso no esta publicado el usuario puede eliminar el curso
					if($publicado == 'NO'){
						?><a class="icon-eliminar" onclick="javascript:curso_delete(<?php echo $id_curso ;?>)" title="Eliminar"></a><?php
					} else {
						if($_SESSION['nivel'] == 10){
							?><a class="icon-eliminar" onclick="javascript:curso_delete(<?php echo $id_curso ;?>)" title="Eliminar"></a><?php
						}
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