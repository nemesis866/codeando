<?php
/************************************************
Creacion y edicion de un curso

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Iniciamos la base de datos
$db = new Db();
$fnc = new Fnc();

if(empty($_GET['sub'])){ $sub = '';} else { $sub = addslashes($_GET['sub']);}
if(empty($_GET['id'])){ $id_curso = '';} else { $id_curso = addslashes($_GET['id']);}
$user = $_SESSION['id'];
$nivel = $_SESSION['nivel'];

// Si es un curso nuevo mostramos mensaje orientado a la creacion del curso
if($sub == 'new'){
	?>
	<div class="alert">
		<img src="/img/alert.png">
		<h1>Bienvenido al asistente de creacion de cursos</h1>
		<p>Crear un curso en Codeando.org es muy facil, el primer paso para crear un curso es ingresar los datos que le solicitan enseguida.-</p>
	</div>
	<?php
}

// Consultamos si el curso a editar pertenece al usuario
$result_temp = $db->mysqli_select("SELECT Count(id_curso) FROM cursos WHERE id_curso='$id_curso' AND autor='$user'");
$curso = $result_temp->fetch_row();
$result_temp->close();

if($curso[0] > 0 || $sub == 'new' || $nivel == 10){
	?>
	<div class="cargando cargando_edit"></div>
	<?php
	// Declaramos variables
	$titulo = '';
	$subtitulo = '';
	$categoria = '';
	$id = '';
	$img = '';
	$description = '';
	$requeriment = '';

	// Obtenemos la informacion del curso
	$result = $db->mysqli_select("SELECT * FROM cursos WHERE id_curso='$id_curso' AND autor='$user'");
	while($row = $result->fetch_assoc()){
		$titulo = $row['titulo'];
		$subtitulo = $row['subtitulo'];
		$categoria = $row['categoria'];
		$description = $fnc->mostrar_curso($row['description']);
		$requeriment = $fnc->mostrar_curso($row['requeriment']);
		$id = $row['id_curso'];
		$img =$row['img'];
	}
	$result->close();

	// Obtenemos la longitud de las variables
	$count_title = 60 - strlen($titulo);
	$count_subtitle = 120 - strlen($subtitulo);
	$count_description = 2000 - strlen($description);
	$count_requeriment = 500 - strlen($requeriment);

	// Declaramos las cabeceras de los formularios
	if($sub == 'edit'){
		// Si es para editar
		$submit = 'Editar curso';
		?>
		<form id="form_curso_editar">
			<input type="hidden" id="id" value="<?php echo $id; ?>">
		<?php
	} else {
		// Si es nuevo
		$submit = 'Crear curso';
		?><form id="form_curso_nuevo"><?php
	}
	?>
		<p><label>Titulo:</label><br>
			<input type="text" id="title" class="input" data-info='info' maxlength="60" placeholder="Ingrese un titulo descriptivo" value="<?php echo $titulo; ?>" required>
			<span id="count_title" class="count"><?php echo $count_title; ?></span></p>
		<p><label>Subtitulo:</label><br>
			<textarea id="subtitle" class="input" maxlength="120" placeholder="El subtitulo ayudara a reforzar su titulo" required><?php echo $subtitulo; ?></textarea>
			<span id="count_subtitle" class="count"><?php echo $count_subtitle; ?></span></p>
		<p><label>Categoria:</label><br>
			<select id="category" class="select" required>
				<option><?php echo $categoria; ?></option>
				<?php
				// Obtenemos la lista de categorias
				$result1 = $db->mysqli_select("SELECT nombre FROM categorias");
				while($row = $result1->fetch_assoc()){
					if($categoria != $row['nombre']){
						?><option><?php echo $row['nombre']; ?></option><?php
					}
				}
				$result1->close();
				?>
			</select></p>
		<p><label>Descripción sobre el curso:</label><br>
		<textarea id="description" class="input" maxlength="2000" placeholder="Describa los detalles del curso" required><?php echo $description; ?></textarea>
			<span id="count_description" class="count"><?php echo $count_description; ?></span></p>
		<p><label>Conocimientos necesarios para tomar el curso:<br>
			(Escriba cada requisito en un renglon diferente)</label><br>
		<textarea id="requeriment" class="input" maxlength="500" placeholder="Ingrese los conocimientos a tener para poder tomar el curso" required><?php echo $requeriment; ?></textarea>
			<span id="count_requeriment" class="count"><?php echo $count_requeriment; ?></span></p>
		<p><input type="submit" class="submit" value="<?php echo $submit; ?>"></p>	
	</form>

	<?php
	// Verificamos si esta en modo edicion para mostrar formulario para cargar imagen de curso
	if($sub == 'edit'){
		?>
		<h3 class="subtitulo">Logo del curso</h3>

		<div id="show_logo">
			<img src="/img_curso/<?php echo $img; ?>" id="logo_img" alt="Logo" title="Logo">
			<p><span id="logo_msg">Logo Actual</span></p>
		</div>
		<form id="form_logo" enctype="multipart/form-data">
			<input type="hidden" id="id_curso" name="id_curso" value="<?php echo $id; ?>">
			<p><label>Seleccione la imagen a subir: (Tamaño maximo 10 Kb)</label><br>
				<input type="file" id="img_logo" name="file" required></p>
			<p><input type="submit" id="submit_logo" class="submit" value="Cambiar Imagen"></p>
			<div class="cargando cargando_logo"></div>
		</form>
		<?php
	} 
} else {
	// Si hay error mostramos mensaje
	?>Esta intentando modificar un curso que no es de su autoria...<?php
}