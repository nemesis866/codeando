<?php
/************************************************
Archivo servidor de las discuciones

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

set_time_limit(0);

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

require_once '../config.php';
require_once 'Fnc.php';
require_once 'Db.php';

$fnc = new Fnc();
$db = new Db();

if(empty($_POST['type'])){ $type = '';} else { $type = addslashes($_POST['type']);}

// Router del server
switch($type){
	case 'dis_delete': // OK
		dis_delete($fnc,$db);
		break;
	case 'dis_mostrar': // OK
		dis_mostrar($fnc,$db);
		break;
	case 'dis_no': // OK
		dis_no($fnc,$db);
		break;
	case 'dis_nueva': // OK
		dis_nueva($fnc,$db);
		break;
	case 'dis_pop': // OK
		dis_pop($fnc,$db);
		break;
	case 'dis_propia': // OK
		dis_propia($fnc,$db);
		break;
	case 'dis_voto_no': // OK
		dis_voto_no($fnc,$db);
		break;
	case 'dis_voto_si': // OK
		dis_voto_si($fnc,$db);
		break;
	case 'discucion_cargar': // OK
		discucion_cargar($fnc,$db);
		break;
	case 'discucion_publicar': // OK
		discucion_publicar($fnc,$db);
		break;
	case 'discucion_editar': // OK
		discucion_editar($fnc,$db);
		break;
	case 'res_delete': // OK
		res_delete($fnc,$db);
		break;
	case 'res_editar': // OK
		res_editar($fnc,$db);
		break;
	case 'res_publicar': // OK
		res_publicar($fnc,$db);
		break;
	case 'res_voto_no': // OK
		res_voto_no($fnc,$db);
		break;
	case 'res_voto_si': // OK
		res_voto_si($fnc,$db);
		break;
}

// Eliminamos una discusion
function dis_delete($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID discusion

	// Consultamos si existen respuestas en la disusion
	$result_temp = $db->mysqli_select("SELECT Count(id_respuesta) FROM respuestas WHERE id_discucion='$id'");
	$count = $result_temp->fetch_row();
	$result_temp->close();

	// Verificamos si existen respuestas en la disusion
	if($count[0] == 0){
		// Consultamos si existen archivos en la discusion
		$result = $db->mysqli_select("SELECT Count(id_file) FROM files WHERE id_discucion='$id'");
		$count1 = $result->fetch_row();
		$result->close();

		// Verificamos si existen archivos en la discusion
		if($count1[0] > 0){
			// Eliminamos los archivos
			$delete1 = $db->mysqli_action("DELETE FROM files WHERE id_discucion='$id'");
		}

		// Si no existen eliminamos discusion
		$delete = $db->mysqli_action("DELETE FROM discucion WHERE id_discucion='$id'");

		echo json_encode(array('status'=>'La discusión se elimino con exito','id'=>$id));
	} else {
		// Si tienen respuestas no eliminamos y mostramos el error
		echo json_encode(array('error'=>'No se puede eliminar, la discusion contiene respuestas','id'=>$id));
	}

	exit();
}

// Cargamos una discusion para ver detalles
function dis_mostrar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID discusion
	$user = $_SESSION['id']; // ID usuario
	$res = "";
	$enlace = "";
	$id_respuesta_pop = "";
	$files_temp = '';

	// Obtenemos discucion
	$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_discucion='$id'");
	$counter = $result->num_rows;

	// Verificamos si existe una discusion
	if($counter > 0){
		// Obtenemos los detalles de la discusion
		while($row = $result->fetch_assoc()){
			$titulo = $row['titulo'];
			$autor = $row['autor'];
			$contenido = $row['contenido'];
			$fecha = $row['fecha'];
			$votos = $row['votos'];
			$respuestas = $row['respuestas'];
			$link = $row['link'];

			// Mostramos html
			$contenido = $fnc->mostrar_html($contenido);

			// obtenemos el nombre del usuario
			$result2 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$autor'");
			while($row2 = $result2->fetch_assoc()){
				$nombre = ucwords($row2['nombre']);
				$fbid = (empty($row2['fbid'])) ? '' : $row2['fbid'];
				$avatar = (empty($row2['avatar'])) ? '' : $row2['avatar'];
				$img = '';
			}
			$result2->close();

			// Mostramos avatar del usuario
			if(empty($fbid)){
				// Si no tiene ID facebook
				$img = "<img src='/avatar/$avatar'>";
			} else {
				// Si tiene id facebook
				$img = "<img src='http://graph.facebook.com/$fbid/picture?type=large'>";
			}

			// Si existe un enlace
			if(!empty($link)){
				$enlace = "<br><br>
					<a class='dis_enlace icon-link' href='$link' title='Aporte' target='_blank'>Enlace</a><br><br>";
			}

			// Calculamos la diferencia entre fecha publicacion y fecha para mostrar
			$segundos_dis = strtotime('now') - strtotime($fecha);
			$minutos_dis = intval($segundos_dis/60);

			// Guardamos la informacion
			$dis = "<div class='discucion dis_$id'>
				<input type='hidden' id='res_dis_id' value='$id'>
				<p class='d_title'>$titulo</p>
				<p class='d_subtitle'>
					<span id='d_user' onclick='javascript:user_mostrar(\"$autor\")'>$img $nombre: </span>
					<span>".$fnc->FechaCOM($fecha)."</span>
				</p>
				<div id='dis_content_$id' class='d_content'>".$fnc->url_replace($contenido)."$enlace";

			// Consultamos si existen archivos en esta discusion
			$result5 = $db->mysqli_select("SELECT id_file,name,ext,size FROM files WHERE id_discucion='$id'  ORDER BY name");
			$count5 = $result5->num_rows;

			// Verficamos si existen archivos en esta discusion
			if($count5 > 0){
				// Si hay archivos los mostramos
				while($row5 = $result5->fetch_assoc()){
					$f_id = $row5['id_file'];
					$f_ext = $row5['ext'];
					$f_name = $row5['name'];
					$f_size = $row5['size'];

					$dis .= "<div class='files_dis icon-$f_ext file_$f_id' onclick='javascript:files_mostrar($f_id, \"file\")'>$f_name ($f_size Kb)</div>";
					$files_temp .= "<div class='files icon-$f_ext file_$f_id' onclick='javascript:files_mostrar($f_id, \"file\")'>$f_name ($f_size Kb)<span onclick='javascript:files_delete_edit($f_id, $id)'>X</span></div>";
				}
				$result5->close();
			}

			// Armamos formulario para editar a la discusion
			$dis .= "</div>
				<div id='dis_form_$id' style='display:none;'>
					<form id='form_discucion_edit'>
						<div id='resp_toolbox' style='display:block;'>
							<span onclick='javascript:toolbox(1,\"content_dis_edit\")' class='icon-code' title='Insertar codigo' style='float:none;'>CODE</span>
							<span onclick='javascript:toolbox(2,\"content_dis_edit\")' class='icon-bold' title='Negrita' style='float:none;'><strong></strong></span>
							<span onclick='javascript:toolbox(3,\"content_dis_edit\")' class='icon-italic' title='Cursiva' style='float:none;'><i></i></span>
							<span onclick='javascript:toolbox(4,\"content_dis_edit\")' class='icon-underline' title='Subrayado' style='float:none;'><u></u></span>
							<span onclick='javascript:toolbox(5,\"content_dis_edit\")' class='icon-strike' title='Tachado' style='float:none;'></span>
						</div>
						<textarea id='content_dis_edit' placeholder='Tienes alguna duda o aporte? publicalo aqui'>".$fnc->tema_replace($contenido)."</textarea>
						<p>
							<label>Enlace del aporte:</label>
							<input type='text' id='link_edit' value='$link' placeholder='http://' style='display:block;'>
						</p>
					</form>
					<div id='content_dis_edit_files'>Arrastre aqui sus archivos<br>(Tamaño maximo 10 Kb.)</div>
					<div id='content_dis_edit_results'>$files_temp</div>
					<div id='content_dis_edit_cargando'></div>
					<p>
						<button id='submit_edit' class='submit' onclick='javascript:dis_editar($id)' style='display:inline-block;'>Editar</button>
						<button id='cancelar_edit' class='submit' onclick='javascript:dis_edit_cancelar($id)' style='display:inline-block;'>Cancelar</button>
						<span id='dis_edit_info'></span>
					</p>
				</div>
				<p class='d_footer' id='dis_option'>";

			// Si esta dentro del rango de los 15 minutos mostramos opciones adicionales para discusion
			if($minutos_dis <= 15 && $user == $autor){
				$dis .= "
					<span style='float:none;' class='icon icon-edit' title='Editar discucion' onclick='javascript:dis_edit($id)'></span>
					<span style='float:none;' class='icon icon-delete' title='Eliminar discusion' onclick='javascript:dis_delete()'></span>";
			}

			$dis .= "
					<span class='icon icon-voto-down' title='Mal aporte' onclick='javascript:dis_voto_no($id)'></span>
					<span title='votos' class='dis_voto_$id'>$votos</span>
					<span class='icon icon-voto-up' title='Buen aporte' onclick='javascript:dis_voto_si($id)'></span> 
					&nbsp;
				</p>
				<p class='d_footer' id='dis_delete' style='display:none;'>
					Esta seguro de eliminar la discusion? <a onclick='javascript:dis_delete_yes($id)' style='margin-right:15px;'>SI</a><a onclick='javascript:dis_delete_no($id)'>NO</a>
				</p>
			</div>";
		}
		$result->close();

		// Consultamos si hay respuesta mas popular
		$result_temp = $db->mysqli_select("SELECT Count(id_respuesta) FROM respuestas WHERE id_discucion='$id' AND votos>'0'");
		$count = $result_temp->fetch_row();
		$result_temp->close();
		
		// Verificamos si hay respuesta mas popular
		if($count[0] > 0){
			// Obtenemos si hay respuesta popular
			$result1 = $db->mysqli_select("SELECT id_respuesta,fecha,contenido,autor,votos FROM respuestas WHERE id_discucion='$id' AND votos>'0' ORDER BY votos DESC LIMIT 1");
			while($row1 = $result1->fetch_assoc()){
				$id_respuesta_pop = $row1['id_respuesta'];
				$res_fecha = $row1['fecha'];
				$res_content = $row1['contenido'];
				$res_autor = $row1['autor'];
				$res_votos = $row1['votos'];

				$res_content = $fnc->mostrar_html($res_content);

				// Obtenemos el nombre del autor
				$result4 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$res_autor'");
				while($row4 = $result4->fetch_assoc()){
					$res_nombre = $row4['nombre'];
					$res_fbid = (empty($row4['fbid'])) ? '' : $row4['fbid'];
					$res_avatar = (empty($row4['avatar'])) ? '' : $row4['avatar'];
					$res_img = '';
				}
				$result4->close();

				// Armamos la imagen del autor de la respuesta
				if(empty($res_fbid)){
					// Si no tiene ID fb
					$res_img = "<img src='/avatar/$res_avatar'>";
				} else {
					// Si tiene ID fb
					$res_img = "<img src='http://graph.facebook.com/$res_fbid/picture?type=large'>";
				}

				$dis .= "<div class='respuestas'>
					<p class='r_title'>Mejor respuesta <span class='icon-check' style='float:none;'></span></p>
					<p><span class='r_user' onclick='javascript:user_mostrar(\"$res_autor\")'>$res_img $res_nombre: </span>
						<span>".$fnc->FechaCOM($res_fecha)."</p>
					<div class='d_content'>".$res_content."</div>
					<p>
						<span class='icon icon-voto-down' title='Mal aporte' onclick='javascript:res_voto_no($id_respuesta_pop)'></span>
						<span title='votos' class='res_voto_$id_respuesta_pop'>$res_votos</span>
						<span class='icon icon-voto-up' title='Buen aporte' onclick='javascript:res_voto_si($id_respuesta_pop)'></span> 
						&nbsp;
					</p>
				</div>
				<p>Otras respuestas:</p>";
			}
			$result1->close();
		}

		// Cargamos las respuestas restantes
		$result2 = $db->mysqli_select("SELECT id_respuesta,fecha,contenido,autor,votos FROM respuestas WHERE id_discucion='$id' ORDER BY fecha");
		while($row2 = $result2->fetch_assoc()){
			$id_respuesta = $row2['id_respuesta'];
			$res_fecha = $row2['fecha'];
			$res_content = $row2['contenido'];
			$res_autor = $row2['autor'];
			$res_votos = $row2['votos'];

			$res_content = $fnc->mostrar_html($res_content);

			// Obtenemos el nombre del autor
			$result3 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$res_autor'");
			while($row3 = $result3->fetch_assoc()){
				$res_nombre = $row3['nombre'];
				$res_fbid = (empty($row3['fbid'])) ? '' : $row3['fbid'];
				$res_avatar = (empty($row3['avatar'])) ? '' : $row3['avatar'];
				$res_img = '';
			}
			$result3->close();

			// Armamos la imagen del autor de la respuesta
			if(empty($res_fbid)){
				// Si no tiene ID fb
				$res_img = "<img src='/avatar/$res_avatar'>";
			} else {
				// Si tiene ID fb
				$res_img = "<img src='http://graph.facebook.com/$res_fbid/picture?type=large'>";
			}

			// Calculamos la diferencia entre fecha publicacion y fecha para mostrar
			$segundos_res = strtotime('now') - strtotime($res_fecha);
			$minutos_res = intval($segundos_res/60);

			// Si hay mejor respuestas no la publicamos en otras respuestas
			if($id_respuesta_pop != $id_respuesta){
				$res .= "<div class='respuestas' id='respuestas_$id_respuesta'>
					<p><span class='r_user' onclick='javascript:user_mostrar(\"$res_autor\")'>$res_img $res_nombre: </span>
						<span>".$fnc->FechaCOM($res_fecha)."</p>
					<div class='d_content' id='res_content_$id_respuesta'>".$res_content."</div>
					<p id='res_option_$id_respuesta'>";

				// Si esta dentro del rango de los 15 minutos mostramos opciones adicionales para respuesta
				if($minutos_res <= 15 && $user == $autor){
					$res .= "
						<span style='float:none;' class='icon icon-edit' title='Editar respuesta' onclick='javascript:res_edit($id_respuesta)'></span>
						<span style='float:none;' class='icon icon-delete' title='Eliminar respuesta' onclick='javascript:res_delete($id_respuesta)'></span>";
				}
				
				$res .= "<span class='icon icon-voto-down' title='Mal aporte' onclick='javascript:res_voto_no($id_respuesta)'></span>
						<span title='votos' class='res_voto_$id_respuesta'>$res_votos</span>
						<span class='icon icon-voto-up' title='Buen aporte' onclick='javascript:res_voto_si($id_respuesta)'></span> 
						&nbsp;
					</p>
					<p class='r_footer' id='res_delete_$id_respuesta' style='display:none;'>
						Esta seguro de eliminar la respuesta? <a onclick='javascript:res_delete_yes($id_respuesta, $id)' style='margin-right:15px;'>SI</a><a onclick='javascript:res_delete_no($id_respuesta)'>NO</a>
					</p>
				</div>";

				// Si esta dentro del rango de los 15 minutos mostramos opciones adicionales para respuesta
				if($minutos_res <= 15 && $user == $autor){
					$res .= "<div id='res_form_$id_respuesta' class='res_form_edit' style='display:none;'>
						<form id='form_res_edit'>
							<div id='resp_toolbox' style='display:block;'>
								<span onclick='javascript:toolbox(1,\"content_res_edit_$id_respuesta\")' class='icon-code' title='Insertar codigo' style='float:none;'>CODE</span>
								<span onclick='javascript:toolbox(2,\"content_res_edit_$id_respuesta\")' class='icon-bold' title='Negrita' style='float:none;'><strong></strong></span>
								<span onclick='javascript:toolbox(3,\"content_res_edit_$id_respuesta\")' class='icon-italic' title='Cursiva' style='float:none;'><i></i></span>
								<span onclick='javascript:toolbox(4,\"content_res_edit_$id_respuesta\")' class='icon-underline' title='Subrayado' style='float:none;'><u></u></span>
								<span onclick='javascript:toolbox(5,\"content_res_edit_$id_respuesta\")' class='icon-strike' title='Tachado' style='float:none;'></span>
							</div>
							<textarea id='content_res_edit_$id_respuesta' class='content_res_edit' placeholder='Tienes alguna respuesta? publicalo aqui'>".$fnc->tema_replace($res_content)."</textarea>
						</form>
						<p>
							<button id='res_submit_edit' class='submit' onclick='javascript:res_editar($id_respuesta, $id)' style='display:inline-block;'>Editar</button>
							<button id='res_cancelar_edit' class='submit' onclick='javascript:res_edit_cancelar($id_respuesta)' style='display:inline-block;'>Cancelar</button>
							<span id='res_edit_info'></span>
						</p>
					</div>";
				}
			}
		}
		$result2->close();

		echo json_encode(array('status'=>'Se cargo discucion correctamente','dis'=>$dis,'res'=>$res,'id'=>$id));
	} else {
		// Si no existe mostramos mensaje
		echo json_encode(array('error'=>'La discusión ha sido eliminada por su autor','id'=>$id));
	}

	exit();
}

// Cargamos mas discuciones no respondidas
function dis_no($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$start = $fnc->secure_sql($_POST['start']) + 10; // Control de carga
	$limit = 10; // Numero de notificaciones a cargar
	$dis_no = "";

	// Consultamos que si existan mas discuciones por cargar
	$result_temp = $db->mysqli_select("SELECT Count(id_discucion) FROM discucion WHERE id_curso='$id' AND respuestas='0' ORDER BY fecha DESC");
	$count = $result_temp->fetch_row();
	$result_temp->close();

	// Verificamos si existen discusiones por cargar
	if($_POST['start'] <= $count[0]){
		// Obtenemos mas discuciones nuevas
		$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' AND respuestas='0' ORDER BY fecha DESC LIMIT {$start}, {$limit}");
		while($row = $result->fetch_assoc()){
			$no_titulo = $row['titulo'];
			$no_autor = $row['autor'];
			$no_contenido = $row['contenido'];
			$no_fecha = $row['fecha'];
			$no_votos = $row['votos'];
			$no_respuestas = $row['respuestas'];
			$no_id = $row['id_discucion'];
			$no_link = $row['link'];

			// Damos formato al contenido
			$data = explode('<', $no_contenido);
			$no_contenido = substr($data[0], 0, 100);
			$no_contenido .= " ...";
			$no_contenido = ucfirst($no_contenido);
			$no_contenido = $fnc->code($no_contenido);

			// obtenemos el nombre del usuario
			$result2 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$no_autor'");
			while($row2 = $result2->fetch_assoc()){
				$no_nombre = ucfirst($row2['nombre']);
				$no_fbid = (empty($row2['fbid'])) ? '' : $row2['fbid'];
				$no_avatar = (empty($row2['avatar'])) ? '' : $row2['avatar'];
				$no_img = '';
			}
			$result2->close();

			// Generamos avatar del autor de la discusion
			if(empty($no_fbid)){
				// Si no tiene ID de facebook
				$no_img = "<img src='/avatar/$no_avatar'>";
			} else {
				// Si tiene ID de facebook
				$no_img = "<img src='http://graph.facebook.com/$no_fbid/picture?type=large'>";
			}

			// Verficamos si existen archivos en esta discusion
			$result3 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$no_id'");
			$count3 = $result3->num_rows;
			$result3->close();

			// Verificamos si existe el link o un archivo
			if($count3 > 0){
				$no_link = "<div class='link'><div id='icon$no_id' class='link_icon icon-archive-icon'></div></div>";
			} else if($no_link != '' || $no_link != null){
				$no_link = "<div class='link'><div id='icon$no_id' class='link_icon icon-link-icon'></div></div>";
			}

			// Guardamos la informacion
			$dis_no .= "<div class='discucion dis_$no_id' onclick='javascript:dis_mostrar($no_id)'>
				$no_link
				<p class='d_title'>$no_titulo</p>
				<p class='d_subtitle'>
					$no_img $no_nombre: 
					<span>".$fnc->FechaCOM($no_fecha)."</span>
				</p>
				<div class='d_content'>".$no_contenido."</div>
				<p class='d_footer'>Votos: <span class='dis_voto_$no_id' style='float:none'>$no_votos</span> <span class='dis_res_$no_id'>Respuestas: $no_respuestas</span></p>
			</div>";
		}
		$result->close();

		// Si no hay mas discusiones mostramos mensaje
		if($start > $count[0]){
			$error = 'No hay mas discusiones por cargar';	
		}
	} else {
		// Si no hay mas discusiones mostramos mensaje
		$error = 'No hay mas discusiones por cargar';
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Discusiones cargadas','dis'=>$dis_no,'start'=>$start,'error'=>$error));
	exit();
}

// Cargamos mas discuciones nuevas
function dis_nueva($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$start = $fnc->secure_sql($_POST['start']) + 10; // Control de carga
	$limit = 10; // Numero de discusiones a cargar
	$dis_nueva = "";

	// Consultamos que si existan mas discuciones por cargar
	$result_temp = $db->mysqli_select("SELECT Count(id_discucion) FROM discucion WHERE id_curso='$id'");
	$count = $result_temp->fetch_row();
	$result_temp->close();

	// Verificamos que si existan mas discuciones por cargar
	if($_POST['start'] <= $count[0]){
		// Obtenemos mas discuciones nuevas
		$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' ORDER BY fecha DESC LIMIT {$start},{$limit}");
		while($row = $result->fetch_assoc()){
			$nueva_titulo = $row['titulo'];
			$nueva_autor = $row['autor'];
			$nueva_contenido = $row['contenido'];
			$nueva_fecha = $row['fecha'];
			$nueva_votos = $row['votos'];
			$nueva_respuestas = $row['respuestas'];
			$nueva_id = $row['id_discucion'];
			$nueva_link = $row['link'];

			// Damos formato al contenido
			$data = explode('<', $nueva_contenido);
			$nueva_contenido = substr($data[0], 0, 100);
			$nueva_contenido .= " ...";
			$nueva_contenido = ucfirst($nueva_contenido);
			$nueva_contenido = $fnc->code($nueva_contenido);

			// obtenemos el nombre del usuario
			$result2 = $db->mysqli_select("SELECT nombre,avatar,fbid FROM usuarios WHERE id='$nueva_autor' LIMIT 1");
			while($row2 = $result2->fetch_assoc()){
				$nueva_nombre = ucfirst($row2['nombre']);
				$nueva_fbid = (empty($row2['fbid'])) ? '' : $row2['fbid'];
				$nueva_avatar = (empty($row2['avatar'])) ? '' : $row2['avatar'];
				$nueva_img = '';
			}
			$result2->close();

			// Verficamos si existen archivos en esta discusion
			$result3 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$nueva_id'");
			$count3 = $result3->num_rows;
			$result3->close();

			// Verificamos si existe el link o un archivo
			if($count3 > 0){
				// Si existe archivo mostramos icono en la discusion
				$nueva_link = "<div class='link'><div id='icon$nueva_id' class='link_icon icon-archive-icon'></div></div>";
			} else if($nueva_link != '' || $nueva_link != null){
				// Si existe enlace mostramos icono en la discusion
				$nueva_link = "<div class='link'><div id='icon$nueva_id' class='link_icon icon-link-icon'></div></div>";
			}

			// Generamos avatar para el autor de la discusion
			if(empty($nueva_fbid)){
				// Si no tiene ID de facebook
				$nueva_img = "<img src='/avatar/$nueva_avatar'>";
			} else {
				// Si tiene ID de facebook
				$nueva_img = "<img src='http://graph.facebook.com/$nueva_fbid/picture?type=large'>";
			}

			// Armamos la respuesta
			$dis_nueva .= "<div class='discucion dis_$nueva_id' onclick='javascript:dis_mostrar($nueva_id)'>
				$nueva_link
				<p class='d_title'>$nueva_titulo</p>
				<p class='d_subtitle'>
					$nueva_img $nueva_nombre: 
					<span>".$fnc->FechaCOM($nueva_fecha)."</span>
				</p>
				<div class='d_content'>".$nueva_contenido."</div>
				<p class='d_footer'>Votos: <span class='dis_voto_$nueva_id' style='float:none'>$nueva_votos</span> <span class='dis_res_$nueva_id'>Respuestas: $nueva_respuestas</span></p>
			</div>";
		}
		$result->close();

		// Si ya no hay mas discusiones por cargar mostramos error
		if($start > $count[0]){
			$error = 'No hay mas discusiones por cargar';	
		}
	} else {
		// Si ya no hay mas discusiones por cargar mostramos error
		$error = 'No hay mas discusiones por cargar';
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Discusiones cargadas','dis'=>$dis_nueva,'start'=>$start,'error'=>$error));
	exit();
}

// Cargamos mas discuciones populares
function dis_pop($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$start = $fnc->secure_sql($_POST['start']) + 10; // Control de carga
	$limit = 10; // Numero de discusiones a cargar
	$dis_pop = "";

	// Consultamos que si existan mas discuciones por cargar
	$result_temp = $db->mysqli_select("SELECT Count(id_discucion) FROM discucion WHERE id_curso='$id' ORDER BY votos DESC, fecha DESC");
	$count = $result_temp->fetch_row();
	$result_temp->close();

	// Verificamos si existen discusiones por cargar
	if($_POST['start'] <= $count[0]){
		// Obtenemos mas discuciones nuevas
		$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' ORDER BY votos DESC, fecha DESC LIMIT {$start}, {$limit}");
		while($row = $result->fetch_assoc()){
			$pop_titulo = $row['titulo'];
			$pop_autor = $row['autor'];
			$pop_contenido = $row['contenido'];
			$pop_fecha = $row['fecha'];
			$pop_votos = $row['votos'];
			$pop_respuestas = $row['respuestas'];
			$pop_id = $row['id_discucion'];
			$pop_link = $row['link'];

			// Damos formato al contenido
			$data = explode('<', $pop_contenido);
			$pop_contenido = substr($data[0], 0, 100);
			$pop_contenido .= " ...";
			$pop_contenido = ucfirst($pop_contenido);
			$pop_contenido = $fnc->code($pop_contenido);

			// obtenemos el nombre del usuario
			$result2 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$pop_autor'");
			while($row2 = $result2->fetch_assoc()){
				$pop_nombre = ucfirst($row2['nombre']);
				$pop_fbid = (empty($row2['fbid'])) ? '' : $row2['fbid'];
				$pop_avatar = (empty($row2['avatar'])) ? '' : $row2['avatar'];
				$pop_img = '';
			}
			$result2->close();

			// Generamos avatar del autor de la discusion
			if(empty($pop_fbid)){
				// Si no tiene ID de facebook
				$pop_img = "<img src='/avatar/$pop_avatar'>";
			} else {
				// Si tiene ID de facebook
				$pop_img = "<img src='http://graph.facebook.com/$pop_fbid/picture?type=large'>";
			}

			// Verficamos si existen archivos en esta discusion
			$result3 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$pop_id'");
			$count3 = $result3->num_rows;
			$result3->close();

			// Verificamos si existe el link o un archivo
			if($count3 > 0){
				$pop_link = "<div class='link'><div id='icon$pop_id' class='link_icon icon-archive-icon'></div></div>";
			} else if($pop_link != '' || $pop_link != null){
				$pop_link = "<div class='link'><div id='icon$pop_id' class='link_icon icon-link-icon'></div></div>";
			}

			// Guardamos la informacion
			$dis_pop .= "<div class='discucion dis_$pop_id' onclick='javascript:dis_mostrar($pop_id)'>
				$pop_link
				<p class='d_title'>$pop_titulo</p>
				<p class='d_subtitle'>
					$pop_img $pop_nombre: 
					<span>".$fnc->FechaCOM($pop_fecha)."</span>
				</p>
				<div class='d_content'>".$pop_contenido."</div>
				<p class='d_footer'>Votos: <span class='dis_voto_$pop_id' style='float:none'>$pop_votos</span> <span class='dis_res_$pop_id'>Respuestas: $pop_respuestas</span></p>
			</div>";
		}
		$result->close();

		// Si no hay mas discusiones mostramos mensaje
		if($start > $count[0]){
			$error = 'No hay mas discusiones por cargar';	
		}
	} else {
		// Si no hay mas discusiones mostramos mensaje
		$error = 'No hay mas discusiones por cargar';
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Discusiones cargadas','dis'=>$dis_pop,'start'=>$start,'error'=>$error));
	exit();
}

// Cargamos mas discuciones propias
function dis_propia($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$start = $fnc->secure_sql($_POST['start']) + 10; // Control de carga
	$limit = 10; // Numero de discusiones a cargar
	$dis_propia = "";
	$user = $_SESSION['id']; // ID del usuario

	// Verificamos que si existan mas discuciones por cargar
	$result_temp = $db->mysqli_select("SELECT Count(id_discucion) FROM discucion WHERE id_curso='$id' AND autor='$user' ORDER BY fecha DESC");
	$count = $result_temp->fetch_row();
	$result_temp->close();

	// Consultamos si existen discusiones por cargar
	if($_POST['start'] <= $count[0]){
		// Obtenemos mas discuciones nuevas
		$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' AND autor='$user' ORDER BY fecha DESC LIMIT {$start}, {$limit}");
		while($row = $result->fetch_assoc()){
			$propia_titulo = $row['titulo'];
			$propia_autor = $row['autor'];
			$propia_contenido = $row['contenido'];
			$propia_fecha = $row['fecha'];
			$propia_votos = $row['votos'];
			$propia_respuestas = $row['respuestas'];
			$propia_id = $row['id_discucion'];
			$propia_link = $row['link'];

			// Damos formato al contenido
			$data = explode('<', $propia_contenido);
			$propia_contenido = substr($data[0], 0, 100);
			$propia_contenido .= " ...";
			$propia_contenido = ucfirst($propia_contenido);
			$propia_contenido = $fnc->code($propia_contenido);

			// obtenemos el nombre del usuario
			$result2 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$propia_autor' LIMIT 1");
			while($row2 = $result2->fetch_assoc()){
				$propia_nombre = ucfirst($row2['nombre']);
				$propia_fbid = (empty($row2['fbid'])) ? '' : $row2['fbid'];
				$propia_avatar = (empty($row2['avatar'])) ? '' : $row2['avatar'];
				$propia_img = '';
			}
			$result2->close();

			// Generamos avatar del autor de la discusion
			if(empty($propia_fbid)){
				// Si no tiene ID de facebook
				$propia_img = "<img src='/avatar/$propia_avatar'>";
			} else {
				// Si tiene ID de facebook
				$propia_img = "<img src='http://graph.facebook.com/$propia_fbid/picture?type=large'>";
			}

			// Verficamos si existen archivos en esta discusion
			$result3 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$propia_id'");
			$count3 = $result3->num_rows;
			$result3->close();

			// Verificamos si existe el link o un archivo
			if($count3 > 0){
				$propia_link = "<div class='link'><div id='icon$propia_id' class='link_icon icon-archive-icon'></div></div>";
			} else if($propia_link != '' || $propia_link != null){
				$propia_link = "<div class='link'><div id='icon$propia_id' class='link_icon icon-link-icon'></div></div>";
			}

			// Guardamos la informacion
			$dis_propia .= "<div class='discucion dis_$propia_id' onclick='javascript:dis_mostrar($propia_id)'>
				$propia_link
				<p class='d_title'>$propia_titulo</p>
				<p class='d_subtitle'>
					$propia_img $propia_nombre: 
					<span>".$fnc->FechaCOM($propia_fecha)."</span>
				</p>
				<div class='d_content'>".$propia_contenido."</div>
				<p class='d_footer'>Votos: <span class='dis_voto_$propia_id' style='float:none'>$propia_votos</span> <span class='dis_res_$propia_id'>Respuestas: $propia_respuestas</span></p>
			</div>";
		}
		$result->close();

		// Si no hay mas discusiones mostramos mensaje
		if($start > $count[0]){
			$error = 'No hay mas discusiones por cargar';	
		}
	} else {
		// Si no hay mas discusiones mostramos mensaje
		$error = 'No hay mas discusiones por cargar';
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Discusiones cargadas','dis'=>$dis_propia,'start'=>$start,'error'=>$error));
	exit();
}

// Voto en contra de una discucion
function dis_voto_no($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID discusion
	$user = $_SESSION['id']; // ID user

	// Obtenemos los votos de la publicacion
	$result = $db->mysqli_select("SELECT votos,autor FROM discucion WHERE id_discucion='$id'");
	while($row = $result->fetch_assoc()){
		$autor = $row['autor'];
		$votos = $row['votos'];
	}
	$result->close();

	// Verificamos que no sea el usuario que voto el autor de la discucion
	if($user != $autor){
		// Verificamos que el usuario no alla votado ya la discucion
		$result2 = $db->mysqli_select("SELECT Count(id_control) FROM control_dis WHERE id_discucion='$id' AND user='$user'");
		$count = $result2->fetch_row();
		$result2->close();

		if($count[0] == 0){
			// Restamos en uno el voto
			$votos = $votos - 1;

			// Actualizamos los votos de la discusion
			$update = $db->mysqli_action("UPDATE discucion SET votos='$votos' WHERE id_discucion='$id'");
			// Agregamos registro para que el usuario no vote 2 veces la discusion
			$insert = $db->mysqli_action("INSERT INTO control_dis (id_discucion,user) VALUES ('$id','$user')");

			echo json_encode(array('status'=>'Voto terminado','votos'=>$votos,'id'=>$id));
			exit();
		} else {
			echo json_encode(array('error'=>'No puede votar 2 veces la misma discusion'));
		}
	} else {
		echo json_encode(array('error'=>'No puede votar su propia discusion'));
		exit();
	}
}

// Voto a favor de una discucion
function dis_voto_si($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID discusion
	$user = $_SESSION['id']; // ID user

	// Obtenemos los votos de la publicacion
	$result = $db->mysqli_select("SELECT votos,autor FROM discucion WHERE id_discucion='$id'");
	while($row = $result->fetch_assoc()){
		$autor = $row['autor'];
		$votos = $row['votos'];
	}
	$result->close();

	// Verificamos que no sea el usuario que voto el autor de la discusion
	if($user != $autor){
		// Consultamos que el usuario no alla votado ya la discucion
		$result2 = $db->mysqli_select("SELECT Count(id_control) FROM control_dis WHERE id_discucion='$id' AND user='$user'");
		$count = $result2->fetch_row();
		$result2->close();

		// Verificamos que el usuario no alla votado ya la discucion
		if($count[0] == 0){
			// sumamos en uno el voto
			$votos = $votos + 1;

			// Obtenemos los puntos del autor de la discucion y sumamos 1
			$result3 = $db->mysqli_select("SELECT puntos FROM usuarios WHERE id='$autor'");
			while($row3 = $result3->fetch_assoc()){
				// Se otorgan 3 puntos por cada voto a favor
				$puntos = $row3['puntos'] + 3;
			}
			$result3->close();

			// Actualizamos los votos de la discucion
			$update = $db->mysqli_action("UPDATE discucion SET votos='$votos' WHERE id_discucion='$id'");
			// Insertamos el registro para que el usuario no vote dos veces
			$insert = $db->mysqli_action("INSERT INTO control_dis (id_discucion,user) VALUES ('$id','$user')");
			// Actualizamos los puntos del autor
			$update1 = $db->mysqli_action("UPDATE usuarios SET puntos='$puntos' WHERE id='$autor'");

			echo json_encode(array('status'=>'Voto terminado','votos'=>$votos,'id'=>$id));
			exit();
		} else {
			echo json_encode(array('error'=>'No puede votar 2 veces la misma discusion'));
		}
	} else {
		echo json_encode(array('error'=>'No puede votar su propia discusion'));
		exit();
	}
}

// Cargamos las ultimas 10 discuciones
function discucion_cargar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$autor = $_SESSION['id']; // ID usuario
	$limit = 10; // Limite de discusiones a cargar
	$start = 0;	// Control de carga
	$dis_nueva = "";
	$dis_pop = "";
	$dis_no = "";
	$dis_propia = "";

	// Obtenemos discuciones nuevas
	$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' ORDER BY fecha DESC LIMIT {$start},{$limit}");
	while($row = $result->fetch_assoc()){
		$nueva_titulo = $row['titulo'];
		$nueva_autor = $row['autor'];
		$nueva_contenido = $row['contenido'];
		$nueva_fecha = $row['fecha'];
		$nueva_votos = $row['votos'];
		$nueva_respuestas = $row['respuestas'];
		$nueva_id = $row['id_discucion'];
		$nueva_link = $row['link'];

		// Damos formato al contenido
		$data = explode('<', $nueva_contenido);
		$nueva_contenido = substr($data[0], 0, 100);
		$nueva_contenido .= " ...";
		$nueva_contenido = ucfirst($nueva_contenido);
		$nueva_contenido = $fnc->code($nueva_contenido);

		// obtenemos el nombre del usuario
		$result2 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$nueva_autor'");
		while($row2 = $result2->fetch_assoc()){
			$nueva_nombre = ucfirst($row2['nombre']);
			$nueva_fbid = (empty($row2['fbid'])) ? '' : $row2['fbid'];
			$nueva_avatar = (empty($row2['avatar'])) ? '' : $row2['avatar'];
			$nueva_img = '';
		}
		$result2->close();

		// Generamos avatar del autor de la discusion
		if(empty($nueva_fbid)){
			// Si no tiene ID de facebook
			$nueva_img = "<img src='/avatar/$nueva_avatar'>";
		} else {
			// Si tiene ID de facebook
			$nueva_img = "<img src='http://graph.facebook.com/$nueva_fbid/picture?type=large'>";
		}

		// Verficamos si existen archivos en esta discusion
		$result9 = $db->mysqli_select("SELECT id_file FROM files WHERE id_discucion='$nueva_id'");
		$count9 = $result9->num_rows;
		$result9->close();

		// Verificamos si existe el link o un archivo
		if($count9 > 0){
			$nueva_link = "<div class='link'><div id='icon$nueva_id' class='link_icon icon-archive-icon'></div></div>";
		} else if($nueva_link != '' || $nueva_link != null){
			$nueva_link = "<div class='link'><div id='icon$nueva_id' class='link_icon icon-link-icon'></div></div>";
		} else {
			$nueva_link = "<div class='link'><div id='icon$nueva_id' class='link_icon'></div></div>";
		}

		// Guardamos la informacion
		$dis_nueva .= "<div class='discucion dis_$nueva_id' onclick='javascript:dis_mostrar($nueva_id)'>
			$nueva_link
			<p class='d_title'>$nueva_titulo</p>
			<p class='d_subtitle'>
				$nueva_img $nueva_nombre: 
				<span>".$fnc->FechaCOM($nueva_fecha)."</span>
			</p>
			<div class='d_content'>".$nueva_contenido."</div>
			<p class='d_footer'>Votos: <span class='dis_voto_$nueva_id' style='float:none'>$nueva_votos</span> <span class='dis_res_$nueva_id'>Respuestas: $nueva_respuestas</span></p>
		</div>";
	}
	$result->close();

	// Si no hay discusiones nuevas mostramos avizo
	if(empty($dis_nueva)){
		$dis_nueva = 'No hay discusiones nuevas';
	}

	// Obtenemos discuciones populares
	$result3 = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' ORDER BY votos DESC, fecha DESC LIMIT {$start}, {$limit}");
	while($row3 = $result3->fetch_assoc()){
		$pop_titulo = $row3['titulo'];
		$pop_autor = $row3['autor'];
		$pop_contenido = $row3['contenido'];
		$pop_fecha = $row3['fecha'];
		$pop_votos = $row3['votos'];
		$pop_respuestas = $row3['respuestas'];
		$pop_id = $row3['id_discucion'];
		$pop_link = $row3['link'];

		// Damos formato al contenido
		$data = explode('<', $pop_contenido);
		$pop_contenido = substr($data[0], 0, 100);
		$pop_contenido .= " ...";
		$pop_contenido = ucfirst($pop_contenido);
		$pop_contenido = $fnc->code($pop_contenido);

		// obtenemos el nombre del usuario
		$result4 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$pop_autor'");
		while($row4 = $result4->fetch_assoc()){
			$pop_nombre = ucfirst($row4['nombre']);
			$pop_fbid = (empty($row4['fbid'])) ? '' : $row4['fbid'];
			$pop_avatar = (empty($row4['avatar'])) ? '' : $row4['avatar'];
			$pop_img = '';
		}
		$result4->close();

		// Generamos avatar del autor de la discusion
		if(empty($pop_fbid)){
			// Si no tiene ID de facebook
			$pop_img = "<img src='/avatar/$pop_avatar'>";
		} else {
			// Si tiene ID de facebook
			$pop_img = "<img src='http://graph.facebook.com/$pop_fbid/picture?type=large'>";
		}

		// Verficamos si existen archivos en esta discusion
		$result10 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$pop_id'");
		$count10 = $result10->num_rows;
		$result10->close();

		// Verificamos si existe el link o un archivo
		if($count10 > 0){
			$pop_link = "<div class='link'><div id='icon$pop_id' class='link_icon icon-archive-icon'></div></div>";
		} else if($pop_link != '' || $pop_link != null){
			$pop_link = "<div class='link'><div id='icon$pop_id' class='link_icon icon-link-icon'></div></div>";
		} else {
			$pop_link = "<div class='link'><div id='icon$pop_id' class='link_icon'></div></div>";
		}

		// Guardamos la informacion
		$dis_pop .= "<div class='discucion dis_$pop_id' onclick='javascript:dis_mostrar($pop_id)'>
			$pop_link
			<p class='d_title'>$pop_titulo</p>
			<p class='d_subtitle'>
				$pop_img $pop_nombre: 
				<span>".$fnc->FechaCOM($pop_fecha)."</span>
			</p>
			<div class='d_content'>".$pop_contenido."</div>
			<p class='d_footer'>Votos: <span class='dis_voto_$pop_id' style='float:none'>$pop_votos</span> <span class='dis_res_$pop_id'>Respuestas: $pop_respuestas</span></p>
		</div>";
	}
	$result3->close();

	// Si no hay discusiones por mostrar mostramos mensaje
	if(empty($dis_pop)){
		$dis_pop = 'No hay discusiones populares';
	}

	// Discuciones sin responder
	$result5 = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' AND respuestas='0' ORDER BY fecha DESC LIMIT {$start}, {$limit}");
	while($row5 = $result5->fetch_assoc()){
		$no_titulo = $row5['titulo'];
		$no_autor = $row5['autor'];
		$no_contenido = $row5['contenido'];
		$no_fecha = $row5['fecha'];
		$no_votos = $row5['votos'];
		$no_respuestas = $row5['respuestas'];
		$no_id = $row5['id_discucion'];
		$no_link = $row5['link'];

		// Damos formato al contenido
		$data = explode('<', $no_contenido);
		$no_contenido = substr($data[0], 0, 100);
		$no_contenido .= " ...";
		$no_contenido = ucfirst($no_contenido);
		$no_contenido = $fnc->code($no_contenido);

		// obtenemos el nombre del usuario
		$result6 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$no_autor'");
		while($row6 = $result6->fetch_assoc()){
			$no_nombre = ucfirst($row6['nombre']);
			$no_fbid = (empty($row6['fbid'])) ? '' : $row6['fbid'];
			$no_avatar = (empty($row6['avatar'])) ? '' : $row6['avatar'];
			$no_img = '';
		}
		$result6->close();

		// Generamos avatar del autor de la discusion
		if(empty($no_fbid)){
			// Si no tiene ID de facebook
			$no_img = "<img src='/avatar/$no_avatar'>";
		} else {
			// Si tiene ID de facebook
			$no_img = "<img src='http://graph.facebook.com/$no_fbid/picture?type=large'>";
		}

		// Verficamos si existen archivos en esta discusion
		$result11 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$no_id'");
		$count11 = $result11->num_rows;
		$result11->close();

		// Verificamos si existe el link o un archivo
		if($count11 > 0){
			$no_link = "<div class='link'><div id='icon$no_id' class='link_icon icon-archive-icon'></div></div>";
		} else if($no_link != '' || $no_link != null){
			$no_link = "<div class='link'><div id='icon$no_id' class='link_icon icon-link-icon'></div></div>";
		} else {
			$no_link = "<div class='link'><div id='icon$no_id' class='link_icon'></div></div>";
		}

		// Guardamos la informacion
		$dis_no .= "<div class='discucion dis_$no_id' onclick='javascript:dis_mostrar($no_id)'>
			$no_link
			<p class='d_title'>$no_titulo</p>
			<p class='d_subtitle'>
				$no_img $no_nombre: 
				<span>".$fnc->FechaCOM($no_fecha)."</span>
			</p>
			<div class='d_content'>".$no_contenido."</div>
			<p class='d_footer'>Votos: <span class='dis_voto_$no_id' style='float:none'>$no_votos</span> <span class='dis_res_$no_id'>Respuestas: $no_respuestas</span></p>
		</div>";
	}
	$result5->close();

	// Si no hay discusiones mostramos mensaje
	if(empty($dis_no)){
		$dis_no = 'No hay discusiones sin responder';
	}

	// Discuciones propias
	$result7 = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' AND autor='$autor' ORDER BY fecha DESC LIMIT {$start}, {$limit}");
	while($row7 = $result7->fetch_assoc()){
		$propia_titulo = $row7['titulo'];
		$propia_autor = $row7['autor'];
		$propia_contenido = $row7['contenido'];
		$propia_fecha = $row7['fecha'];
		$propia_votos = $row7['votos'];
		$propia_respuestas = $row7['respuestas'];
		$propia_id = $row7['id_discucion'];
		$propia_link = $row7['link'];

		// Damos formato al contenido
		$data = explode('<', $propia_contenido);
		$propia_contenido = substr($data[0], 0, 100);
		$propia_contenido .= " ...";
		$propia_contenido = ucfirst($propia_contenido);
		$propia_contenido = $fnc->code($propia_contenido);

		// obtenemos el nombre del usuario
		$result8 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$propia_autor'");
		while($row8 = $result8->fetch_assoc()){
			$propia_nombre = ucfirst($row8['nombre']);
			$propia_fbid = (empty($row8['fbid'])) ? '' : $row8['fbid'];
			$propia_avatar = (empty($row8['avatar'])) ? '' : $row8['avatar'];
			$propia_img = '';
		}
		$result8->close();

		// Generamos avatar del autor de la discusion
		if(empty($propia_fbid)){
			// Si no tiene ID de facebook
			$propia_img = "<img src='/avatar/$propia_avatar'>";
		} else {
			// Si tiene ID de facebook
			$propia_img = "<img src='http://graph.facebook.com/$propia_fbid/picture?type=large'>";
		}

		// Verficamos si existen archivos en esta discusion
		$result12 = $db->mysqli_select("SELECT * FROM files WHERE id_discucion='$propia_id'");
		$count12 = $result12->num_rows;
		$result12->close();

		// Verificamos si existe el link o un archivo
		if($count12 > 0){
			$propia_link = "<div class='link'><div id='icon$propia_id' class='link_icon icon-archive-icon'></div></div>";
		} else if($propia_link != '' || $propia_link != null){
			$propia_link = "<div class='link'><div id='icon$propia_id' class='link_icon icon-link-icon'></div></div>";
		} else {
			$propia_link = "<div class='link'><div id='icon$propia_id' class='link_icon'></div></div>";
		}

		// Guardamos la informacion
		$dis_propia .= "<div class='discucion dis_$propia_id' onclick='javascript:dis_mostrar($propia_id)'>
			$propia_link
			<p class='d_title'>$propia_titulo</p>
			<p class='d_subtitle'>
				$propia_img $propia_nombre: 
				<span>".$fnc->FechaCOM($propia_fecha)."</span>
			</p>
			<div class='d_content'>".$propia_contenido."</div>
			<p class='d_footer'>Votos: <span class='dis_voto_$propia_id' style='float:none'>$propia_votos</span> <span class='dis_res_$propia_id'>Respuestas: $propia_respuestas</span></p>
		</div>";
	}
	$result7->close();

	// Si no hay discusiones mostramos mensaje
	if(empty($dis_propia)){
		$dis_propia = 'No hay discusiones propias';
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Discusiones cargadas','dis_nueva'=>$dis_nueva,'dis_pop'=>$dis_pop,'dis_no'=>$dis_no,'dis_propia'=>$dis_propia));
	exit();
}

// Creamos una discusion
function discucion_publicar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID curso
	$contenido = $_POST['contenido']; // Contenido
	$link = $fnc->secure_sql($_POST['link']); // Enlace
	$control = $fnc->secure_sql($_POST['control']); // Control de archivos
	$autor = $_SESSION['id']; // ID user
	$dis = '';

	// Obtenemos los valores reales habiendo pasado el filtro mod_security
	$contenido = $fnc->html_replace($contenido);

	// Verificamos el link
	if(!empty($link)){
		$data = substr($link, 0, 4);
		if($data != 'http'){
			$link = 'http://'.$link;
		}
	}

	// Obtenemos el titulo de la discusion
	$data = explode('<', $contenido);
	$titulo = substr($data[0], 0, 30);
	$titulo .= " ...";
	$titulo = ucfirst($titulo);
	$titulo = $fnc->code($titulo);

	// Obtenemos los datos del usuario
	$result = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$autor'");
	while($row = $result->fetch_assoc()){
		$nombre = ucwords($row['nombre']);
		$fbid = (empty($row['fbid'])) ? '' : $row['fbid'];
		$vatar = (empty($row['avatar'])) ? '' : $row['avatar'];
		$img = '';
	}
	$result->close();

	// Generamos avatar del autor de la discusion
	if(empty($fbid)){
		// Si no tiene ID de facebook
		$img = "<img src='/avatar/$avatar'>";
	} else {
		// Si tiene ID de facebook
		$img = "<img src='http://graph.facebook.com/$fbid/picture?type=large'>";
	}

	// Guardamos la discucion en la base de datos
	$insert = $db->mysqli_action("INSERT INTO discucion (id_curso,titulo,contenido,autor,link,fecha) VALUES ('$id','$titulo','$contenido','$autor','$link', NOW())");

	// Obtenemos el id de la discucion
	$result1 = $db->mysqli_select("SELECT fecha,contenido,link FROM discucion WHERE id_discucion='$insert' LIMIT 1");
	while($row1 = $result1->fetch_assoc()){
		$id_discucion = $insert;
		$fecha = $row1['fecha'];
		$content = $row1['contenido'];
		$dis_link = $row1['link'];
	}
	$result1->close();

	// Damos formato al contenido
	$data = explode('<', $content);
	$content = substr($data[0], 0, 100);
	$content .= " ...";
	$content = ucfirst($content);
	$content = $fnc->code($content);

	// Obtenemos si existen archivos en esta discusion
	$result2 = $db->mysqli_select("SELECT * FROM files_temp WHERE control='$control' AND user='$autor'");
	$count = $result2->num_rows;

	// Verificamos si existen archivos en esta discusion
	if($count > 0){
		// Volcamos los datos de una DB a otra DB
		while($row2 = $result2->fetch_assoc()){
			$f_name = $row2['name'];
			$f_size = $row2['size'];
			$f_ext = $row2['ext'];
			$f_contenido = $row2['contenido'];
			$f_type = $row2['type'];

			// Guardamos en la nueva base de datos
			$insert1 = $db->mysqli_action("INSERT INTO files (id_discucion,name,size,ext,contenido,type,fecha,user) VALUES ('$insert','$f_name','$f_size','$f_ext','$f_contenido','$f_type',NOW(),'$autor')");
		}
		$result2->close();

		// Eliminamos la base de datos temp
		$delete = $db->mysqli_action("DELETE FROM files_temp WHERE user='$autor'");
	}

	// Verificamos si existe el link o un archivo
	if($count > 0){
		$dis_link = "<div class='link'><div id='icon$id_discucion' class='link_icon icon-archive-icon'></div></div>";
	} else if($dis_link != '' || $dis_link != null){
		$dis_link = "<div class='link'><div id='icon$id_discucion' class='link_icon icon-link-icon'></div></div>";
	} else {
		$dis_link = "<div class='link'><div id='icon$id_discucion' class='link_icon'></div></div>";
	}

	// Armamos la respuesta
	$dis .= "<div class='discucion dis_$id_discucion' onclick='javascript:dis_mostrar($id_discucion)'>
		$dis_link
		<p class='d_title'>$titulo</p>
		<p class='d_subtitle'>
			$img $nombre: 
			<span>".$fnc->FechaCOM($fecha)."</span>
		</p>
		<div class='d_content'>".$content."</div>
		<p class='d_footer'>Votos: <span class='dis_voto_$id_discucion' style='float:none'>0</span> <span class='dis_res_$id_discucion'> Respuestas: 0</span></p>
	</div>";

	// Regresamos respuesta
	echo json_encode(array('status'=>'La discucion se creo con exito','dis'=>$dis));
	exit();
}

// Editamos una discucion
function discucion_editar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID discusion
	$contenido = $_POST['contenido']; // Contenido
	$link = (empty($_POST['link'])) ? '' : $fnc->secure_sql($_POST['link']); // Enlace
	$dis = "";
	$dis_link = '';
	$files = "";

	// Obtenemos los valores reales habiendo pasado el filtro mod_security
	$contenido = $fnc->html_replace($contenido);

	// Verificamos el link
	if(!empty($link)){
		$data = substr($link, 0, 4);
		if($data != 'http'){
			$link = 'http://'.$link;
		}
	}

	// Obtenemos el titulo de la discusion
	$data = explode('<', $contenido);
	$titulo = substr($data[0], 0, 30);
	$titulo .= " ...";
	$titulo = ucfirst($titulo);
	$titulo = $fnc->code($titulo);

	// Obtenemos contenido resumido
	$data = explode('<', $contenido);
	$content = substr($data[0], 0, 100);
	$content .= " ...";
	$content = ucfirst($content);
	$content = $fnc->code($content);	

	// Guardamos la discucion en la base de datos
	$update = $db->mysqli_action("UPDATE discucion SET titulo='$titulo',contenido='$contenido',link='$link' WHERE id_discucion='$id'");

	// Damos formato al contenido
	$contenido_edit = $fnc->tema_replace($contenido);
	$contenido = $fnc->url_replace($fnc->mostrar_html($contenido));

	// Si existe un enlace
	if(!empty($link)){
		// Si existe lo agregamos al contenido
		$contenido .= "<br><br>
			<a class='dis_enlace icon-link' href='$link' title='Aporte' target='_blank'>Enlace</a><br><br>";
	}

	// Consultamos si existen archivos en esta discusion
	$result = $db->mysqli_select("SELECT id_file,name,ext,size FROM files WHERE id_discucion='$id'  ORDER BY name");
	$count = $result->num_rows;

	// Verficamos si existen archivos en esta discusion
	if($count > 0){
		// Si hay archivos los mostramos
		while($row = $result->fetch_assoc()){
			$f_id = $row['id_file'];
			$f_ext = $row['ext'];
			$f_name = $row['name'];
			$f_size = $row['size'];

			$files .= "<div class='files_dis icon-$f_ext' onclick='javascript:files_mostrar($f_id, \"file\")'>$f_name ($f_size Kb)</div>";
		}
		$result->close();
	}

	$control = '';
	// Verificamos si hay archivos
	if($count > 0){
		$control = 'file';
	} else {
		// Verificamos si existe el link
		if(!empty($link)){
			// Si existe agregamos un icono informativo
			$control = 'link';
		} else {
			$control = 'none';
		}
	}

	echo json_encode(array('status'=>'La discucion se edito con exito','contenido'=>$contenido,'contenido_edit'=>$contenido_edit,'dis_link'=>$dis_link,'id'=>$id,'titulo'=>$titulo,'content'=>$content,'files'=>$files,'control'=>$control));
	exit();
}

// Eliminamos una respuesta
function res_delete($fnc, $db)
{
	$id_dis = $fnc->secure_sql($_POST['id_dis']); // ID discusion
	$id_res = $fnc->secure_sql($_POST['id_res']); // ID respuesta

	// Eliminamos la respuesta
	$delete = $db->mysqli_action("DELETE FROM respuestas WHERE id_respuesta='$id_res'");

	// Obtenemos el total de respuestas de la discusion
	$result = $db->mysqli_select("SELECT respuestas FROM discucion WHERE id_discucion='$id_dis' LIMIT 1");
	while($row = $result->fetch_assoc()){
		$respuestas = $row['respuestas'] - 1;
	}
	$result->close();

	// Actualizamos el total de respuestas
	$update = $db->mysqli_action("UPDATE discucion SET respuestas='$respuestas' WHERE id_discucion='$id_dis'");

	// Regresamos informacion
	echo json_encode(array('status'=>'La discusion se elimino con exito','id_res'=>$id_res,'id_dis'=>$id_dis,'res'=>$respuestas));
	exit();
}

// Editamos una respuesta
function res_editar($fnc,$db)
{
	$contenido = $_POST['contenido']; // Contenido
	$id = $fnc->secure_sql($_POST['id']); // ID respuesta
	$id_dis = $fnc->secure_sql($_POST['id_dis']); // ID discusion

	// Obtenemos el filtro original
	$contenido = $fnc->html_replace($contenido);

	// Actualizamos la base de datos
	$update = $db->mysqli_action("UPDATE respuestas SET contenido='$contenido' WHERE id_respuesta='$id'");

	// Damos formato al contenido
	$contenido_edit = $fnc->tema_replace($contenido);
	$contenido_mostrar = $fnc->url_replace($fnc->mostrar_html($contenido));

	// Regresamos respuesta
	echo json_encode(array('status'=>'La respuesta se edito con exito','contenido_edit'=>$contenido_edit,'contenido_mostrar'=>$contenido_mostrar,'id'=>$id,'id_dis'=>$id_dis));
	exit();
}

// Creamos una respuesta
function res_publicar($fnc,$db)
{
	$id_curso = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$id_dis = $fnc->secure_sql($_POST['id_dis']);	// ID discusion
	$contenido = $_POST['contenido']; // Contenido
	$user = $_SESSION['id']; // ID user

	// Damos formato al codigo
	$contenido = $fnc->html_replace($contenido);

	// Obtenemos los datos del usuario
	$result = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$user'");
	while($row = $result->fetch_assoc()){
		$nombre = ucwords($row['nombre']);
		$fbid = (empty($row['fbid'])) ? '' : $row['fbid'];
		$avatar = (empty($row['avatar'])) ? '' : $row['avatar'];
		$img = '';
	}
	$result->close();

	// Generamos avatar para el usuario
	if(empty($fbid)){
		// Si no tiene ID de fb
		$img = "<img src='/avatar/$avatar'>";
	} else {
		// Si tiene ID de fb
		$img = "<img src='http://graph.facebook.com/$fbid/picture?type=large'>";
	}

	// Guardamos la respuesta en la base de datos
	$insert = $db->mysqli_action("INSERT INTO respuestas (id_discucion,id_curso,contenido,autor,fecha) VALUES ('$id_dis','$id_curso','$contenido','$user', NOW())");

	// Obtenemos el id de la discucion
	$result1 = $db->mysqli_select("SELECT fecha,contenido FROM respuestas WHERE id_respuesta='$insert' LIMIT 1");
	while($row1 = $result1->fetch_assoc()){
		$id_respuesta = $insert;
		$fecha = $row1['fecha'];
		$content = $row1['contenido'];
	}
	$result1->close();

	// Obtenemos el total de respuestas y sumamos 1
	$result2 = $db->mysqli_select("SELECT respuestas,titulo,autor FROM discucion WHERE id_discucion='$id_dis'");
	while($row2 = $result2->fetch_assoc()){
		$respuestas = $row2['respuestas'] + 1;
		$titulo = $row2['titulo'];
		$autor = $row2['autor'];
	}
	$result2->close();

	// Actualizamos el total de respuestas
	$update = $db->mysqli_action("UPDATE discucion SET respuestas='$respuestas' WHERE id_discucion='$id_dis'");

	$res = "<div class='respuestas' id='respuestas_$id_respuesta'>
		<p>$img $nombre: 
			<span>".$fnc->FechaCOM($fecha)."</p>
		<div class='d_content' id='res_content_$id_respuesta'>".$fnc->url_replace($fnc->mostrar_html($content))."</div>
		<p id='res_option_$id_respuesta'>
			<span style='float:none;' class='icon icon-edit' title='Editar respuesta' onclick='javascript:res_edit($id_respuesta, $id_dis)'></span>
			<span style='float:none;' class='icon icon-delete' title='Eliminar respuesta' onclick='javascript:res_delete($id_respuesta)'></span>
			<span class='icon icon-voto-down' title='Mal aporte' onclick='javascript:res_voto_no($id_respuesta)'></span>
			<span title='votos' class='res_voto_$id_respuesta'>0</span>
			<span class='icon icon-voto-up' title='Buen aporte' onclick='javascript:res_voto_si($id_respuesta)'></span>
			&nbsp;
		</p>
		<p class='r_footer' id='res_delete_$id_respuesta' style='display:none;'>
			Esta seguro de eliminar la respuesta? <a onclick='javascript:res_delete_yes($id_respuesta, $id_dis)' style='margin-right:15px;'>SI</a><a onclick='javascript:res_delete_no($id_respuesta)'>NO</a>
		</p>
	</div>";

	$res .= "<div id='res_form_$id_respuesta' class='res_form_edit' style='display:none;'>
		<form id='form_res_edit'>
			<div id='resp_toolbox' style='display:block;'>
				<span onclick='javascript:toolbox(1,\"content_res_edit_$id_respuesta\")' class='icon-code' title='Insertar codigo' style='float:none;'>CODE</span>
				<span onclick='javascript:toolbox(2,\"content_res_edit_$id_respuesta\")' class='icon-bold' title='Negrita' style='float:none;'><strong></strong></span>
				<span onclick='javascript:toolbox(3,\"content_res_edit_$id_respuesta\")' class='icon-italic' title='Cursiva' style='float:none;'><i></i></span>
				<span onclick='javascript:toolbox(4,\"content_res_edit_$id_respuesta\")' class='icon-underline' title='Subrayado' style='float:none;'><u></u></span>
				<span onclick='javascript:toolbox(5,\"content_res_edit_$id_respuesta\")' class='icon-strike' title='Tachado' style='float:none;'></span>
			</div>
			<textarea id='content_res_edit_$id_respuesta' class='content_res_edit' placeholder='Tienes alguna respuesta? publicalo aqui'>".$fnc->tema_replace($content)."</textarea>
		</form>
		<p>
			<button id='res_submit_edit' class='submit' onclick='javascript:res_editar($id_respuesta, $id_dis)' style='display:inline-block;'>Editar</button>
			<button id='res_cancelar_edit' class='submit' onclick='javascript:res_edit_cancelar($id_respuesta)' style='display:inline-block;'>Cancelar</button>
			<span id='res_edit_info'></span>
		</p>
	</div>";

	// Verificamos que el autor de la respuesta no sea el autor de la discusion
	if($user != $autor){
		// Procesamos una notificacion al autor de la discusion
		$texto_notificacion = "$nombre comento tu discusión.- $titulo";
		// Guardamos la notificacion
		$insert = $db->mysqli_action("INSERT INTO notificacion (user,texto,id_curso,id_discucion,status,type,fecha) VALUES ('$autor','$texto_notificacion','$id_curso','$id_dis','NO','DIS',NOW())");
	}

	// Procesamos una notificacion a los que respondieron la discusion
	$result3 = $db->mysqli_select("SELECT DISTINCT autor FROM respuestas WHERE id_discucion='$id_dis'");
	while($row3 = $result3->fetch_assoc()){
		$res_autor = $row3['autor'];
		if($res_autor != $autor){
			if($res_autor != $user){
				$texto_notificacion = "$nombre comento la discusión.- $titulo";
				$insert1 = $db->mysqli_action("INSERT INTO notificacion (user,texto,id_curso,id_discucion,status,type,fecha) VALUES ('$res_autor','$texto_notificacion','$id_curso','$id_dis','DIS','NO',NOW())");
			}
		}
	}
	$result3->close();

	// Regresamos respuesta
	echo json_encode(array('status'=>'Su respuesta de guardo con exito','res'=>$res,'id'=>$id_dis,'respuestas'=>$respuestas));
	exit();
}

// Voto en contra de una respuesta
function res_voto_no($fnc, $db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID respuesta
	$user = $_SESSION['id']; // ID usuario

	// Obtenemos los votos de la respuesta
	$result = $db->mysqli_select("SELECT id_discucion,votos,autor FROM respuestas WHERE id_respuesta='$id'");
	while($row = $result->fetch_assoc()){
		$autor = $row['autor'];
		$votos = $row['votos'];
		$id_dis = $row['id_discucion'];
	}
	$result->close();

	// Verificamos que no sea el usuario que voto el autor de la respuesta
	if($user != $autor){
		// Consultamos que el usuario no alla votado ya la respuesta
		$result2 = $db->mysqli_select("SELECT Count(id_control) FROM control_res WHERE id_respuesta='$id' AND user='$user'");
		$count = $result2->fetch_row();
		$result2->close();

		// Verificamos que el usuario no alla votado ya la respuesta
		if($count[0] == 0){
			// Restamos en uno el voto
			$votos = $votos - 1;

			// Actualizamos los votos de la respuesta
			$update = $db->mysqli_action("UPDATE respuestas SET votos='$votos' WHERE id_respuesta='$id'");
			// Actualizamos el registro para que el usuario no vote 2 veces la respuesta
			$insert = $db->mysqli_action("INSERT INTO control_res (id_respuesta,user) VALUES ('$id','$user')");

			echo json_encode(array('status'=>'Voto terminado','votos'=>$votos,'id'=>$id,'id_dis'=>$id_dis));
			exit();
		} else {
			echo json_encode(array('error'=>'No puede votar 2 veces la misma respuesta'));
		}
	} else {
		echo json_encode(array('error'=>'No puede votar su propia respuesta'));
		exit();
	}
}

// Voto a favor de una discucion
function res_voto_si($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID respuesta
	$user = $_SESSION['id']; // ID usuario

	// Obtenemos los votos de la respuesta
	$result = $db->mysqli_select("SELECT id_discucion,votos,autor FROM respuestas WHERE id_respuesta='$id'");
	while($row = $result->fetch_assoc()){
		$autor = $row['autor'];
		$votos = $row['votos'];
		$id_dis = $row['id_discucion'];
	}
	$result->close();

	// Verificamos que no sea el usuario que voto el autor de la respuesta
	if($user != $autor){
		// Consultamos que el usuario no alla votado ya la discucion
		$result2 = $db->mysqli_select("SELECT Count(id_control) FROM control_res WHERE id_respuesta='$id' AND user='$user'");
		$count = $result2->fetch_row();
		$result2->close();

		// Verificamos que el usuario no alla votado ya la discusion
		if($count[0] == 0){
			// sumamos 1 a los votos de la respuesta
			$votos = $votos + 1;

			// Obtenemos los puntos del autor de la respuesta y sumamos 3 puntos
			$result3 = $db->mysqli_select("SELECT puntos FROM usuarios WHERE id='$autor'");
			while($row3 = $result3->fetch_assoc()){
				$puntos = $row3['puntos'] + 3;
			}
			$result3->close();

			// Actualizamos los votos de la respuesta
			$update = $db->mysqli_action("UPDATE respuestas SET votos='$votos' WHERE id_respuesta='$id'");
			// Actualizamos el registro para que el usuario no vote dos veces la respuesta
			$insert = $db->mysqli_action("INSERT INTO control_res (id_respuesta,user) VALUES ('$id','$user')");
			// Actualizamos los puntos del autor de la respuesta
			$update1 = $db->mysqli_action("UPDATE usuarios SET puntos='$puntos' WHERE id='$autor'");

			echo json_encode(array('status'=>'Voto terminado','votos'=>$votos,'id'=>$id,'id_dis'=>$id_dis));
			exit();
		} else {
			echo json_encode(array('error'=>'No puede votar 2 veces la misma respuesta'));
		}
	} else {
		echo json_encode(array('error'=>'No puede votar su propia respuesta'));
		exit();
	}
}