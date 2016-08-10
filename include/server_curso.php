<?php
/************************************************
Servidor php para cargar información sobre cursos

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
require_once '../phpmailer/PHPMailerAutoload.php';
require_once 'Fnc.php';
require_once 'Db.php';
require_once 'Template.php';


$fnc = new Fnc();
$db = new Db();
$mail = new PHPMailer;
$template = new Template();

if(empty($_POST['type'])){ $type = '';} else { $type = addslashes($_POST['type']);}

// Router del server
switch($type){
	case 'buscador': // OK
		buscador($fnc,$db);
		break;
	case 'curso_cargar': // OK
		curso_cargar($fnc,$db);
		break;
	case 'nota_edicion': // OK
		nota_edicion($fnc,$db);
		break;
	case 'nota_delete': // OK
		nota_delete($fnc,$db);
		break;
	case 'nota_publicar': // OK
		nota_publicar($fnc,$db);
		break;
	case 'nota_ver': // OK
		nota_ver($fnc,$db);
		break;
	case 'notas': // OK
		notas($fnc,$db);
		break;
	case 'notificacion_cargar': // OK
		notificacion_cargar($fnc,$db);
		break;
	case 'notificacion_leida': // OK
		notificacion_leida($fnc,$db);
		break;
	case 'notificacion_mostrar': // OK
		notificacion_mostrar($fnc,$db);
		break;
	case 'tema_cargar': // OK
		tema_cargar($fnc,$db);
		break;
	case 'user_cargar': // OK
		user_cargar($fnc,$db);
		break;
	case 'user_mensaje': // OK
		user_mensaje($fnc,$db,$mail,$data_email,$template);
		break;
	case 'user_mostrar': // OK
		user_mostrar($fnc,$db);
		break;
}

// Buscamos en las discuciones
function buscador($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id_curso']); // ID curso
	$q = $fnc->secure_sql($_POST['q']); // valor a buscar
	$find = '';
	$bus_link = '';

	// Obtenemos los resultados de la busqueda en las discuciones
	$result = $db->mysqli_select("SELECT * FROM discucion WHERE id_curso='$id' AND contenido LIKE '%$q%' LIMIT 10");
	$count = $result->num_rows;

	// Verificamos si hay resultados
	if($count > 0){
		// Si hay obtenemos los resultados
		while($row = $result->fetch_assoc()){
			$titulo = $row['titulo'];
			$id_discucion = $row['id_discucion'];
			$votos = $row['votos'];
			$respuestas = $row['respuestas'];
			$link = $row['link'];

			// Verificamos si la discusion tiene archivos
			$result2 = $db->mysqli_select("SELECT Count(id_file) FROM files WHERE id_discucion='$id_discucion'");
			$count2 = $result2->fetch_row();
			$result2->close();

			// Verificamos si exsten archivos
			if($count2[0] > 0){
				// Si existe lo mostramos
				$bus_link = "<div class='link'><div class='link_icon icon-archive-icon'></div></div>";
			} else {
				// Verificamos si existe el link
				if(!empty($link)){
					// Si existe lo mostramos
					$bus_link = "<div class='link'><div class='link_icon icon-link-icon'></div></div>";
				}
			}

			// Armamos la respuesta
			$find .= "<div class='resultados' onclick='javascript:dis_mostrar($id_discucion)'>
				$bus_link
				<p class='d_subtitle'>$titulo</p>
				<p class='d_footer'>Votos: $votos <span>Respuestas: $respuestas</span></p>
			</div>";
		}
		$result->close();
	} else {
		// Si no hay resultados damos avizo
		$find .= "No hay resultados con la busqueda \"$q\"";
	}

	// Regresamos respuesta
	echo json_encode(array('status'=>'Busqueda terminada','find'=>$find));
	exit();
}

// Cargamos el curso al principio
function curso_cargar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID del curso
	$user = (empty($_SESSION['id'])) ? $fnc->secure_sql($_POST['id_user']) : $_SESSION['id']; // ID del usuario
	$curso = '';

	// Obtenemos los datos del usuario
	// Verificamos que el usuario exista
	$result6 = $db->mysqli_select("SELECT id,fbid,email,nombre,username,nivel_user,avatar FROM usuarios WHERE id='$user' LIMIT 1");
	$count6 = $result6->num_rows;

	if($count6 > 0){
		// Si existe
		while($row6 = $result6->fetch_assoc()){
			$email = (empty($row6['email'])) ? '' : $row6['email'];
			$_SESSION['logged_in'] = true;
			$_SESSION['logged_fb'] = false;
			$_SESSION['email'] = (empty($row6['email'])) ? '' : $row6['email'];
			$_SESSION['nombre'] = (empty($row6['nombre'])) ? '' : $row6['nombre'];
			$_SESSION['user_name'] = (empty($row6['username'])) ? '' : $row6['username'];
			$_SESSION['nivel'] = (empty($row6['nivel_user'])) ? '' : $row6['nivel_user'];
			$_SESSION['id'] = (empty($row6['id'])) ? '' : $row6['id'];
			$_SESSION['user_id'] = (empty($row6['fbid'])) ? '' : $row6['fbid'];
			$_SESSION['avatar'] = (empty($row6['avatar'])) ? '' : $row6['avatar'];
			$_SESSION['gender'] = 'male';
		}
		$result6->close();
	}

	// Comprobamos el avatar del usuario
	if(empty($_SESSION['user_id']) || $_SESSION['user_id'] == 0){
		$user_img = "/avatar/".$_SESSION['avatar'];
	} else {
		$user_img = "http://graph.facebook.com/".$_SESSION['user_id']."/picture?type=large";
	}

	$usuario_current = $_SESSION['nombre']; // Nombre del usuario

	// Consultamos que el usuario este suscrito al curso solicitado
	$res = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE user='$user' AND id_curso='$id'");
	$count_res = $res->fetch_row();
	$res->close();

	// Verificamos que el usuario este suscrito
	if($count_res[0] == 0){
		// Si no esta suscrito, lo suscribimos
		$insert = $db->mysqli_action("INSERT INTO suscripcion (id_curso,user,fecha) VALUES ('$id','$user',NOW())");
	}

	// Obtenemos los datos del curso seleccionado
	$result = $db->mysqli_select("SELECT titulo,subtitulo,autor,categoria FROM cursos WHERE id_curso='$id'");
	while($row = $result->fetch_assoc()){
		$curso_titulo = $row['titulo'];
		$curso_subtitulo = $row['subtitulo'];
		$autor = $row['autor']; // ID del autor del curso
		$curso_categoria = $row['categoria'];
	}
	$result->close();

	// Obtenemos el nombre del instructor
	$result2 = $db->mysqli_select("SELECT nombre,fbid FROM usuarios WHERE id='$autor'");
	while($row2 = $result2->fetch_assoc()){
		$curso_autor = ucwords($row2['nombre']);
		$curso_autor_id = (empty($row2['fbid'])) ? '' : $row2['fbid'];
	}
	$result2->close();

	// Agregamos el primer contenido
	$curso .= "<div id='w_header'>
		<h1>$curso_titulo</h1>
		<p id='w_subtitulo'>$curso_subtitulo</p>
		<p id='w_autor'>Instructor: <span onclick='javascript:user_mostrar(\"$autor\")'>$curso_autor</span></p>
	</div>
	<div id='w_content'>";

	// Orden para los capitulos
	$capitulo_orden = 1; // Empezamos con el capitulo 1

	// Obtenemos datos de los capitulos
	$result3 = $db->mysqli_select("SELECT titulo,id_capitulo FROM capitulos WHERE id_curso='$id' AND visibility='YES' ORDER BY orden");
	while($row3 = $result3->fetch_assoc()){
		$capitulo_id = $row3['id_capitulo'];
		$capitulo_titulo = $row3['titulo'];

		// Agregamos titulos de los capitulos
		$curso .= "<h2 class='icon-directorio'>$capitulo_orden: $capitulo_titulo</h2><div class='w_temas'>";
		// Aumentamos 1 al orden de los capitulos
		$capitulo_orden++;

		// Orden para los temas
		$tema_orden = 1; // Empezamos con 1

		// Obtenemos datos de los temas
		$result4 = $db->mysqli_select("SELECT titulo,video,id_tema FROM temas WHERE id_capitulo='$capitulo_id' AND id_curso='$id' AND visibility='YES' ORDER BY orden");
		while($row4 = $result4->fetch_assoc()){
			$tema_titulo = $row4['titulo'];
			$tema_id = $row4['id_tema'];
			$video = (empty($row4)) ? '' : $row4['video'];

			// Si no hay video cargamos imagen de practicas
			if(!empty($video)){
				$tema_video = "img/curso_video.jpg";
			} else {
				$tema_video = "/img/curso_normal.jpg";
			}

			// Agregamos informacion de los temas
			$curso .= "<div class='w_tema' onclick='javascript:mostrar($tema_id)'>
				<img class='img1' src='$tema_video'>
				<p>$tema_orden: $tema_titulo<br>
				<span>Clic para ver el tema</span></p></div>";

			// Aumentamos 1 al orden de los temas
			$tema_orden++;
		}
		$result4->close();

		// Cerramos div w_temas
		$curso .= "</div>";
	}
	$result3->close();

	// Agregamos la informacion de los twitts y cerramos el div de contenido
	$curso .= '<div class="twitter_container" id="twitter_title">
			<div class="twitterContainer" id="twitter_contenedor">
			</div>
		</div>
	</div>';

	// Informacion sobre la documentacion
	$doc = "<p>Bienvenido $usuario_current:</p>
			<p>$curso_titulo</p>
			<br>
			<p>En esta sección aparecerá documentación de apoyo en el momento en que de clic en algún tema de los situados en el lado izquierdo de esta pantalla, intente acceder a alguno...</p><br>
            <p>Si tiene dudas puede utilizar el sistema de discusiones para realizar preguntas sobre los temas vistos en el curso, realizar aportes (compartir enlaces y codigo), intercambiar ideas y ayudar a sus compañeros del curso.</p><br>
			<p>Recuerde, si alguna discusión le fue de ayuda, dele un voto positivo para hacerla mas relevante y asi más usuarios puedan tener acceso a ésta, en caso contrario puede darle un voto negativo para dejarla en el rezago.</p>";

	// Informacion sobre el sistema de discusiones
	$dis = "
	<div id='discucion_form'>
		<form id='form_discucion'>
			<div id='resp_toolbox'>
				<span onclick='javascript:toolbox(1,\"content_dis\")' class='icon-code' title='Insertar codigo'>CODE</span>
				<span onclick='javascript:toolbox(2,\"content_dis\")' class='icon-bold' title='Negrita'><strong></strong></span>
				<span onclick='javascript:toolbox(3,\"content_dis\")' class='icon-italic' title='Cursiva'><i></i></span>
				<span onclick='javascript:toolbox(4,\"content_dis\")' class='icon-underline' title='Subrayado'><u></u></span>
				<span onclick='javascript:toolbox(5,\"content_dis\")' class='icon-strike' title='Tachado'></span>
			</div>
			<textarea id='content_dis' placeholder='Tienes alguna duda o aporte? publicalo aqui' onclick='javascript:mostrar_discucion()'></textarea>
			<input type='text' id='link' placeholder='http://'>
		</form>
		<div id='content_dis_files'>Arrastre aqui sus archivos<br>(Tamaño maximo 10 Kb.)</div>
		<div id='content_dis_results'></div>
		<div id='content_dis_cargando'></div>
		<p>
			<button id='submit' onclick='javascript:dis_publicar()'>Publicar</button>
			<button class='submit' onclick='javascript:dis_enlace()'>Link</button>
			<span id='form_info'></span>
		</p>
	</div>
	<div>
		<ul id='discucion_controls'>
			<li class='controls' data-type='1' onclick='javascript:dis_router(1)'>Nuevas</li><!--
			--><li class='controls' data-type='2' onclick='javascript:dis_router(2)'>Populares</li><!--
			--><li class='controls' data-type='3' onclick='javascript:dis_router(3)'>No respondidas</li><!--
			--><li class='controls' data-type='4' onclick='javascript:dis_router(4)'>Propias</li>
		</ul>
	</div>
	<div id='discucion_mostrar'>";

	// Informacion sobre el sistema de busquedas
	$find = "<div id='form_buscador'>
		<input type='text' id='q' placeholder='Buscar en las discusiones' onkeyup='javascript:buscador(event)' autocomplete='off'>
	</div>
	<div id='buscador_cargando'><img src='/img/cargando.gif'></div>
	<div id='buscador_respuesta'>";

	// Obtenemos el numero de notificaciones del usuario
	$result5 = $db->mysqli_select("SELECT texto,fecha,id_discucion,id_notificacion,type FROM notificacion WHERE (user='$user') AND (id_curso='$id') AND (status='NO')");
	$count_notificacion = $result5->num_rows;
	$result5->close();

	// Convertimos en mayuscula la primer letra de la categoria del curso
	$categoria = ucfirst($curso_categoria);

	// Regresamos informacion
	echo json_encode(array('status'=>'Se cargo correctamente el curso','curso'=>$curso,'doc'=>$doc,'dis'=>$dis,'find'=>$find,'not_num'=>$count_notificacion,'titulo'=>$curso_titulo,'categoria'=>$categoria,"user"=>$user_img,"id_user"=>$_SESSION['id']));
	exit();
}

// Editamos una nota
function nota_edicion($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID nota
	$contenido = $_POST['contenido']; // Contenido

	// Formateamos el contenido
	$contenido = $fnc->html_replace($contenido);

	// Actualizamos los datos en la DB
	$update = $db->mysqli_action("UPDATE notas SET nota='$contenido' WHERE id_notas='$id'");

	// Obtenemos los datos de la nota guardada
	$result = $db->mysqli_select("SELECT nota,fecha FROM notas WHERE id_notas='$id'");
	while($row = $result->fetch_assoc()){
		$contenido = $row['nota'];
		$id_nota = $id;
		$fecha = $row['fecha'];
	}
	$result->close();

	// Damos formato al titulo
	$data = explode('<', $contenido);
	$titulo = substr($data[0], 0, 30);
	$titulo .= " ...";
	$titulo = ucfirst($titulo);
	$titulo = $fnc->code($titulo);

	// Damos formato al contenido
	$data = explode('<', $contenido);
	$content = substr($data[0], 0, 100);
	$content .= " ...";
	$content = ucfirst($content);
	$content = $fnc->code($content);

	// Damos formato al contenido
	$contenido_edit = $fnc->tema_replace($contenido);
	$contenido = $fnc->url_replace($fnc->mostrar_html($contenido));

	// Regresamos respuesta
	echo json_encode(array('status'=>'Nota editada','id'=>$id,'titulo'=>$titulo,'content'=>$content,'contenido'=>$contenido,'contenido_edit'=>$contenido_edit));
	exit();
}

// Eliminamos una nota
function nota_delete($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID nota

	// Eliminamos la nota
	$delete = $db->mysqli_action("DELETE FROM notas WHERE id_notas='$id'");

	// Regresamos respuesta
	echo json_encode(array('status'=>'Nota eliminada','id'=>$id));
	exit();
}

// Publicamos una nota
function nota_publicar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID curso
	$contenido = $_POST['contenido']; // Contenido
	$user = $_SESSION['id']; // ID user

	// Formateamos el contenido
	$contenido = $fnc->html_replace($contenido);

	// Guardamos en la base de datos
	$insert = $db->mysqli_action("INSERT INTO notas (id_curso,nota,user,fecha) VALUES ('$id','$contenido','$user',NOW())");

	// Obtenemos los datos de la nota guardada
	$result = $db->mysqli_select("SELECT nota,fecha FROM notas WHERE id_notas='$insert' LIMIT 1");
	while($row = $result->fetch_assoc()){
		$contenido = $row['nota'];
		$id_nota = $insert;
		$fecha = $row['fecha'];
	}
	$result->close();

	// Damos formato al titulo
	$data = explode('<', $contenido);
	$titulo = substr($data[0], 0, 30);
	$titulo .= " ...";
	$titulo = ucfirst($titulo);
	$titulo = $fnc->code($titulo);

	// Damos formato al contenido
	$data = explode('<', $contenido);
	$contenido = substr($data[0], 0, 100);
	$contenido .= " ...";
	$contenido = ucfirst($contenido);
	$contenido = $fnc->code($contenido);

	// Armamos la respuesta
	$nota = "<div id='nota_$id_nota' class='notas' onclick='javascript:nota_ver($id_nota)'>
		<p><span class='n_title' style='float:none;'>$titulo</span><span>".$fnc->FechaCOM($fecha)."</span></p>
		<p class='d_content'>$contenido</p>
	</div>";

	// Regresamos respuesta
	echo json_encode(array('status'=>'Nota publicada','nota'=>$nota));
	exit();
}

// Visualizamos una nota
function nota_ver($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID nota
	$user = $_SESSION['id']; // ID usuario

	// Obtenemos los datos de la nota guardada
	$result = $db->mysqli_select("SELECT nota,id_notas,fecha FROM notas WHERE id_notas='$id' LIMIT 1");
	while($row = $result->fetch_assoc()){
		$contenido = $row['nota'];
		$id_nota = $row['id_notas'];
		$fecha = $row['fecha'];
	}
	$result->close();

	// Damos formato al titulo
	$data = explode('<', $contenido);
	$titulo = substr($data[0], 0, 30);
	$titulo .= " ...";
	$titulo = ucfirst($titulo);
	$titulo = $fnc->code($titulo);

	// Damos formato al contenido
	$contenido = $fnc->mostrar_html($contenido);

	// Armamos la respuesta
	$nota = "<div class='notas'>
		<p><span class='n_title n_title_$id' style='float:none;'>$titulo</span><span>".$fnc->FechaCOM($fecha)."</span></p>
		<div id='nota_content_$id_nota' class='d_content'>".$fnc->url_replace($contenido)."</div>
		<div id='nota_edit_'$id_nota class='nota_edit'>
			<form id='form_nota_edit'>
				<div id='resp_toolbox' style='display:block;'>
					&nbsp;
					<span onclick='javascript:toolbox(1,\"nota_edicion\")' class='icon-code' title='Insertar codigo' style='float:none;'>CODE</span>
					<span onclick='javascript:toolbox(2,\"nota_edicion\")' class='icon-bold' title='Negrita' style='float:none;'></span>
					<span onclick='javascript:toolbox(3,\"nota_edicion\")' class='icon-italic' title='Cursiva' style='float:none;'></span>
					<span onclick='javascript:toolbox(4,\"nota_edicion\")' class='icon-underline' title='Subrayado' style='float:none;'></span>
					<span onclick='javascript:toolbox(5,\"nota_edicion\")' class='icon-strike' title='Tachado' style='float:none;'></span>
				</div>
				<textarea id='nota_edicion'>".$fnc->tema_replace($contenido)."</textarea>
			</form>
			<p>
				&nbsp;
				<button class='submit' onclick='javascript:nota_edicion($id_nota)' style='display:inline-block;'>Editar</button>
				<button class='submit' onclick='javascript:nota_cancelar($id_nota)' style='display:inline-block;'>Cancelar</button>
				<span id='form_nota_edit_info'></span>
			</p>
		</div>
		<p id='nota_options'>&nbsp;
			<span class='icon icon-delete' title='Eliminar nota' onclick='javascript:nota_delete()'></span>
			<span class='icon icon-edit' title='Editar nota' onclick='javascript:nota_edit($id_nota)'></span>
		</p>
		<p id='nota_delete'>
			Esta seguro de eliminar la nota <a onclick='javascript:nota_delete_yes($id_nota)'>SI</a><a onclick='javascript:nota_delete_no($id_nota)'>NO</a>
		</p>
	</div>";

	// Regresamos respuesta
	echo json_encode(array('status'=>'Nota cargada','nota'=>$nota));
	exit();
}

// Cargamos las notas del curso
function notas($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID curso
	$user = $_SESSION['id']; // ID usuario
	$nota = "";

	// Consultamos si existen notas en el curso
	$result = $db->mysqli_select("SELECT nota,id_notas,fecha FROM notas WHERE id_curso='$id' AND user='$user' ORDER BY fecha DESC");
	$count = $result->num_rows;

	// Verificamos si existen notas en el curso
	if($count > 0){
		// Si hay obtenemos las notas
		while($row = $result->fetch_assoc()){
			$contenido = $row['nota'];
			$id_nota = $row['id_notas'];
			$fecha = $row['fecha'];

			// Damos formato al titulo
			$data = explode('<', $contenido);
			$titulo = substr($data[0], 0, 30);
			$titulo .= " ...";
			$titulo = ucfirst($titulo);
			$titulo = $fnc->code($titulo);

			// Damos formato al contenido
			$data = explode('<', $contenido);
			$contenido = substr($data[0], 0, 100);
			$contenido .= " ...";
			$contenido = ucfirst($contenido);
			$contenido = $fnc->code($contenido);

			// Armamos la respuesta
			$nota .= "<div id='nota_$id_nota' class='notas' onclick='javascript:nota_ver($id_nota)'>
					<p><span class='n_title n_title_$id_nota' style='float:none;'>$titulo</span><span>".$fnc->FechaCOM($fecha)."</span></p>
					<p class='n_content n_content_$id_nota'>$contenido</p>
				</div>";
		}
		$result->close();
	} else {
		// Si no hay notas mostramos el mensaje
		$nota .= "No tiene notas en este curso";
	}

	// Armamos el contenido por default
	$default = "<div id='notas'>
		<form id='form_notas'>
			<div id='resp_toolbox'>
				<span onclick='javascript:toolbox(1,\"content_notas\")' class='icon-code' title='Insertar codigo'>CODE</span>
				<span onclick='javascript:toolbox(2,\"content_notas\")' class='icon-bold' title='Negrita'><strong></strong></span>
				<span onclick='javascript:toolbox(3,\"content_notas\")' class='icon-italic' title='Cursiva'><i></i></span>
				<span onclick='javascript:toolbox(4,\"content_notas\")' class='icon-underline' title='Subrayado'><u></u></span>
				<span onclick='javascript:toolbox(5,\"content_notas\")' class='icon-strike' title='Tachado'></span>
			</div>
			<textarea id='content_notas' placeholder='Escribe aqui tus apuntes sobre el curso ' onclick='javascript:mostrar_notas()'></textarea>
		</form>
		<p>
			<button id='submit' onclick='javascript:nota_publicar()'>Publicar</button>
			<span id='form_nota_info'></span>
		</p>
	</div>
	<div id='notas_append'>";

	// Regresamos respuesta
	echo json_encode(array('status'=>'Notas cargadas','nota'=>$nota,'defecto'=>$default));
	exit();
}

// Cargamos notificaciones leidas
function notificacion_cargar($fnc,$db)
{
	$start = $fnc->secure_sql($_POST['start']); // Desde donde empezar a cargar
	$id = $fnc->secure_sql($_POST['id']); // ID curso
	$limit = 10;
	$user = $_SESSION['id'];
	$not = "";

	// consultamos que si existan mas discuciones por cargar
	$result_tmp = $db->mysqli_select("SELECT Count(id_notificacion) FROM notificacion WHERE user='$user' AND id_curso='$id' AND status='YES'");
	$count = $result_tmp->fetch_row();
	$result_tmp->close();

	// Verificamos que si existan mas discuciones por cargar
	if($start <= $count[0]){
		// Obtenemos mas discuciones nuevas
		$result = $db->mysqli_select("SELECT id_notificacion,fecha,texto,id_discucion FROM notificacion WHERE user='$user' AND id_curso='$id' AND status='YES' ORDER BY fecha DESC LIMIT {$start},{$limit}");
		while($row = $result->fetch_assoc()){
			$not_id = $row['id_notificacion'];
			$not_fecha = $row['fecha'];
			$not_texto = $row['texto'];
			$not_id_dis = $row['id_discucion'];

			// Armamos la respuesta
			$not .= "<div class='notificacion' onclick='javascript:notificacion_ver($not_id,$not_id_dis)'>
				<p>$not_texto ".$fnc->FechaCOM($not_fecha)."</p>
			</div>";			
		}
		$result->close();

		if(($start + 10) > $count[0]){
			$error = 'No hay mas notificaciones por cargar';	
		}
	} else {
		// Si no hay mas notificaciones mostramos mensaje
		$error = 'No hay mas notificaciones por cargar';
	}

	// Sumamos 10 al control de carga de notificaciones
	$start = $start + 10;

	// Regresamos respuesta
	echo json_encode(array('status'=>'Notificaciones cargadas','not'=>$not,'start'=>$start,'error'=>$error));
	exit();
}

// Marcamos una notificacion como leida
function notificacion_leida($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID notificacion
	$leida = 'YES';

	// Consultamos si la notificacion ya fue leida
	$result = $db->mysqli_select("SELECT Count(id_notificacion) FROM notificacion WHERE id_notificacion='$id' AND status='NO'");
	$count = $result->fetch_row();
	$result->close();

	// Verificamos si la notificacion ya fue leida
	if($count[0] > 0){
		// Actualizamos la notificacion a leida
		$update = $db->mysqli_action("UPDATE notificacion SET status='YES' WHERE id_notificacion='$id'");

		$leida = 'NO';
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Notificacion leida','leida'=>$leida));
	exit();
}

// Cargamos las notificaciones nuevas cada vez que demos clic en alertas
function notificacion_mostrar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID del curso
	$user = $_SESSION['id']; // ID del usuario
	$not = '';

	// Consultamos si hay notificaciones
	$result = $db->mysqli_select("SELECT texto,fecha,id_discucion,id_notificacion,type FROM notificacion WHERE (user='$user') AND (id_curso='$id') AND (status='NO')");
	$count = $result->num_rows;

	// Verificamos si hay notificaciones nuevas
	if($count > 0){
		// Obtenemos las notificaciones
		while($row = $result->fetch_assoc()){
			$not_id = $row['id_notificacion'];
			$not_fecha = $row['fecha'];
			$not_texto = $row['texto'];
			$not_id_dis = $row['id_discucion'];
			$not_type = $row['type'];

			// Mostramos notificacion segun su tipo
			if($not_type == 'DIS'){
				$not .= "<div class='notificacion' onclick='javascript:notificacion_ver($not_id,$not_id_dis)'>
					<p>$not_texto ".$fnc->FechaCOM($not_fecha)." <span>nueva</span></p>
				</div>";
			} else {
				$not .= "<div class='notificacion' onclick='javascript:notificacion_perfil($not_id,$user)'>
					<p>$not_texto ".$fnc->FechaCOM($not_fecha)." <span>nueva</span></p>
				</div>";
			}
		}
	} else {
		// Si no hay notificaciones mostramos avizo
		$not .= "<div class='notificacion'>No tiene notificaciones nuevas</div>";
	}
	$result->close();

	// Obtenemos el titulo del curso
	$result2 = $db->mysqli_select("SELECT titulo FROM cursos WHERE id_curso='$id' LIMIT 1");
	while($row2 = $result2->fetch_assoc()){
		// Mostramos titulo segun el total de notificaciones
		if($count > 0){
			// Si hay notificaciones
			$titulo = '('.$count.') '.$row2['titulo'].' | Codeando.org';
		} else {
			// Si no hay notificaciones
			$titulo = $row2['titulo'].' | Codeando.org';
		}
	}
	$result2->close();

	// Regresamos informacion
	echo json_encode(array('status'=>'Se cargaron con exito las notificaciones','not'=>$not,'num'=>$count,'titulo'=>$titulo));
	exit();
}

// Mostramos el contenido de un tema
function tema_cargar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']); // ID tema
	$back = "<div class='w_back icon-left_back' onclick='javascript:back()'>Regresar</div>";
	$tema = "";

	// Agregamos informacion
	$tema .= "$back<div id='w_content_video'>";

	// Obtenemos datos del tema
	$result = $db->mysqli_select("SELECT * FROM temas WHERE id_tema='$id' LIMIT 1");
	while($row = $result->fetch_assoc()){
		$video = (!empty($row['video'])) ? $row['video'] : '';
		$titulo = (!empty($row['titulo'])) ? $row['titulo'] : '';
		$info = (!empty($row['info'])) ? $row['info'] : '';
		$id_curso = $row['id_curso'];
		$doc = (!empty($row['doc'])) ? $fnc->url_replace($row['doc']) : '';
		$github = (!empty($row['github'])) ? $row['github'] : '';

		// Si el tema tiene un video lo mostramos
		if(!empty($video)){
			$tema_video = "<div class='w_video'>
				<iframe width='560' height='315' src='//www.youtube.com/embed/$video?rel=0&showinfo=0&theme=light&iv_load_policy=3&color=white' frameborder='0' allowfullscreen></iframe>
			</div>";
		}

		// Mostramos html
		$doc = $fnc->mostrar_html($doc);
		$info = $fnc->mostrar_html($info);

		// Obtenemos el numero del tema
		$result2 = $db->mysqli_select("SELECT id_capitulo FROM capitulos WHERE id_curso='$id_curso' AND visibility='YES' ORDER BY orden");
		$ii = 1; // Para capitulos
		$orden_tema = 0;
		$orden_cap = 0;
		while($row2 = $result2->fetch_assoc()){
			$id_cap = $row2['id_capitulo'];
			$i = 1; // Para temas
			$result3 = $db->mysqli_select("SELECT id_tema FROM temas WHERE id_curso='$id_curso' AND id_capitulo='$id_cap' AND visibility='YES' ORDER BY orden");
			$count = $result2->num_rows;
			while($row3 = $result3->fetch_assoc()){
				if($id == $row3['id_tema']){
					$orden_tema = $i;
					$orden_cap = $ii;
				}
				$i++;
			}
			$result3->close();
			$ii++;
		}

		// Agregamos informacion
		$tema .= "<h2><span>Capitulo $orden_cap - Tema $orden_tema</span><br>$titulo</h2>";

		// Si hay video lo agregamos
		if(!empty($video)){
			$tema .= "$tema_video</div>";
		} else {
			$tema .= "</div>";
		}

		// Si tenemos informacion o enlace a github agregamos un div
		if(!empty($info) || !empty($github)){
			$tema .= "<div id='w_content'>";
		}

		// Si tenemos informacion la agregamos
		if(!empty($info)){
			$tema .= "<p class='w_p'>$info</p>";
		}

		// Agregamos repositorio
		if(!empty($github)){
			$tema .= "<a href='$github' id='github_a' target='_blank'>
				<div id='github' class='icon-github'>
					Accede al repositorio del tema en github
				</div>
				</a>";
		}
	}
	$result->close();

	// Agregamos cierre del div
	if(!empty($info) || !empty($github)){
		$tema .= "</div>$back";
	} else {
		$tema .= "$back";
	}

	// Si tenemos documentacion la mostramos
	if(!empty($doc)){
		$doc = "<p><strong>Documentación: $titulo</strong></p><br>$doc";
	}

	// Creamos la url para el tema
	$url = strtolower($titulo);
	$code = array(' ',',','á','é','í','ó','ú','|','?','!','¡');
	$replace = array('-','','a','e','i','o','u','','','','');
	for($i = 0; $i < count($code); $i++){
		$url = str_replace($code[$i], $replace[$i], $url);
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Tema cargado','tema'=>$tema,'doc'=>$doc,'id'=>$id,'url'=>$url,'titulo'=>$titulo));
	exit();
}

// Mostramos mas mensajes de perfil de usuario
function user_cargar($fnc,$db)
{
	$user = $fnc->secure_sql($_POST['user']); // ID del usuario
	$start = $fnc->secure_sql($_POST['start']) + 10; // Numero desde el que se comenzara a cargar los comentarios
	$limit = 10; // Limite de comentarios a cargar
	$resultado = '';

	// Consultamos que si existan mas mensajes por cargar
	$result_temp = $db->mysqli_select("SELECT Count(id_mensaje) FROM mensajes WHERE user='$user' ORDER BY fecha DESC");
	$count = $result_temp->fetch_row();
	$result_temp->close();

	// Verificamos que si existan mas mensajes por cargar
	if($start <= $count[0]){
		// Si existen
		// Obtenemos los siguientes 10 mensajes
		$result = $db->mysqli_select("SELECT mensaje,fecha,visitante FROM mensajes WHERE user='$user' ORDER BY fecha DESC LIMIT {$start}, {$limit}");
		while($row = $result->fetch_assoc()){
			$mensaje = $row['mensaje'];
			$fecha = $row['fecha'];
			$visitante = $row['visitante'];

			// Obtenemos el nombre del visitante
			$result1 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$visitante' LIMIT 1");
			while($row1 = $result1->fetch_assoc()){
				$visitante_nombre = $row1['nombre'];
				$visitante_fbid = (empty($row1['fbid'])) ? '' : $row1['fbid'];
				$visitante_avatar = (empty($row1['avatar'])) ? '' : $row1['avatar'];
				$visitante_img = '';
			}
			$result1->close();

			// Configuramos imagen del visitante
			if(empty($visitante_img)){
				$visitante_img = "<img src='/avatar/$visitante_avatar' alt='Avatar' title='$visitante_nombre'>";
			} else {
				$visitante_img = "<img src='http://graph.facebook.com/$visitante_fbid/picture?type=large' alt='avatar'>' alt='Avatar' title='$visitante_nombre'>";
			}

			$resultado .= "<div class='u_mensajes'>
				<p class='title'>$visitante_img
				<a onclick='javascript:user_mostrar(\"$visitante\")'>$visitante_nombre</a> dejo un mensaje: <span>".$fnc->FechaCOM($fecha)."</span></p>
				<p>".$fnc->mostrar_html($mensaje)."</p>
			</div>";
		}
		$result->close();

		// Verificamos si aun quedan mas mensajes por cargar
		if(($start + 10) <= $count[0]){
			// Si quedan regresamos el control a 0 para recibir mas peticiones
			$control = 0;
		} else {
			// Si no quedan regresamos el control a 1 para ya no recibir mas peticiones
			$control = 1;
		}
	} else {
		// Regresamos el valor 1 de que ya no hay mas mensajes
		$control = 1;
	}

	// Regresamos informacion
	echo json_encode(array('status'=>'Se cargaron mensajes con exito','control'=>$control,'resultado'=>$resultado,'user'=>$user,'start'=>$start));
	exit();
}

// Procesamos un mensaje al perfil de usuario
function user_mensaje($fnc,$db,$mail,$data_email,$template)
{
	$user = $fnc->secure_sql($_POST['user']); // ID del usuario
	$mensaje = $fnc->secure_sql($_POST['mensaje']);
	$id_curso = $fnc->secure_sql($_POST['id_curso']);
	$visitante = $_SESSION['id'];
	$resultado = '';

	// Formateamos el contenido
	$mensaje = $fnc->html_replace($mensaje);

	// Guardamos el mensaje en la base de datos
	$insert = $db->mysqli_action("INSERT INTO mensajes (user,visitante,mensaje,fecha) VALUES ('$user','$visitante','$mensaje',NOW())");

	// Obtenemos los datos del mensaje
	$result = $db->mysqli_select("SELECT fecha FROM mensajes WHERE id_mensaje='$insert'");
	while($row = $result->fetch_assoc()){
		$fecha = $row['fecha'];
	}
	$result->close();

	// Obtenemos el nombre del visitante
	$result1 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$visitante' LIMIT 1");
	while($row1 = $result1->fetch_assoc()){
		$visitante_nombre = $row1['nombre'];
		$visitante_fbid = (empty($row1['fbid'])) ? '' : $row1['fbid'];
		$visitante_avatar = (empty($row1['avatar'])) ? '' : $row1['avatar'];
		$visitante_img = '';
	}
	$result1->close();

	// Generamos la imagen para el visitante
	if(empty($visitante_fbid)){
		$visitante_img = "<img src='/avatar/$visitante_avatar' alt='Avatar' title='$visitante_nombre'>";
	} else {
		$visitante_img = "<img src='http://graph.facebook.com/$visitante_fbid/picture?type=large' alt='Avatar' title='$visitante_nombre'>";
	}

	// Obtenemos el nombre del autor
	$result2 = $db->mysqli_select("SELECT nombre,email FROM usuarios WHERE id='$user'");
	while($row2 = $result2->fetch_assoc()){
		$user_nombre = $row2['nombre'];
		$user_email = $row2['email'];
	}
	$result2->close();

	// Cremos el mensaje a mostrar
	$resultado .= "<div class='u_mensajes'>
		<p class='title'>$visitante_img
		<a onclick='javascript:user_mostrar(\"$visitante\")'>$visitante_nombre</a> dejo un mensaje: <span>".$fnc->FechaCOM($fecha)."</span></p>
		<p>".$fnc->mostrar_html($mensaje)."</p>
	</div>";

	// Verificamos que el autor y el visitante no sean los mismos
	if($user != $visitante){
		// Si no lo es configuramos el email a enviar
		$texto_notificacion = "$visitante_nombre publico en su perfil";
		$insert1 = $db->mysqli_action("INSERT INTO notificacion (user,texto,id_curso,id_discucion,status,type,fecha) VALUES ('$user','$texto_notificacion','$id_curso','$id_dis','NO','PERFIL',NOW())");

		// Generamos respuesta
		$respuesta = "<p>Estimad@ $user_nombre:</p>
					<p>Recibe este email por que recibio un mensaje nuevo en su perfil de usuario en el sitio ".$data_email['site_name']."</p>
					<p>Le mostramos los datos del mensaje recibido:</p>
					<div style='background-color: #FFFFCC;border: 1px solid #CCC;border-radius: 3px;margin-bottom: 10px;min-height: 50px;padding: 10px;'>
						<img src='https://lh3.googleusercontent.com/-R6Afx0S1CJ8/Ve5Bxe45GiI/AAAAAAAACLU/ni2l19AY57k/s189-Ic42/avatar.PNG' style='float:left;width:50px;margin-right:10px;'>
						<p>Usuario: $visitante_nombre<br>
						Mensaje:</p>
						<p>".$fnc->mostrar_html($mensaje)."</p>
					</div>";

		$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

		// Configuracion del servidor SMTP para el envio de email
		$mail->IsSMTP();
		$mail->Host = $data_email['host'];  // Indico el servidor para SMTP
		$mail->SMTPAuth = true;  // Debo de hacer autenticación SMTP
		$mail->Username = $data_email['user'];  // Indico un usuario
		$mail->Password = $data_email['pass'];  // clave de un usuario
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;  // Puerto por defecto del servidor SMTP

		// Datos del remitente
		$mail->From = $data_email['email']; // Email remitente
		$mail->FromName = $data_email['site_name']; // Nombre remitente

		// Indicamos el destinatario
		$mail->AddAddress($user_email, $user_nombre); // Email y nombre del destinatario
		// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
		$mail->Subject = 'Nuevo mensaje en su perfil - '.$data_email['site_name'];
		$mail->MsgHTML($respuesta);

		// Procesamos el envio de email
		if(!$mail->Send()) {
			// Error
			echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente','mensaje'=>$resultado,'user'=>$user));
		} else {
			// Exito
			echo json_encode(array('status'=>'Su mensaje se envio con exito!','mensaje'=>$resultado,'user'=>$user));
		}
	} else {
		// Si es el mismo no mandamos email
		echo json_encode(array('status'=>'Su mensaje se envio con exito!','mensaje'=>$resultado,'user'=>$user));
	}

	exit();
}

// Mostramos informacion sobre el usuario
function user_mostrar($fnc,$db)
{
	$user = $fnc->secure_sql($_POST['user']); // ID del usuario
	$back = "<div class='w_back icon-left_back' onclick='javascript:back_user()'>Regresar</div>"; // Boton para regresar
	$datos = '';
	$img = '';

	// Agregamos informacion
	$datos .= "$back<div id='u_content'>";

	// Obtenemos informacion sobre el usuario
	$result = $db->mysqli_select("SELECT fbid,avatar,nombre,puntos,fecha,ultimo_acceso FROM usuarios WHERE id='$user'");
	while($row = $result->fetch_assoc()){
		$nombre = $row['nombre'];
		$fecha = $row['fecha'];
		$puntos = $row['puntos'];
		$ultimo_acceso = $row['ultimo_acceso'];
		$fbid = (empty($row['fbid'])) ? '' : $row['fbid'];
		$avatar = (empty($row['avatar'])) ? '' : $row['avatar'];
	}
	$result->close();

	// Verificamos si el usuario ha iniciado sesion con facebook
	if(empty($fbid)){
		// Si no ha iniciado cargamos avatar por defecto
		$img = "<img src='/avatar/$avatar' alt='Avatar' title='$nombre'>";
	} else {
		$img = "<img src='http://graph.facebook.com/$fbid/picture?type=large' alt='Avatar' title='$nombre'>";
	}

	// Agregamos informacion sobre datos del usuario
	$datos .= "$img
	<div id='u_datos'>
		<h2>$nombre</h2>
		<p>Ingreso por primera vez: ".$fnc->FechaUSER($fecha)."</p>
		<p>Ultimo acceso a la plataforma: ".$fnc->FechaUSER($ultimo_acceso)."</p>
		<p>Puntos en todos los cursos: $puntos</p>
	</div>
	<div id='u_cursos'>
		<h3>Esta inscrito en los cursos:</h3><p>";

	// Obtenemos cursos en los que esta inscrito
	$result1 = $db->mysqli_select("SELECT id_curso FROM suscripcion WHERE user='$user'");
	while($row1 = $result1->fetch_assoc()){
		$id_curso = $row1['id_curso'];
		// Obtenemos los nombres de los cursos
		$result2 = $db->mysqli_select("SELECT titulo FROM cursos WHERE id_curso='$id_curso'");
		while($row2 = $result2->fetch_assoc()){
			$titulo = $row2['titulo'];
			// Agregamos los titulos de los cursos a los que esta suscrito
			$datos .= "$titulo <br>";
		}
		$result2->close();
	}
	$result1->close();

	// Agregamos informacion
	$datos .= "</p></div>
	<div id='u_form'>
		<h3>Dejar un mensaje en el perfil de $nombre:</h3>
		<form id='form_user'>
			<textarea id='content_user' placeholder='Deje un mensaje a $nombre'></textarea>
		</form>
		<p><button id='user_boton' class='submit' onclick='javascript:user_mensaje($user)' style='display:inline-block;'>Enviar</button>
			<span id='user_info'></span></p>
	</div>
	<div id='u_mensajes'>";

	// Consultamos si el usuario tiene mensajes
	$result3 = $db->mysqli_select("SELECT mensaje,fecha,visitante FROM mensajes WHERE user='$user' ORDER BY fecha DESC LIMIT 10");
	$count = $result3->num_rows;

	// Verificamos si el usuario tiene mensajes
	if($count > 0){
		// Obtenemos ultimos mensajes del usuario
		while($row3 = $result3->fetch_assoc()){
			$mensaje = $row3['mensaje'];
			$fecha = $row3['fecha'];
			$visitante = $row3['visitante'];

			// Obtenemos el nombre del visitante que dejo el mensaje
			$result4 = $db->mysqli_select("SELECT nombre,fbid,avatar FROM usuarios WHERE id='$visitante' LIMIT 1");
			while($row4 = $result4->fetch_assoc()){
				$visitante_nombre = $row4['nombre'];
				$visitante_fbid = (empty($row4['fbid'])) ? '' : $row4['fbid'];
				$visitante_avatar = (empty($row4['avatar'])) ? '' : $row4['avatar'];
				$visitante_img = '';
			}
			$result4->close();

			// Designamos imagen del visitante
			if(empty($visitante_fbid)){
				// Si no tiene fb mostramos avatar
				$visitante_img = "<img src='/avatar/$visitante_avatar' alt='avatar' title='$visitante_nombre'>";
			} else {
				// Mostramos avatar de fb
				$visitante_img = "<img src='http://graph.facebook.com/$visitante_fbid/picture?type=large' alt='avatar' title='$visitante_nombre'>";
			}

			$datos .= "<div class='u_mensajes'>
				<p class='title'>$visitante_img
				<a onclick='javascript:user_mostrar(\"$visitante\")'>$visitante_nombre</a> dejo un mensaje: <span>".$fnc->FechaCOM($fecha)."</span></p>
				<p>".$fnc->mostrar_html($mensaje)."</p>
			</div>";
		}
		$result3->close();
	} else {
		// Si no hay mensajes mostramos avizo
		$datos .= "No hay mensajes en este perfil";
	}

	// Agregamos informacion
	$datos .= "</div></div>
		<div id='user_cargando' class='cargando'></div>
		<input type='hidden' id='user_mostrar_id' value='$user'>
		$back";

	// Regresamos la informacion
	echo json_encode(array('status'=>'Informacion de usuario se cargo correctamente','datos'=>$datos,'user'=>$user));
	exit();
}