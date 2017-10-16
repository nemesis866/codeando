<?php
/********************************************************************
Archivo en donde editamos la configuracion del curso

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

$fnc = new Fnc();
$db = new Db();

// Obtenemos las variables
$autor = $_SESSION['id'];
$nivel = $_SESSION['nivel'];
if(empty($_GET['id'])){ $id_curso = '';} else { $id_curso = $_GET['id'];}
if(empty($_GET['item'])){ $item = '';} else { $item = $_GET['item'];}

?>
<div class="alert">
	<img src="/img/alert.png">
	<p>En esta seccion podra introducir los temas y capitulos del curso, asi como videos, documentación e información sobre el mismo.</p>
</div>

<?php
// Consultamos si el usuario es el autor del curso
$result_temp = $db->mysqli_select("SELECT Count(id_curso) FROM cursos WHERE id_curso='$id_curso' AND autor='$autor'");
$curso = $result_temp->fetch_row();
$result_temp->close();

// Verificamos si el usuario es el autor del curso
if($curso[0] > 0 || $nivel == 10){
	// Si es el autor procedemos

	$i = 1; // Variable para el control de capitulos
	?>

	<ul id="u">
	<?php

	// Obtenemos los capitulos del curso
	$result = $db->mysqli_select("SELECT titulo,id_capitulo,visibility FROM capitulos WHERE id_curso='$id_curso' AND autor='$autor' ORDER BY orden");
	while($row = $result->fetch_assoc()){
		$id_cap = $row['id_capitulo'];
		$titulo = $row['titulo'];
		$visibility = $row['visibility'];
		$_i = 1; // Variable para el control de temas
		?>

		<li class="item" draggable="true" id="<?php echo $id_cap; ?>">
		<div class="capitulo">
			<div id="cap_<?php echo $id_cap; ?>">
				<span class="number_cap_<?php echo $id_cap; ?>">Capitulo <?php echo $i; ?>: </span>
				<span id="cap_title_<?php echo $id_cap; ?>"><?php echo $titulo; ?></span> 
				<span class="icon icon-edit" onclick="javascript:cap_form('<?php echo $id_cap; ?>')" title="Editar"></span>
				<?php
				// Verificamos la visibilidad del capitulo
				if($visibility == 'YES'){
					// Si es visible mostramos sus opciones
					?>
					<span style="display:none;" id="cap_draw_<?php echo $id_cap; ?>" class="right icon icon-capdraw" title="Cambiar a publico" onclick="javascript:cap_visibility(<?php echo $id_cap; ?>, 'draw')"></span>
					<span style="display:block" id="cap_public_<?php echo $id_cap; ?>" class="right icon capitulo_public icon-cappublic" title="Cambiar a borrador" onclick="javascript:cap_visibility(<?php echo $id_cap; ?>, 'public')"></span>
					<?php
				} else {
					// Si no es visible mostramos sus opciones
					?>
					<span id="cap_draw_<?php echo $id_cap; ?>" class="right icon icon-capdraw" title="Cambiar a publico" onclick="javascript:cap_visibility(<?php echo $id_cap; ?>, 'draw')"></span>
					<span id="cap_public_<?php echo $id_cap; ?>" class="right icon capitulo_public icon-cappublic" title="Cambiar a borrador" onclick="javascript:cap_visibility(<?php echo $id_cap; ?>, 'public')"></span>
					<?php
				}
				?>
				<span class="right icon icon-delete" onclick="javascript:cap_delete(<?php echo $id_cap; ?>)" title="Eliminar capitulo"></span>
				<span id="cap_mostrar_<?php echo $id_cap; ?>" class="right icon icon-mas" onclick="javascript:cap_mostrar('<?php echo $id_cap; ?>')" title="Mostrar temas"></span>
				<span id="cap_ocultar_<?php echo $id_cap; ?>" class="right icon icon-menos" style="display:none" onclick="javascript:cap_ocultar('<?php echo $id_cap; ?>')" title="Ocultar temas"></span>
			</div>
			<div id="form_<?php echo $id_cap; ?>" style="display:none;">
				<div id="cargando_line_<?php echo $id_cap; ?>" class="cargando_line"></div>
				<form id="form_cap_<?php echo $id_cap; ?>">
					<span class="number_cap_<?php echo $id_cap; ?>">Capitulo <?php echo $i; ?>: </span>
					<input type="text" id="title" class="input" maxlength="60" value="<?php echo $titulo; ?>">
					<span class="icon icon-submit" onclick="javascript:cap_submit('<?php echo $id_cap; ?>')" title="Guardar"></span>
					<span class="icon icon-cancel" onclick="javascript:cap_cancel('<?php echo $id_cap; ?>')" title="Cancelar"></span>
				</form>
			</div>
			<div id="cap_delete_<?php echo $id_cap; ?>" style="display:none;">
				Esta seguro de eliminar el capitulo: 
				<span class="icon icon-confirm" onclick="javascript:cap_delete_yes(<?php echo $id_cap; ?>,<?php echo $id_curso; ?>)"></span>
				<span class="icon icon-cancel" onclick="javascript:cap_cancel(<?php echo $id_cap; ?>)"></span>
			</div>
		</div>
		<div id="mostrar_tema_<?php echo $id_cap; ?>" style="display:none">
			<ul class="add_temas_<?php echo $id_cap; ?>" style="list-style:none">
			<?php
			$result2 = $db->mysqli_select("SELECT id_tema,titulo,info,doc,video,github,orden,visibility FROM temas WHERE id_curso='$id_curso' AND id_capitulo='$id_cap' ORDER BY orden");
			while($row2 = $result2->fetch_assoc()){
				$id_tema = $row2['id_tema'];
				$tema_titulo = $row2['titulo'];
				$tema_info = $row2['info'];
				$tema_doc = $row2['doc'];
				$tema_video = $row2['video'];
				$tema_github = $row2['github'];
				$tema_orden = $row2['orden'];
				$tema_visibility = $row2['visibility'];
				?>
				<li class="items<?php echo $id_cap; ?> orden<?php echo $id_tema; ?>" data-id="<?php echo $id_tema; ?>" data-orden="<?php echo $tema_orden; ?>" id="li_<?php echo $id_cap.''.$id_tema; ?>">
				<div class="tema" id="<?php echo $id_cap.''.$id_tema; ?>">
					<div id="tema_<?php echo $id_cap.''.$id_tema; ?>">
						<span class="number_tema_<?php echo $id_tema; ?>">Tema <?php echo $_i; ?>: </span>
						<span id="tema_title_<?php echo $id_cap.''.$id_tema; ?>"><?php echo $tema_titulo; ?></span> 
						<span class="icon icon-edit_tema" onclick="javascript:tema_form('<?php echo $id_cap.''.$id_tema; ?>')" title="Editar"></span>
						<span class="right icon icon-bajar" title="Bajar tema" onclick="javascript:tema_bajar(<?php echo $id_cap; ?>,<?php echo $id_tema; ?>)"></span>
						<span class="right icon icon-subir" title="Subir tema" onclick="javascript:tema_subir(<?php echo $id_cap; ?>,<?php echo $id_tema; ?>)"></span>
						<span class="right icon icon-delete_tema" onclick="javascript:tema_delete(<?php echo $id_cap.''.$id_tema; ?>)" title="Eliminar tema"></span>
						<span id="mostrar_<?php echo $id_cap.''.$id_tema; ?>" class="right icon icon-mostrar" onclick="javascript:tema_mostrar('<?php echo $id_cap.''.$id_tema; ?>')" title="Mostrar opciones"></span>
						<span id="ocultar_<?php echo $id_cap.''.$id_tema; ?>" class="right icon icon-ocultar" style="display:none" onclick="javascript:tema_ocultar('<?php echo $id_cap.''.$id_tema; ?>')" title="Ocultar Opciones"></span>
					</div>
					<div id="form_<?php echo $id_cap.''.$id_tema; ?>" style="display:none;">
						<div id="cargando_line_<?php echo $id_cap.''.$id_tema; ?>" class="cargando_line"></div>
						<form id="form_tema_<?php echo $id_cap.''.$id_tema; ?>">
							<div class="error"></div>
							<span class="number_tema_<?php echo $id_tema; ?>">Tema <?php echo $_i; ?>: </span>
							<input type="text" id="title" class="input" maxlength="60" value="<?php echo $tema_titulo; ?>" required>
							<span class="icon icon-submit_tema" onclick="javascript:tema_submit('<?php echo $id_cap.''.$id_tema; ?>','<?php echo $id_tema; ?>')" title="Guardar"></span>
							<span class="icon icon-cancel_tema" onclick="javascript:tema_cancel('<?php echo $id_cap.''.$id_tema; ?>')" title="Cancelar"></span>
						</form>
					</div>
					<div id="tema_delete_<?php echo $id_cap.''.$id_tema; ?>" style="display:none;">
						Esta seguro de eliminar el tema: 
						<span class="icon icon-confirm_tema" onclick="javascript:tema_delete_yes(<?php echo $id_tema; ?>,<?php echo $id_cap; ?>,<?php echo $id_curso; ?>)"></span>
						<span class="icon icon-cancel_tema" onclick="javascript:tema_cancel(<?php echo $id_cap.''.$id_tema; ?>)"></span>
					</div>
					<div id="iconos_<?php echo $id_cap.''.$id_tema; ?>" class="iconos" style="display:none">
						<?php
						if($tema_visibility == 'YES'){
							?>
							<span style="display:none" id="tema_draw_<?php echo $id_cap.''.$id_tema; ?>" class="tema_draw icon-draw" onclick="javascript:tema_visibility(<?php echo $id_cap.''.$id_tema; ?>, <?php echo $id_tema; ?>, 'draw')">Borrador</span>
							<span style="display:block" id="tema_public_<?php echo $id_cap.''.$id_tema; ?>" class="tema_public icon-public" onclick="javascript:tema_visibility(<?php echo $id_cap.''.$id_tema; ?>, <?php echo $id_tema; ?>, 'public')">Publico</span>	
							<?php
						} else {
							?>
							<span id="tema_draw_<?php echo $id_cap.''.$id_tema; ?>" class="tema_draw icon-draw" onclick="javascript:tema_visibility(<?php echo $id_cap.''.$id_tema; ?>, <?php echo $id_tema; ?>, 'draw')">Borrador</span>
							<span id="tema_public_<?php echo $id_cap.''.$id_tema; ?>" class="tema_public icon-public" onclick="javascript:tema_visibility(<?php echo $id_cap.''.$id_tema; ?>, <?php echo $id_tema; ?>, 'public')">Publico</span>
							<?php
						}
						?>
						Opciones:
						<span class="icon-info" title="Información" onclick="javascript:tema_router('<?php echo $id_cap.''.$id_tema; ?>','info')"></span>
						<span class="icon-doc" title="Documentación" onclick="javascript:tema_router('<?php echo $id_cap.''.$id_tema; ?>','doc')"></span>
						<span class="icon-video" title="Insertar video" onclick="javascript:tema_router('<?php echo $id_cap.''.$id_tema; ?>','video')"></span>
						<span class="icon-github" title="Insertar repositorio" onclick="javascript:tema_router('<?php echo $id_cap.''.$id_tema; ?>','github')"></span>
					</div>
				</div>
				<div id="_info_<?php echo $id_cap.''.$id_tema; ?>" class="info" style="display:none;">
					<div id="info_<?php echo $id_cap.''.$id_tema; ?>">
						<p>Información sobre el tema:</p>
						<div class="cargando"></div>
						<form id="form_info_<?php echo $id_cap.''.$id_tema; ?>">
							<div id="resp_toolbox">
								<span onclick="javascript:toolbox(2,'info<?php echo $id_cap.''.$id_tema; ?>')" class="icon-bold" title="Negrita"><strong></strong></span>
								<span onclick="javascript:toolbox(3,'info<?php echo $id_cap.''.$id_tema; ?>')" class="icon-italic" title="Cursiva"><i></i></span>
								<span onclick="javascript:toolbox(4,'info<?php echo $id_cap.''.$id_tema; ?>')" class="icon-underline" title="Subrayado"><u></u></span>
								<span onclick="javascript:toolbox(5,'info<?php echo $id_cap.''.$id_tema; ?>')" class="icon-strike" title="Tachado"></span>
							</div>
							<p><textarea id="info<?php echo $id_cap.''.$id_tema; ?>" class="input_tema" placeholder="Ingrese información sobre el tema, sea claro y detallado."><?php echo $fnc->tema_replace($tema_info); ?></textarea></p>
						</form>
						<p><button id="button_info_<?php echo $id_cap.''.$id_tema; ?>" class="submit" onclick="javascript:tema_info('<?php echo $id_cap.''.$id_tema; ?>','<?php echo $id_tema; ?>')">Guardar</button>
							<span id='data_info_<?php echo $id_cap.''.$id_tema; ?>'></span></p>
					</div>
					<div id="doc_<?php echo $id_cap.''.$id_tema; ?>" style="display:none">
						<p>Documentación sobre el tema:</p>
						<div class="cargando"></div>
						<form id="form_doc_<?php echo $id_cap.''.$id_tema; ?>">
							<div id="resp_toolbox">
								<span onclick="javascript:toolbox(1,'doc<?php echo $id_cap.''.$id_tema; ?>')" class="icon-code" title="Insertar codigo">CODE</span>
								<span onclick="javascript:toolbox(2,'doc<?php echo $id_cap.''.$id_tema; ?>')" class="icon-bold" title="Negrita"><strong></strong></span>
								<span onclick="javascript:toolbox(3,'doc<?php echo $id_cap.''.$id_tema; ?>')" class="icon-italic" title="Cursiva"><i></i></span>
								<span onclick="javascript:toolbox(4,'doc<?php echo $id_cap.''.$id_tema; ?>')" class="icon-underline" title="Subrayado"><u></u></span>
								<span onclick="javascript:toolbox(5,'doc<?php echo $id_cap.''.$id_tema; ?>')" class="icon-strike" title="Tachado"></span>
							</div>
							<p><textarea id="doc<?php echo $id_cap.''.$id_tema; ?>" class="input_tema" placeholder="Si el tema necesita documentación de apoyo, ingresela aqui."><?php echo $fnc->tema_replace($tema_doc); ?></textarea></p>
						</form>
						<p>
							<button id="button_doc_<?php echo $id_cap.''.$id_tema; ?>" class="submit" onclick="javascript:tema_doc('<?php echo $id_cap.''.$id_tema; ?>','<?php echo $id_tema; ?>')">Guardar</button>
							<span id='data_doc_<?php echo $id_cap.''.$id_tema; ?>'></span>
						</p>
					</div>
					<div id="video_<?php echo $id_cap.''.$id_tema; ?>" style="display:none">
						<p>Ingrese la url del video del tema (Youtube):</p>
						<div class="cargando"></div>
						<form id="form_video_<?php echo $id_cap.''.$id_tema; ?>">
							<p><input type="text" id="video<?php echo $id_cap.''.$id_tema; ?>" class="input_tema" placeholder="Ingrese la url del video de youtube" value="<?php if(!empty($tema_video)){ echo "https://www.youtube.com/watch?v=$tema_video"; } ?>" required></p>
						</form>
						<p>
							<button id="button_video_<?php echo $id_cap.''.$id_tema; ?>" class="submit" onclick="javascript:tema_video('<?php echo $id_cap.''.$id_tema; ?>','<?php echo $id_tema; ?>')">Guardar</button>
							<span id='data_video_<?php echo $id_cap.''.$id_tema; ?>'></span>
						</p>
						<p id="data_video_title_<?php echo $id_cap.''.$id_tema; ?>">
							<?php
							if(!empty($tema_video)){
								?>Titulo: <?php echo $fnc->youtube_video($tema_video);
							}
							?>
						</p>
					</div>
					<div id="github_<?php echo $id_cap.''.$id_tema; ?>" style="display:none">
						<p>Ingrese la url del repositorio del tema (Github):</p>
						<div class="cargando"></div>
						<form id="form_github_<?php echo $id_cap.''.$id_tema; ?>">
							<p><input type="text" id="github<?php echo $id_cap.''.$id_tema; ?>" class="input_tema" placeholder="Ingrese la url del repositorio de github" value="<?php if(!empty($tema_github)){ echo "$tema_github"; } ?>" required></p>
						</form>
						<p>
							<button id="button_github_<?php echo $id_cap.''.$id_tema; ?>" class="submit" onclick="javascript:tema_github('<?php echo $id_cap.''.$id_tema; ?>','<?php echo $id_tema; ?>')">Guardar</button>
							<span id='data_github_<?php echo $id_cap.''.$id_tema; ?>'></span>
						</p>
						<p id="data_github_title_<?php echo $id_cap.''.$id_tema; ?>">
							<?php
							if(!empty($tema_github)){
								?>Repositorio: <?php echo "Tiene un repositorio asignado a este tema";
							}
							?>
						</p>
					</div>
				</div>
				</li>
				<?php
				$_i++;
			}
			$result2->close();
			?>
			<?php
			$i++;
			?>
			</ul>
			<div id="conteo_<?php echo $id_cap; ?>" data-i="<?php echo $_i; ?>"></div>
		<?php
		// Si esta en modo review no mostramos el boton agregar
		if($item != 'review'){
			?>
			<div id="tema_nuevo" class="tema_nuevo<?php echo $id_cap; ?> icon-agregar" onclick="javascript:tema_nuevo(<?php echo $id_curso; ?>,<?php echo $id_cap; ?>)">Agregar Tema nuevo</div>
			<div id="cargando_tema<?php echo $id_cap; ?>"></div>
			<?php
		}
		?>
		</div>
		</li>
		<?php
	}
	$result->close();
	?>
	</ul>
	<?php
	// Si esta en modo review no mostramos el boton agregar
	if($item != 'review'){
		?>
		<div id="capitulo_nuevo" class="icon-agregar" onclick="javascript:cap_nuevo(<?php echo $i; ?>,<?php echo $id_curso; ?>)">Agregar Capitulo nuevo</div>
		<div class="cargando"></div>
		<?php
	}
} else {
	// Si el usuario no es el autor del curso mostramos mensaje
	?><p>Esta intentando acceder a un curso que no es de su autoria...</p><?php
}