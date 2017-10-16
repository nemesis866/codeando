<?php
/************************************************
Archivo servidor del admin

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
	case 'cap_delete': // OK
		cap_delete($fnc,$db);
		break;
	case 'cap_nuevo'; // OK
		cap_nuevo($fnc,$db);
		break;
	case 'cap_visibility': // OK
		cap_visibility($fnc,$db);
		break;
	case 'curso_mensaje':
		curso_mensaje($fnc,$db);
		break;
	case 'curso_publicar_no': // OK
		curso_publicar_no($db,$fnc,$mail,$data_email,$template);
		break;
	case 'curso_publicar_yes': // OK
		curso_publicar_yes($db,$fnc,$mail,$data_email,$template);
		break;
	case 'eliminar_categoria': // OK
		eliminar_categoria($fnc,$db);
		break;
	case 'eliminar_curso': // OK
		Eliminar_curso($fnc,$db);
		break;
	case 'estadisticas': // OK
		estadisticas($fnc,$db);
		break;
	case 'form_aviso': // OK
		form_aviso($fnc,$db,$mail,$data_email,$template);
		break;
	case 'form_cap_edit': // OK
		form_cap_edit($fnc,$db);
		break;
	case 'form_categoria': // OK
		form_categoria($fnc,$db);
		break;
	case 'form_categoria_edit': // OK
		form_categoria_edit($fnc,$db);
		break;
	case 'form_categoria_solicitud': // OK
		form_categoria_solicitud($db, $fnc,$mail,$data_email,$template);
		break;
	case 'form_curso_editar': // OK
		form_curso_editar($fnc,$db);
		break;
	case 'form_curso_nuevo': // OK
		form_curso_nuevo($fnc,$db);
		break;
	case 'form_password': // OK
		form_password($db, $fnc);
		break;
	case 'form_perfil': // OK
		form_perfil($fnc,$db);
		break;
	case 'form_tema_doc': // OK
		form_tema_doc($fnc,$db);
		break;
	case 'form_tema_edit': // OK
		form_tema_edit($fnc,$db);
		break;
	case 'form_tema_github': // OK
		form_tema_github($fnc,$db);
		break;
	case 'form_tema_info': // OK
		form_tema_info($fnc,$db);
		break;
	case 'form_tema_video': // OK
		form_tema_video($fnc,$db);
		break;
	case 'orden_capitulos': // OK
		orden_capitulos($fnc,$db);
		break;
	case 'revicion_curso': // OK
		revicion_curso($fnc,$db);
		break;
	case 'tema_delete': // OK
		tema_delete($fnc,$db);
		break;
	case 'tema_nuevo': // OK
		tema_nuevo($fnc,$db);
		break;
	case 'tema_subir': // OK
		tema_subir($fnc,$db);
		break;
	case 'tema_visibility': // OK
		tema_visibility($fnc,$db);
		break;
}

// Eliminamos un capitulo del curso y sus temas
function cap_delete($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$id_curso = $fnc->secure_sql($_POST['id_curso']);

	// Eliminamos los capitulos
	$delete = $db->mysqli_action("DELETE FROM capitulos WHERE id_capitulo='$id'");

	// Eliminamos los temas del capitulo
	$delete2 = $db->mysqli_action("DELETE FROM temas WHERE id_capitulo='$id' AND id_curso='$id_curso'");

	echo json_encode(array('status'=>'Capitulo eliminado con exito'));
	exit();
}

// Agregamos un capitulo nuevo al curso en cuestion
function cap_nuevo($fnc,$db)
{
	$id_curso = $fnc->secure_sql($_POST['id']);
	$count = $fnc->secure_sql($_POST['i']);
	$autor = $_SESSION['id'];
	$orden = $count - 1;
	$title = "Capitulo $count";
	$title_tema = 'Tema 1';

	// Los creamos en la base de datos
	$insert = $db->mysqli_action("INSERT INTO capitulos (titulo,autor,id_curso,orden,visibility) VALUES ('$title','$autor','$id_curso','$orden','NO')");

	//Obtenemos el id del capitulo agregado
	$id_capitulo = $insert;

	// Guardamos informacion del tema
	$insert2 = $db->mysqli_action("INSERT INTO temas (titulo,autor,id_curso,id_capitulo,orden) VALUES ('$title_tema','$autor','$id_curso','$id_capitulo','1')");

	// Obtenemos el id del ultimo tema agregado
	$id_tema = $insert2;

	$i = $id_capitulo; // ID capitulo
	$ii = $i.''.$id_tema; // Combinacion del ID capitulo y tema

	$texto = "<div class='capitulo'>
		<div id='cap_$i'>
			<span class='number_cap_$i'>Capitulo $count: </span>
			<span id='cap_title_$i'>$title</span> 
			<span class='icon icon-edit' onclick='javascript:cap_form($i)' title='Editar'></span>
			<span id='cap_draw_$i' class='right icon icon-capdraw' title='Cambiar a publico' onclick='javascript:cap_visibility($i, \"draw\")'></span>
			<span id='cap_public_$i' class='right icon capitulo_public icon-cappublic' title='Cambiar a borrador' onclick='javascript:cap_visibility($i, \"public\")'></span>
			<span class='right icon icon-delete' onclick='javascript:cap_delete($i)' title='Eliminar capitulo'></span>
			<span id='cap_mostrar_$i' class='right icon icon-mas' onclick='javascript:cap_mostrar($i)' title='Mostrar temas'></span>
			<span id='cap_ocultar_$i' class='right icon icon-menos' style='display:none' onclick='javascript:cap_ocultar($i)' title='Ocultar temas'></span>
		</div>
		<div id='form_$i' style='display:none;'>
			<div id='cargando_line_$i' class='cargando_line'></div>
			<form id='form_cap_$i'>
				<span class='number_cap_$i'>Capitulo $count: </span>
				<input type='text' id='title' class='input' maxlength='60' value='$title'>
				<span class='icon icon-submit' onclick='javascript:cap_submit($i)' title='Guardar'></span>
				<span class='icon icon-cancel' onclick='javascript:cap_cancel($i)' title='Cancelar'></span>
			</form>
		</div>
		<div id='cap_delete_$i' style='display:none;'>
			Esta seguro de eliminar el capitulo: 
			<span class='icon icon-confirm' onclick='javascript:cap_delete_yes($i,$id_curso)'></span>
			<span class='icon icon-cancel' onclick='javascript:cap_cancel($i)'></span>
		</div>
	</div>
	<div id='mostrar_tema_$i' style='display:none'>
		<ul class='add_temas_$i' style='list-style:none'>
			<li class='items$i orden$id_tema' data-id='$id_tema' data-orden='1' id='li_$ii'>
			<div class='tema' id='$ii'>
				<div id='tema_$ii'>
					<span class='number_tema_$id_tema'>Tema 1: </span>
					<span id='tema_title_$ii'>$title_tema</span> 
					<span class='icon icon-edit_tema' onclick='javascript:tema_form($ii)' title='Editar'></span>
					<span class='right icon icon-bajar' title='Bajar tema' onclick='javascript:tema_bajar($i,$id_tema)'></span>
					<span class='right icon icon-subir' title='Subir tema' onclick='javascript:tema_subir($i,$id_tema)'></span>
					<span class='right icon icon-delete_tema' onclick='javascript:tema_delete($ii)' title='Eliminar tema'></span>
					<span id='mostrar_$ii' class='right icon icon-mostrar' onclick='javascript:tema_mostrar($ii)' title='Mostrar opciones'></span>
					<span id='ocultar_$ii' class='right icon icon-ocultar' style='display:none' onclick='javascript:tema_ocultar($ii)' title='Ocultar opciones'></span>
				</div>
				<div id='form_$ii' style='display:none;'>
					<div id='cargando_line_$ii' class='cargando_line'></div>
					<form id='form_tema_$ii'>
						<span class='number_tema_$id_tema'>Tema 1: </span>
						<input type='text' id='title' class='input' maxlength='60' value='$title_tema' required>
						<span class='icon icon-submit_tema' onclick='javascript:tema_submit($ii,$id_tema)' title='Guardar'></span>
						<span class='icon icon-cancel_tema' onclick='javascript:tema_cancel($ii)' title='Cancelar'></span>
					</form>
				</div>
				<div id='tema_delete_$ii' style='display:none;'>
					Esta seguro de eliminar el tema: 
					<span class='icon icon-confirm_tema' onclick='javascript:tema_delete_yes($id_tema,$i,$id_curso)'></span>
					<span class='icon icon-cancel_tema' onclick='javascript:tema_cancel($ii)'></span>
				</div>
				<div id='iconos_$ii' class='iconos' style='display:none'>
					<span id='tema_draw_$ii' class='tema_draw icon-draw' onclick='javascript:tema_visibility($ii, $id_tema, \"draw\")'>Borrador</span>
					<span id='tema_public_$ii' class='tema_public icon-public' onclick='javascript:tema_visibility($ii, $id_tema, \"public\")'>Publico</span>
					Opciones: 
					<span class='icon-info' title='Informacion' onclick='javascript:tema_router($ii,\"info\")'></span>
					<span class='icon-doc' title='Documentacion' onclick='javascript:tema_router($ii,\"doc\")'></span>
					<span class='icon-video' title='Insertar video' onclick='javascript:tema_router($ii,\"video\")'></span>
					<span class='icon-github' title='Insertar repositorio' onclick='javascript:tema_router($ii,\"github\")'></span>
				</div>
			</div>
			<div id='_info_$ii' class='info' style='display:none;'>
				<div id='info_$ii'>
					<p>Informacion sobre el tema:</p>
					<div class='cargando'></div>
					<form id='form_info_$ii'>
						<div id='resp_toolbox'>
							<span onclick='javascript:toolbox(2,\"info$ii\")' class='icon-bold' title='Negrita'><strong></strong></span>
							<span onclick='javascript:toolbox(3,\"info$ii\")' class='icon-italic' title='Cursiva'><i></i></span>
							<span onclick='javascript:toolbox(4,\"info$ii\")' class='icon-underline' title='Subrayado'><u></u></span>
							<span onclick='javascript:toolbox(5,\"info$ii\")' class='icon-strike' title='Tachado'></span>
						</div>
						<p><textarea id='info$ii' class='input_tema' placeholder='Ingrese informacion sobre el tema, sea claro y detallado.'></textarea></p>
					</form>
					<p>
						<button id='button_info_$ii' class='submit' onclick='javascript:tema_info($ii,$id_tema)'>Guardar</button>
						<span id='data_info_$ii'></span>
					</p>
				</div>
				<div id='doc_$ii' style='display:none'>
					<p>Documentacion sobre el tema:</p>
					<div class='cargando'></div>
					<form id='form_doc_$ii'>
						<div id='resp_toolbox'>
							<span onclick='javascript:toolbox(1,\"doc$ii\")' class='icon-code' title='Insertar codigo'>CODE</span>
							<span onclick='javascript:toolbox(2,\"doc$ii\")' class='icon-bold' title='Negrita'><strong></strong></span>
							<span onclick='javascript:toolbox(3,\"doc$ii\")' class='icon-italic' title='Cursiva'><i></i></span>
							<span onclick='javascript:toolbox(4,\"doc$ii\")' class='icon-underline' title='Subrayado'><u></u></span>
							<span onclick='javascript:toolbox(5,\"doc$ii\")' class='icon-strike' title='Tachado'></span>
						</div>
						<p><textarea id='doc$ii' class='input_tema' placeholder='Si el tema necesita documentacion de apoyo, ingresela aqui.''></textarea></p>
					</form>
					<p>
						<button id='button_doc_$ii' class='submit' onclick='javascript:tema_doc($ii,$id_tema)'>Guardar</button>
						<span id='data_doc_$ii'></span>
					</p>
				</div>
				<div id='video_$ii' style='display:none'>
					<p>Ingrese la url del video del tema (Youtube):</p>
					<div class='cargando'></div>
					<form id='form_video_$ii'>
						<p><input type='text' id='video$ii' class='input_tema' placeholder='Ingrese la url del video de youtube' required></p>
					</form>
					<p>
						<button id='button_video_$ii' class='submit' onclick='javascript:tema_video($ii,$id_tema)'>Guardar</button>
						<span id='data_video_$ii'></span>
					</p>
					<p id='data_video_title_$ii'>Titulo: </p>
				</div>
				<div id='github_$ii' style='display:none'>
					<p>Ingrese la url del repositorio del tema (Github):</p>
					<div class='cargando'></div>
					<form id='form_github_$ii'>
						<p><input type='text' id='github$ii' class='input_tema' placeholder='Ingrese la url del repositorio de github' required></p>
					</form>
					<p>
						<button id='button_github_$ii' class='submit' onclick='javascript:tema_github($ii,$id_tema)'>Guardar</button>
						<span id='data_github_$ii'></span>
					</p>
					<p id='data_github_title_$ii'>Repositorio: </p>
				</div>
			</div>
			</li>
		</ul>
		<div id='conteo_$i' data-i='2'></div>
		<div id='tema_nuevo' class='icon-agregar tema_nuevo$id_capitulo' onclick='javascript:tema_nuevo($id_curso,$id_capitulo)'>Agregar Tema nuevo</div>
		<div id='cargando_tema'></div>
	</div>
	";

	echo json_encode(array('status'=>'El capitulo de creo con exito','texto'=>$texto,'i'=>$i));
	exit();
}

// Determina la visibilidad de un tema
function cap_visibility($fnc, $db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$visibility = $fnc->secure_sql($_POST['visibility']);
	$valor = '';
	$return = '';

	// Determinamos el valor
	if($visibility == 'draw'){
		$valor = 'YES';
		$return = 'public';
	} else if($visibility == 'public'){
		$valor = 'NO';
		$return = 'draw';
	}

	// Actualizamos la base de datos
	$update = $db->mysqli_action("UPDATE capitulos SET visibility='$valor' WHERE id_capitulo='$id'");

	echo json_encode(array('status'=>'Se actualizo la visibilidad del capitulo','id'=>$id,'return'=>$return));
	exit();
}

// Mostramos mensajes sobre el curso
function curso_mensaje($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$msg = '';

	// Obtenemos los mensajes del curso
	$result = $db->mysqli_select("SELECT instruccion FROM cursos WHERE id_curso='$id'");
	while($row = $result->fetch_assoc()){
		$msg .= $row['instruccion'];
	}
	$result->close();

	// Agregamos boton de cerrar
	$msg .= "<p><a onclick='javascript:curso_mensaje_no($id);'>Cerrar</p>";

	echo json_encode(array('msg'=>$msg));
	exit();
}

// Si no queremos publicar el curso
function curso_publicar_no($db,$fnc,$mail,$data_email)
{
	$id = $fnc->secure_sql($_POST['id']);
	$content = $fnc->secure_sql($_POST['content']);

	// Damos formato al contenido
	$resultado = "<p><strong>Su curso no fue publicado (".date('d - m - Y')."):</strong><br>
			$content</p>";

	// Consultamos que no alla mas respuestas
	$result = $db->mysqli_select("SELECT instruccion,titulo,autor FROM cursos WHERE id_curso='$id'");
	while($row = $result->fetch_assoc()){
		$instruccion = (empty($row['instruccion'])) ? '' : $row['instruccion'];
		$titulo = (empty($row['titulo'])) ? '' : $row['titulo'];
		$autor = (empty($row['autor'])) ? '' : $row['autor'];
	}
	$result->close();

	// Obtenemos datos del autor del curso
	$result1 = $db->mysqli_select("SELECT nombre,email FROM usuarios WHERE id='$autor'");
	while($row1 = $result1->fetch_assoc()){
		$nombre = (empty($row['nombre'])) ? '' : $row['nombre'];
		$email = (empty($row['email'])) ? '' : $row['email'];	
	}
	$result1->close();

	// Verificamos que no alla mas respuestas
	if(empty($instruccion)){
		// Si es la primera vez solo actualizamos
		// Actualizamos las instrucciones del curso
		$update = $db->mysqli_action("UPDATE cursos SET instruccion='$resultado',revicion='NO' WHERE id_curso='$id'");
	} else {
		// Si hay mas optenemos la respuestas

		// Armamos la nueva respuesta
		$resultado .= $instruccion;

		// Actualizamos las instrucciones del curso
		$update = $db->mysqli_action("UPDATE cursos SET instruccion='$resultado',revicion='NO' WHERE id_curso='$id'");
	}

	// Enviamos notificacion al usuario
	// Generamos respuesta
	$respuesta = "<p>$nombre: Recibe este mensaje por que solicito la revisión para la publicación del curso: '$titulo' en la plataforma ".$data_email['site_name']."</p>
				<p>Le informamos que su curso no fue publicado por las siguientes razones:</p>
				<p>$content</p>
				<p>Le sugerimos realizar los cambios pertinentes en su curso para que lo vuelva enviar a revisión, gracias por preferir a ".$data_email['site_name']."</p>";

	$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

	// Configuracion del servidor SMTP para el envio de email
	$mail->CharSet = "UTF-8";
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
	$mail->AddAddress($email, $nombre); // Email y nombre del destinatario
	// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
	$mail->Subject = 'Avizo de publicación de curso - '.$data_email['site_name'];
	$mail->MsgHTML($respuesta);

	// Procesamos el envio de email
	if(!$mail->Send()) {
		// Error
		echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente'));
	} else {
		// Exito

		echo json_encode(array('status'=>'Curso no publicado, usuario notificado'));
	}

	exit();
}

// Si queremos publicar el curso
function curso_publicar_yes($db,$fnc,$mail,$data_email)
{
	$id = $fnc->secure_sql($_POST['id']);
	$content = $fnc->secure_sql($_POST['content']);

	// Damos formato al contenido
	$resultado = "<p><strong>Su curso fue publicado (".date('d - m - Y')."):</strong><br>
			$content</p>";

	// Actualizamos la instruccion del curso
	$update = $db->mysqli_action("UPDATE cursos SET instruccion='$resultado',public='YES',revicion='NO' WHERE id_curso='$id'");

	// Obtenemos el autor del curso
	$result = $db->mysqli_select("SELECT titulo,autor FROM cursos WHERE id_curso='$id'");
	while($row = $result->fetch_assoc()){
		$titulo = (empty($row['titulo'])) ? '' : $row['titulo'];
		$autor = (empty($row['autor'])) ? '' : $row['autor'];
	}
	$result->close();

	// Obtenemos datos del autor del curso
	$result1 = $db->mysqli_select("SELECT nombre,email FROM usuarios WHERE id='$autor'");
	while($row1 = $result1->fetch_assoc()){
		$nombre = (empty($row['nombre'])) ? '' : $row['nombre'];
		$email = (empty($row['email'])) ? '' : $row['email'];	
	}
	$result1->close();

	// Enviamos notificacion al usuario
	// Generamos respuesta
	$respuesta = "<p>$nombre: Recibe este mensaje por que solicito la revisión para la publicación de su curso: '$titulo' en la plataforma ".$data_email['site_name']."</p>
				<p>Le informamos que su curso fue publicado con exito:</p>
				<p>$content</p>
				<p>Encontrara su curso visible en el area de cursos disponibles.<br>
				<a href='http://codeando.org/cursos/'>Cursos disponibles</a></p>
				<p>Gracias por su prefrencia.</p>";

	$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

	// Configuracion del servidor SMTP para el envio de email
	$mail->CharSet = "UTF-8";
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
	$mail->AddAddress($email, $nombre); // Email y nombre del destinatario
	// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
	$mail->Subject = 'Avizo de publicación de curso - '.$data_email['site_name'];
	$mail->MsgHTML($respuesta);

	// Procesamos el envio de email
	if(!$mail->Send()) {
		// Error
		echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente'));
	} else {
		// Exito

		echo json_encode(array('status'=>'Curso publicado, usuario notificado'));
	}
	exit();
}

// Eliminar categoria
function eliminar_categoria($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);

	// Eliminamos la categoria
	$delate = $db->mysqli_action("DELETE FROM categorias WHERE id_categoria='$id'");

	echo json_encode(array('status'=>'La categoria se elimino con exito','id'=>$id));
	exit();
}

// Eliminar curso
function eliminar_curso($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);

	// Obtenemos informacion del curso
	$result = $db->mysqli_select("SELECT public FROM cursos WHERE id_curso='$id'");
	while($row = $result->fetch_assoc()){
		$public = $row['public'];
	}
	$result->close();

	// Si el curso esta online no se podra eliminar
	if($public == 'NO'){
		$delete = $db->mysqli_action("DELETE FROM cursos WHERE id_curso='$id'");

		echo json_encode(array('status'=>'El curso se elimino con exito'));
		exit();
	} else {
		echo json_encode(array('error'=>'El curso no se puede eliminar se encuentra online'));
		exit();
	}
}

// Cargamos estadisticas de un curso
function estadisticas($fnc,$db)
{
	$id_curso = $fnc->secure_sql($_POST['id']);
	$result = '';

	// Obtenemos el total de usuarios sucritos
	$result1 = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE id_curso='$id_curso'");
	$count1 = $result1->fetch_row();
	$result1->close();

	// Obtenemos el total de discucisiones del curso
	$result2 = $db->mysqli_select("SELECT Count(id_discucion) FROM discucion WHERE id_curso='$id_curso'");
	$count2 = $result2->fetch_row();
	$result2->close();

	// Obtenemos el total de videos del curso
	$result3 = $db->mysqli_select("SELECT Count(id_tema) FROM temas WHERE id_curso='$id_curso' AND visibility='YES'");
	$count3 = $result3->fetch_row();
	$result3->close();

	// Obtenemos los capitulos del curso
	$result4 = $db->mysqli_select("SELECT Count(id_capitulo) FROM capitulos WHERE id_curso='$id_curso' AND visibility='YES'");
	$count4 = $result4->fetch_row();
	$result4->close();

	// Obtenemos los temas del curso
	$result5 = $db->mysqli_select("SELECT Count(id_tema) FROM temas WHERE id_curso='$id_curso' AND visibility='YES'");
	$count5 = $result5->fetch_row();
	$result5->close();

	// Armamos la respuesta
	$result = "<p><span class='icon-usuario'>".$count1[0]." Usuarios suscritos</span></p>
			<p><span class='icon-discusion'>".$count2[0]." Discusiones</span></p>
			<p><span class='icon-video'>".$count3[0]." Videos *</span></p>
			<p><span class='icon-capitulo'>".$count4[0]." Capitulos *</span></p>
			<p><span class='icon-capitulo'>".$count5[0]." Temas *</span></p>
			<p>* Solo se contabilizan los items que estan como visibles.</p>
			<p><a onclick='javascript:curso_estadistica_no($id_curso);'>Ocultar</a></p>";

	echo json_encode(array('result'=>$result));
	exit();
}

// Procesamos el formulario de aviso
function form_aviso($fnc,$db,$mail,$data_email,$template)
{
	$contenido = $_POST['contenido'];
	$id_curso = $_POST['id_curso'];
	$user = $_SESSION['id'];

	// Formatemoas el contenido
	$contenido = $fnc->html_replace($contenido);

	// Almacenamos en la base de datos
	$insert = $db->mysqli_action("INSERT INTO avisos (id_curso,contenido,fecha) VALUES ('$id_curso','$contenido',NOW())");

	// Obtenemos los detalles del curso
	$result = $db->mysqli_select("SELECT titulo FROM cursos WHERE id_curso='$id_curso'");
	while($row = $result->fetch_assoc()){
		$curso_titulo = $row['titulo'];
	}
	$result->close();

	// Generamos respuesta
	$respuesta = "<p>Hola, recibe este email por que esta suscrito al curso.- '$curso_titulo' en la plataforma Codeando.org</p>
				<p>Mensaje del instructor:</p>
				<div style='background-color: #FFFFCC;border: 1px solid #CCC;border-radius: 3px;margin-bottom: 10px;min-height: 50px;padding: 10px;'>
					<p>".$fnc->mostrar_html($contenido)."</p>
				</div>
				<br>
				<p>Puede ingresar al curso desde el <a href='http://codeando.org/cursos/'>Area de cursos</a>.</p>
				<p>Siguenos en twitter.-</p>
				<p><a href='http://twitter.com/codeando_org'>@codeando_org<br>
				<a href='http://twitter.com/programacionweb'>@programacionweb<br>
				<a href='http://twitter.com/paulo_866'>@paulo_866</a></p>";

	$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

	// Configuracion del servidor SMTP para el envio de email
	$mail->CharSet = "UTF-8";
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

	// Obtenemos los id de usuarios suscritos
	$result1 = $db->mysqli_select("SELECT DISTINCT user FROM suscripcion WHERE id_curso='$id_curso'");
	while($row1 = $result1->fetch_assoc()){
		$user_suscripcion = $row1['user'];

		// Obtenemos detalles del usuario
		$result2 = $db->mysqli_select("SELECT nombre,email FROM usuarios WHERE id='$user_suscripcion'");
		$count2 = $result2->num_rows;

		// Verificamos que el usuario exista
		if($count2 > 0){
			while($row2 = $result2->fetch_assoc()){
				// Indicamos los destinatario
				$mail->AddBCC($row2['email'], $row2['nombre']); // Email y nombre del destinatario
			}
		}
		$result2->close();
	}
	$result1->close();

	// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
	$mail->Subject = 'Nueva actividad en '.$curso_titulo.' - '.$data_email['site_name'];
	$mail->MsgHTML($respuesta);

	// Procesamos el envio de email
	if(!$mail->Send()) {
		// Error
		echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente'));
	} else {
		// Exito
		echo json_encode(array('status'=>'Su aviso se envio con exito'));
	}
}

// Formulario registro de categorias
function form_categoria($fnc,$db)
{
	$name = $fnc->secure_sql($_POST['name']);

	// Convertimos a minusculas la categoria
	$name = strtolower($name);

	// consultamos que la categoria no exista en la base de datos
	$result = $db->mysqli_select("SELECT Count(id_categoria) FROM categorias WHERE nombre='$name'");
	$count = $result->fetch_row();
	$result->close();

	// Verificamos que la categoria no exista en la base de datos
	if($count[0] == 0){
		// Si no existe
		// Guardamos en la base de datos
		$insert = $db->mysqli_action("INSERT INTO categorias (nombre) value ('$name')");

		echo json_encode(array('status'=>'La categoria se creo con exito'));
		exit();
	} else {
		// Avizamos que ya existe la categoria
		echo json_encode(array('error'=>'La categoria ya existe en la plataforma','name'=>$name));
		exit();
	}
}

// Formulario edicion de categorias
function form_categoria_edit($fnc,$db)
{
	$id_categoria = $fnc->secure_sql($_POST['id']);
	$name = $fnc->secure_sql($_POST['name']);

	// Convertimos a minusculas la categoria
	$name = strtolower($name);

	// Consultamos que la categoria no exista en la base de datos
	$result = $db->mysqli_select("SELECT Count(id_categoria) FROM categorias WHERE nombre='$name'");
	$count = $result->fetch_row();
	$result->close();

	// Verificamos que la categoria no exista en la base de datos
	if($count[0] == 0){
		// Si no existe
		// Guardamos en la base de datos
		$update = $db->mysqli_action("UPDATE categorias SET nombre='$name' WHERE id_categoria='$id_categoria'");

		echo json_encode(array('status'=>'La categoria se modifico con exito'));
	} else {
		// Si existe
		// Avizamos que ya existe la categoria
		echo json_encode(array('error'=>'Esta guardando el mismo nombre de categoria','name'=>$name));
	}

	exit();
}

// Procesamos formulario de solicitud de categoria
function form_categoria_solicitud($db, $fnc,$mail,$data_email)
{
	$cat = $fnc->secure_sql($_POST['name']);
	$id = $_SESSION['id'];

	// Obtenemos detalles del usuario
	$result = $db->mysqli_select("SELECT nombre,email FROM usuarios WHERE id='$id'");
	while($row = $result->fetch_assoc()){
		$nombre = (empty($row['nombre'])) ? '' : $row['nombre'];
		$email = (empty($row['email'])) ? '' : $row['email'];
	}
	$result->close();

	// Generamos respuesta
	$respuesta = "<p>$nombre: Solicito agregar una nueva categoria a la plataforma Codeando.org.</p>
				<p>Categoria solicitada:<br>
				$cat</p>
				<p>Detalles del solicitante.-<br>
				<strong>Nombre</strong>: $nombre<br>
				<strong>Email</strong>: $email</p>
				<p>Su solicitud sera atendida a la brevedad posible por uno de nuestros administradores.</p>";

	$respuesta = $template->email_header($respuesta).''.$template->email_footer($data_email['site_name']);

	// Configuracion del servidor SMTP para el envio de email
	$mail->CharSet = "UTF-8";
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
	$mail->AddAddress($data_email['email'], 'Paulo Andrade'); // Email y nombre del destinatario
	// $mail->AddReplyTo('', ''); // Email y nombre para enviar copia
	$mail->Subject = 'Solicitud de categoria nueva - '.$data_email['site_name'];
	$mail->MsgHTML($respuesta);

	// Procesamos el envio de email
	if(!$mail->Send()) {
		// Error
		echo json_encode(array('error'=>'Error al enviar sus datos, intente nuevamente'));
	} else {
		// Exito
		echo json_encode(array('status'=>'Categoria solicitada, administrador notificado'));
	}

	exit();
}

// Formulario edicion de cursos
function form_curso_editar($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$category = $fnc->secure_sql($_POST['category']);
	$description = $_POST['description'];
	$requeriment = $_POST['requeriment'];
	$subtitle = $fnc->secure_sql($_POST['subtitle']);
	$title = $fnc->secure_sql($_POST['title']);

	// Guardamos en la base de datos
	$update = $db->mysqli_action("UPDATE cursos SET categoria='$category',titulo='$title',subtitulo='$subtitle',fecha_update=NOW(),description='$description',requeriment='$requeriment' WHERE id_curso='$id'");

	echo json_encode(array('status'=>'El curso se edito con exito'));
	exit();
}

// Formulario creacion de cursos
function form_curso_nuevo($fnc,$db)
{
	$category = $fnc->secure_sql($_POST['category']);
	$description = $_POST['description'];
	$requeriment = $_POST['requeriment'];
	$subtitle = $fnc->secure_sql($_POST['subtitle']);
	$title = $fnc->secure_sql($_POST['title']);
	$autor = $_SESSION['id'];
	$img = 'default.png'; // Direccion de la imagen del curso por default
	$url2 = '';

	// Creamos la url del curso
	$url = strtolower($title);
	$data = explode(' ',$url);
	$count = count($data);
	for($i=0; $i < $count; $i++){
		if($i == ($count - 1)){
			$url2.= $data[$i];
		} else {
			$url2.= $data[$i].'-';
		}
	}
	// Limpiamos el string
	$url2 = $fnc->sanear_string($url2);

	// Damos formato al titulo
	$title = ucfirst($title);

	// Guardamos en la base de datos
	$insert = $db->mysqli_action("INSERT INTO cursos (categoria,titulo,subtitulo,description,requeriment,autor,img,public,url,fecha,fecha_update,revicion) VALUES ('$category','$title','$subtitle','$description','$requeriment','$autor','$img','NO','$url2',NOW(),NOW(),'NO')");

	// Obtenemos el id del curso creado
	$id_curso = $insert;

	// Texto del primer capitulo del curso
	$cap = 'Primer capitulo del curso';

	// Guardamos el capitulo del curso
	$insert2 = $db->mysqli_action("INSERT INTO capitulos (titulo,autor,id_curso,visibility) VALUES ('$cap','$autor','$id_curso','NO')");

	// Obtenemos el ID del capitulo nuevo
	$id_capitulo = $insert2;

	// Texto del primer tema del curso
	$tema = 'Primer tema del capitulo';

	// Guardamos el tema del curso
	$insert3 = $db->mysqli_action("INSERT INTO temas (titulo,autor,id_curso,id_capitulo,orden,visibility) VALUES ('$tema','$autor','$id_curso','$id_capitulo','1','NO')");

	echo json_encode(array('status'=>'El curso se creo con exito'));
	exit();
}

// Formulario de edicion de capitulos
function form_cap_edit($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$title = $fnc->secure_sql($_POST['title']);

	// Formateamos el titulo
	$title = strtolower($title);
	$title = ucfirst($title);

	$update = $db->mysqli_action("UPDATE capitulos SET titulo='$title' WHERE id_capitulo='$id'");

	echo json_encode(array('status'=>'El titulo se actualizo con exito','title'=>$title,'id'=>$id));
	exit();
}

// Procesamos el formulario para cambiar contraseña
function form_password($db, $fnc)
{
	$pass = md5($fnc->secure_sql($_POST['pass']));
	$newpass = md5($fnc->secure_sql($_POST['new_pass']));
	$id = $_SESSION['id'];

	// Verificamos que la contraseña anterior sea correcta
	$result = $db->mysqli_select("SELECT id FROM usuarios WHERE password='$pass' AND id='$id' LIMIT 1");
	$count = $result->num_rows;
	$result->close();

	if($count > 0){
		// Actualizamos la contraseña
		$update = $db->mysqli_action("UPDATE usuarios SET password='$newpass' WHERE id='$id'");

		echo json_encode(array('status'=>'La contraseña se cambio con exito'));
	} else {
		// Informamos el error
		echo json_encode(array('error'=>'La contraseña actual no coincide'));
	}

	exit();
}

// Formulario para guardar la configuracion de perfil del usuario
function form_perfil($fnc,$db)
{
	$bio = $fnc->secure_sql($_POST['bio']);
	$google = $fnc->secure_sql($_POST['google']);
	$twitter = $fnc->secure_sql($_POST['twitter']);
	$user = $_SESSION['id'];

	// Actualizamos el perfil
	$update = $db->mysqli_action("UPDATE usuarios SET bio='$bio',google='$google',twitter='$twitter' WHERE id='$user'");

	echo json_encode(array('status'=>'Perfil actualizado con exito'));
	exit();
}

// Formulario para la documentacion de los temas
function form_tema_doc($fnc,$db)
{
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$contenido = $_POST['contenido'];

	// Formateamos el contenido
	$contenido = $fnc->html_replace($contenido);

	// Actualizamos la base de datos
	$update = $db->mysqli_action("UPDATE temas SET doc='$contenido' WHERE id_tema='$id_tema'");

	echo json_encode(array('id'=>$id_tema,'status'=>'Documentación del tema se guardo con exito'));
	exit();
}

// Formulario de edicion de temas
function form_tema_edit($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$title = $fnc->secure_sql($_POST['title']);

	// Formateamos el titulo
	$title = strtolower($title);
	$title = ucfirst($title);

	// Actualizamos el titulo
	$update = $db->mysqli_action("UPDATE temas SET titulo='$title' WHERE id_tema='$id_tema'");

	echo json_encode(array('status'=>'Titulo del tema actualizado','title'=>$title,'id'=>$id));
	exit();
}

// Formulario para guardar un repositorio de un tema
function form_tema_github($fnc,$db)
{
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$github = $fnc->secure_sql($_POST['github']);

	// Verificamos si se trata de una url de github
	if($fnc->github_url($github) !== false){
		// Si es actualizamos la base de datos
		$update = $db->mysqli_action("UPDATE temas SET github='$github' WHERE id_tema='$id_tema'");

		echo json_encode(array('id'=>$id_tema,'status'=>'Repositorio del tema se guardo con exito'));
		exit();
	} else {
		// Si no es mostramos mensaje
		echo json_encode(array('id'=>$id_tema,'error'=>'Ingrese una url de github valida'));
		exit();
	}
}

// Formulario para la informacion de los temas
function form_tema_info($fnc,$db)
{
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$contenido = $_POST['contenido'];

	// Procesamos el contenido
	$contenido = $fnc->html_replace($contenido);

	// Actualizamos la base de datos
	$update = $db->mysqli_action("UPDATE temas SET info='$contenido' WHERE id_tema='$id_tema'");

	echo json_encode(array('id'=>$id_tema,'status'=>'Informacion del tema se guardo con exito'));
	exit();
}

// Formulario para la informacion de los temas
function form_tema_video($fnc,$db)
{
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$video = $fnc->secure_sql($_POST['video']);

	// Verificamos si se trata de una url de youtube
	if($fnc->youtube_url($video)){
		// Obtenemos la url del video
		$video = $fnc->youtube_url($video);

		// Actualizamos la base de datos
		$update = $db->mysqli_action("UPDATE temas SET video='$video' WHERE id_tema='$id_tema'");

		// Obtenemos el titulo del video
		$title = $fnc->youtube_video($video);

		echo json_encode(array('id'=>$id_tema,'status'=>'Video del tema se guardo con exito','title'=>$title));
		exit();
	} else {
		// Si no es una url de youtube mostramos mensaje
		echo json_encode(array('id'=>$id_tema,'error'=>'Ingrese una url de youtube valida'));
		exit();
	}
}

// Ordenamos los capitulos despues de arrastrar y soltar
function orden_capitulos($fnc,$db)
{
	// Recibimos el arreglo enviado desde javascript
	$object = json_decode($_POST['object']);
	// Contador para el nuevo acomodo de capitulos
	$i = 0;

	// actualizamos los capitulos en base al orden del objeto
	foreach($object as $obj){
		// Obtenemos el identificador de cada capitulo ya con el nuevo acomodo
		$id_capitulo = $obj->identificador;
		// Actualizamos la base de datos
		$update = $db->mysqli_action("UPDATE capitulos SET orden='$i' WHERE id_capitulo='$id_capitulo'");
		// Incrementamos el contador
		$i++;
	}

	echo json_encode(array('status'=>'Orden de los capitulos actualizado'));
	exit();
}

// Enviamos un curso a revicion
function revicion_curso($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);

	// Actualizamos el status del curso
	$update = $db->mysqli_action("UPDATE cursos SET revicion='YES' WHERE id_curso='$id'");

	echo json_encode(array('status'=>'El curso se envio a revisión'));
	exit();
}

// Eliminamos un tema del curso
function tema_delete($fnc,$db)
{
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$id_cap = $fnc->secure_sql($_POST['id_cap']);
	$id_curso = $fnc->secure_sql($_POST['id_curso']);

	// Eliminamos el tema de la base de datos
	$delete = $db->mysqli_action("DELETE FROM temas WHERE id_tema='$id_tema' AND id_capitulo='$id_cap'");

	// Creamos un array con los temas restantes
	$array = array();

	// Obtenemos los temas restantes
	$result = $db->mysqli_select("SELECT id_tema FROM temas WHERE id_curso='$id_curso' AND id_capitulo='$id_cap' ORDER BY orden");
	while($row = $result->fetch_assoc()){
		// Almacenamos los ID de los temas en el array
		$data = array('id'=>$row['id_tema']);
		array_push($array, $data);
	}
	$result->close();

	echo json_encode($array);
}

// Agregamos un capitulo nuevo al curso en cuestion
function tema_nuevo($fnc,$db)
{
	$count = $fnc->secure_sql($_POST['i']);
	$id_curso = $fnc->secure_sql($_POST['id_curso']);
	$id_capitulo = $fnc->secure_sql($_POST['id_cap']);
	$autor = $_SESSION['id'];
	$title_tema = 'Tema '.$count;

	// Guardamos informacion del tema en la base de datos
	$insert = $db->mysqli_action("INSERT INTO temas (titulo,autor,id_curso,id_capitulo,orden,visibility) VALUES ('$title_tema','$autor','$id_curso','$id_capitulo',$count,'NO')");

	//Obtenemos el id del tema agregado
	$id_tema = $insert;

	$i = $id_capitulo; // ID del capitulo
	$ii = $i.''.$id_tema; // Combinacion de ID capitulo y tema

	$texto = "
	<li class='items$i orden$id_tema' data-id='$id_tema' data-orden='$count' id='li_$ii'>
		<div class='tema' id='$ii'>
			<div id='tema_$ii'>
				<span class='number_tema_$id_tema'>$title_tema: </span>
				<span id='tema_title_$ii'>$title_tema</span> 
				<span class='icon icon-edit_tema' onclick='javascript:tema_form($ii)' title='Editar'></span>
				<span class='right icon icon-bajar' title='Bajar tema' onclick='javascript:tema_bajar($i,$id_tema)'></span>
				<span class='right icon icon-subir' title='Subir tema' onclick='javascript:tema_subir($i,$id_tema)'></span>
				<span class='right icon icon-delete_tema' onclick='javascript:tema_delete($ii)' title='Eliminar tema'></span>
				<span id='mostrar_$ii' class='right icon icon-mostrar' onclick='javascript:tema_mostrar($ii)' title='Mostrar opciones'></span>
				<span id='ocultar_$ii' class='right icon icon-ocultar' style='display:none' onclick='javascript:tema_ocultar($ii)' title='Ocultar opciones'></span>
			</div>
			<div id='form_$ii' style='display:none;'>
				<div id='cargando_line_$ii' class='cargando_line'></div>
				<form id='form_tema_$ii'>
					<span class='number_tema_$id_tema'>$title_tema: </span>
					<input type='text' id='title' class='input' maxlength='60' value='$title_tema' required>
					<span class='icon icon-submit_tema' onclick='javascript:tema_submit($ii,$id_tema)' title='Guardar'></span>
					<span class='icon icon-cancel_tema' onclick='javascript:tema_cancel($ii)' title='Cancelar'></span>
				</form>
			</div>
			<div id='tema_delete_$ii' style='display:none;'>
				Esta seguro de eliminar el tema: 
				<span class='icon icon-confirm_tema' onclick='javascript:tema_delete_yes($id_tema,$i,$id_curso)'></span>
				<span class='icon icon-cancel_tema' onclick='javascript:tema_cancel($ii)'></span>
			</div>
			<div id='iconos_$ii' class='iconos' style='display:none'>
				<span id='tema_draw_$ii' class='tema_draw icon-draw' onclick='javascript:tema_visibility($ii, $id_tema, \"draw\")'>Borrador</span>
				<span id='tema_public_$ii' class='tema_public icon-public' onclick='javascript:tema_visibility($ii, $id_tema, \"public\")'>Publico</span>
				Opciones: 
				<span class='icon-info' title='Información' onclick='javascript:tema_router($ii,\"info\")'></span>
				<span class='icon-doc' title='Documentación' onclick='javascript:tema_router($ii,\"doc\")'></span>
				<span class='icon-video' title='Insertar video' onclick='javascript:tema_router($ii,\"video\")'></span>
				<span class='icon-github' title='Insertar repositorio' onclick='javascript:tema_router($ii,\"github\")'></span>
			</div>
		</div>
		<div id='_info_$ii' class='info' style='display:none;'>
			<div id='info_$ii'>
				<p>Información sobre el tema:</p>
				<div class='cargando'></div>
				<form id='form_info_$ii'>
					<div id='resp_toolbox'>
						<span onclick='javascript:toolbox(2,\"info$ii\")' class='icon-bold' title='Negrita'><strong></strong></span>
						<span onclick='javascript:toolbox(3,\"info$ii\")' class='icon-italic' title='Cursiva'><i></i></span>
						<span onclick='javascript:toolbox(4,\"info$ii\")' class='icon-underline' title='Subrayado'><u></u></span>
						<span onclick='javascript:toolbox(5,\"info$ii\")' class='icon-strike' title='Tachado'></span>
					</div>
					<p><textarea id='info$ii' class='input_tema' placeholder='Ingrese información sobre el tema, sea claro y detallado.'></textarea></p>
				</form>
				<p>
					<button id='button_info_$ii' class='submit' onclick='javascript:tema_info($ii,$id_tema)'>Guardar</button>
					<span id='data_info_$ii'></span>
				</p>
			</div>
			<div id='doc_$ii' style='display:none'>
				<p>Documentación sobre el tema:</p>
				<div class='cargando'></div>
				<form id='form_doc_$ii'>
					<div id='resp_toolbox'>
						<span onclick='javascript:toolbox(1,\"doc$ii\")' class='icon-code' title='Insertar codigo'>CODE</span>
						<span onclick='javascript:toolbox(2,\"doc$ii\")' class='icon-bold' title='Negrita'><strong></strong></span>
						<span onclick='javascript:toolbox(3,\"doc$ii\")' class='icon-italic' title='Cursiva'><i></i></span>
						<span onclick='javascript:toolbox(4,\"doc$ii\")' class='icon-underline' title='Subrayado'><u></u></span>
						<span onclick='javascript:toolbox(5,\"doc$ii\")' class='icon-strike' title='Tachado'></span>
					</div>
					<p><textarea id='doc$ii' class='input_tema' placeholder='Si el tema necesita documentación de apoyo, ingresela aqui.''></textarea></p>
				</form>
				<p>
					<button id='button_doc_$ii' class='submit' onclick='javascript:tema_doc($ii,$id_tema)'>Guardar</button>
					<span id='data_doc_$ii'></span>
				</p>
			</div>
			<div id='video_$ii' style='display:none'>
				<p>Ingrese la url del video del tema (Youtube):</p>
				<div class='cargando'></div>
				<form id='form_video_$ii'>
					<p><input type='text' id='video$ii' class='input_tema' placeholder='Ingrese la url del video de youtube' required></p>
				</form>
				<p>
					<button id='button_video_$ii' class='submit' onclick='javascript:tema_video($ii,$id_tema)'>Guardar</button>
					<span id='data_video_$ii'></span>
				</p>
				<p id='data_video_title_$ii'>Titulo: </p>
			</div>
			<div id='github_$ii' style='display:none'>
				<p>Ingrese la url del repositorio del tema (Github):</p>
				<div class='cargando'></div>
				<form id='form_github_$ii'>
					<p><input type='text' id='github$ii' class='input_tema' placeholder='Ingrese la url del repositorio de github' required></p>
				</form>
				<p>
					<button id='button_github_$ii' class='submit' onclick='javascript:tema_github($ii,$id_tema)'>Guardar</button>
					<span id='data_github_$ii'></span>
				</p>
				<p id='data_github_title_$ii'>Repositorio: </p>
			</div>
		</div>
	</li>";

	echo json_encode(array('status'=>'El tema se creo con exito','texto'=>$texto,'i'=>$count,'id'=>$id_capitulo));
	exit();
}

// Subimos el tema en 1
function tema_subir($fnc,$db)
{
	$current = $fnc->secure_sql($_POST['current']); // Orden del tema a subir
	$current_id = $fnc->secure_sql($_POST['current_id']); // ID del tema a subir
	$prev = $fnc->secure_sql($_POST['prev']); // Orden del tema previo
	$prev_id = $fnc->secure_sql($_POST['prev_id']); // ID del tema previo

	// Actualizamos el orden del tema a subir
	$update = $db->mysqli_action("UPDATE temas SET orden='$current' WHERE id_tema='$current_id'");

	// Actualizamos el orden del tema previo
	$update2 = $db->mysqli_action("UPDATE temas SET orden='$prev' WHERE id_tema='$prev_id'");

	echo json_encode(array('status'=>'Orden de tema actualizado'));
	exit();
}

// Determina la visibilidad de un tema
function tema_visibility($fnc,$db)
{
	$id = $fnc->secure_sql($_POST['id']);
	$id_tema = $fnc->secure_sql($_POST['id_tema']);
	$visibility = $fnc->secure_sql($_POST['visibility']);
	$valor = '';
	$return = '';
	$status = '';

	// Determinamos el valor segun el tipo
	if($visibility == 'draw'){
		// Si es borrador
		$valor = 'YES';
		$return = 'public';
		$status = 'Tema publicado';
	} else if($visibility == 'public'){
		// Si es publico
		$valor = 'NO';
		$return = 'draw';
		$status = 'Tema en borrador';
	}

	// Actualizamos la base de datos
	$update = $db->mysqli_action("UPDATE temas SET visibility='$valor' WHERE id_tema='$id_tema'");

	echo json_encode(array('status'=>$status,'id'=>$id,'return'=>$return));
	exit();
}