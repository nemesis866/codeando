<?php
/************************************************
Archivo con contenido de la pagina de cursos

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Inicializamos los objetos
$social = new Social();
$fnc = new Fnc();
$db = new Db();
$template = new Template();

// Seguridad
if(empty($_SESSION['user_id'])){
	$_SESSION['user_id'] = '';
}
if(empty($_SESSION['id'])){
	$_SESSION['id'] = '';
}
if(empty($_SESSION['logged_in'])){
	$_SESSION['logged_in'] = false;
}

$user = $_SESSION['id'];

// Obtenemos los cursos disponibles en la plataforma
$result = $db->mysqli_select("SELECT * FROM cursos WHERE public='YES' ORDER BY fecha DESC");
$count = $result->num_rows;

?>
<div id="presentacion_cursos" class="center parallax">
	<h2>Cursos disponibles</h2>
	<p>Seleccione un curso para ingresar a la plataforma o para ver su información disponible.</p>
</div>
<section id="wrapper" class="parallax">
<div id="content">

<div id="rutas">
	<p class="icon-home"><a href="/">Inicio</a> / <a href="/cursos/">Cursos</a><span id="rutas_info">Usted esta aqui</span></p>
</div>

<?php

// Mostramos redes sociales
$template->mostrar_redes();

?>

<!-- Codeando_6 -->
<ins class="adsbygoogle"
     style="display:block;margin:15px 0;"
     data-ad-client="ca-pub-0593566584451788"
     data-ad-slot="3824500815"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

<div class="center">
	<?php
	// Verificamos si el usuario inicio sesion
	if(!$_SESSION['logged_in']){
		// Si no inicio mostramos boton de login
		?>
		<button id="login_button" class="icon-usuario button login_button large left">Inicio de sesion</button>
		<h3 id="results"></h3>
		<?php
	} else {
		// Si inicio sesion mostramos mensaje de bienvenida
		?>
		<div id="bienvenida">
			<?php
			$mensaje = '';

			// Verificamos si el usuario inicio sesion con facebook
			if($_SESSION['logged_fb']){
				// Si inicio
				if($_SESSION['gender'] == 'male'){
					$mensaje = 'Bienvenido';
				} else {
					$mensaje = 'Bienvenida';
				}
			} else {
				// Si no inicio
				$mensaje = 'Bienvenid@';
			}

			// Mostramos el mensaje de bienvenida
			echo "<h3 class='titulo'>".$mensaje." ".ucwords($_SESSION['nombre']).", Por favor seleccione un curso para ingresar a la plataforma.</h3>";
		?>
		</div>
	<?php
	}
	?>
</div>

<div class="center">
<?php
// Verificamos si existen cursos en la plataforma
if($count > 0){
	// Si existen cursos los mostramos
	while($row = $result->fetch_assoc()){
		// Obtenemos las variables a utilizar
		$id_curso = $row['id_curso'];
		$img = $row['img'];
		$titulo = $row['titulo'];
		$subtitulo = $row['subtitulo'];

		// Creamos la url del curso
		$url = $fnc->Url($titulo);
		?>

		<div class='cursos'>
			<img src="/img_curso/<?php echo $img; ?>" alt="<?php echo $titulo; ?>" title="<?php echo $titulo; ?>">
			<h2><?php echo $titulo; ?></h2>
			<p><?php echo $subtitulo; ?></p>

			<?php
			// Obtenemos el total de usuarios sucritos
			$result2 = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE id_curso='$id_curso'");
			$count2 = $result2->fetch_row();
			$result2->close();

			// Obtenemos el total de discucisiones del curso
			$result3 = $db->mysqli_select("SELECT Count(id_discucion) FROM discucion WHERE id_curso='$id_curso'");
			$count3 = $result3->fetch_row();
			$result3->close();

			// Obtenemos el total de videos del curso
			$result4 = $db->mysqli_select("SELECT Count(id_tema) FROM temas WHERE id_curso='$id_curso' AND visibility='YES'");
			$count4 = $result4->fetch_row();
			$result4->close();
			?>

			<div class="estadisticas">
				<div id="user_suscription" class="icon-usuario" title="<?php echo $count2[0]; ?> usuarios suscritos en el curso">
					<span id="user_suscription<?php echo $id_curso; ?>"><?php echo $count2[0]; ?></span>
				</div>
				<div class="icon-discusion" title="<?php echo $count3[0]; ?> discusiones en el curso"><?php echo $count3[0]; ?></div>
				<div class="icon-video" title="El curso cuenta con <?php echo $count4[0]; ?> videos">
					<?php
					// Mostramos el total de videos en el curso
					echo $count4[0];
					?>
				</div>
			</div>

			<div class="buttons">
				<?php
				if($_SESSION['logged_in']){
					// Mostramos boton si esta logueado
					?>
					<div class="button-1 left">
						<a href="/curso/<?php echo strtolower($url); ?>/<?php echo $id_curso; ?>/">Información</a>
					</div>
					<?php
				} else {
					// Mostramos boton si no esta logueado
					?>
					<div class="button-3 left boton_none">
						<a href="/curso/<?php echo strtolower($url); ?>/<?php echo $id_curso; ?>/">Información</a>
					</div>
					<div class="button-2 boton_informacion">
						<a href="/curso/<?php echo strtolower($url); ?>/<?php echo $id_curso; ?>/">Información</a>
					</div>
					<?php
				}
			
			// Mostramos boton de ingreso solo si inicio sesion el usuario
			if($_SESSION['logged_in']){
				// Obtenemos informacion - si el usuario esta suscrito al curso
				$result_temp = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE id_curso='$id_curso' AND user='$user'");
				$count = $result_temp->fetch_row();
				$result_temp->close();

				// Verificamos si esta suscrito al curso
				if($count[0] > 0){
					// Si esta suscrito mostramos boton de ingreso
					?>
						<div class="button-1 right">
							<a onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</a>
						</div>
					</div>
					<?php
				} else {
					// Si no esta suscrito mostramos boton para suscribir
					?>
						<div class="button-1 right">
							<a id="boton_suscribir_<?php echo $id_curso; ?>" onclick="javascript:suscribir(<?php echo $id_curso; ?>)">Suscribirse al curso</a>
							<a id="boton_ingreso_<?php echo $id_curso; ?>" class="boton_none" onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</a>
						</div>
					</div>
					<?php
				}
			} else {
				?>
				<div id="boton_ingreso_<?php echo $id_curso; ?>" class="button-3 right boton_none">
					<a onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</a>
				</div>
				</div><?php
			}
			?>
		</div>
		<?php
	}
	$result->close();
} else {
	// Mostramos mensaje si no hay cursos disponibles
	?><p>No hay cursos disponibles en este momento.</p><?php
}
?>
</div>

<!-- Codeando_7 -->
<ins class="adsbygoogle"
     style="display:block;margin:10px 0;"
     data-ad-client="ca-pub-0593566584451788"
     data-ad-slot="5301234015"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>