<?php
/************************************************
Archivo en el que mostramos los detalles de un
curso en concreto

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Seguridad
if(empty($_SESSION['logged_fb'])){
	$_SESSION['logged_fb'] = false;
}

// Inicializamos los objetos
$social = new Social();
$fnc = new Fnc();
$db = new Db();
$template = new Template();

// Obtenemos parametros
if(empty($_GET['id_curso'])){ $id_curso = '';} else { $id_curso = addslashes($_GET['id_curso']);}

// Obtenemos los detalles del curso a mostrar
$result = $db->mysqli_select("SELECT categoria,autor,titulo,subtitulo,img,description,requeriment FROM cursos WHERE id_curso='$id_curso' LIMIT 1");
while($row = $result->fetch_assoc()){
	$autor = $row['autor'];
	$description = $row['description'];
	$img = $row['img'];
	$titulo = $row['titulo'];
	$subtitulo = $row['subtitulo'];
	$requeriment = $row['requeriment'];
	$categoria = $row['categoria'];
}
$result->close();

// Añadimos listas a los requisitos del curso
$data = explode('<br>', $requeriment);
$datos = '';
for($i = 0; $i < count($data); $i++){
	if(!empty($data[$i])){
		$datos .= '<li>'.$data[$i].'</li>';
	}
}
$requeriment = $datos;
?>

<div id="presentacion_curso" class="center parallax">
	<h1><?php echo $titulo; ?></h1>
	<h2><?php echo $subtitulo; ?></h2>
</div>
<section id="wrapper" class="parallax">
<div id="content">

<div id="rutas">
	<p class="icon-home"><a href="/">Inicio</a> / <a href="/cursos/">Cursos</a> / <a href="/curso/<?php echo strtolower($fnc->Url($titulo)); ?>/<?php echo $id_curso; ?>/"><?php echo $titulo; ?></a><span id="rutas_info">Usted esta aqui</span></p>
</div>

<?php
// Mostramos las redes sociales
$template->mostrar_redes();
?>

<!-- codeando_4 -->
<ins class="adsbygoogle"
     style="display:block;margin-bottom:10px;"
     data-ad-client="ca-pub-0593566584451788"
     data-ad-slot="1010635212"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

<div id="detalles">
	<div id="detalles_1">
		<?php
		// Si hay contenido en la descripcion del curso la mostramos
		if(!empty($description)){
			?><h3>Descripción sobre el curso</h3>
			<p><?php echo $description; ?></p><?php
		}
		if(!empty($requeriment)){
			?><h3>Conocimientos y requisitos para tomar el curso</h3>
			<ul><?php echo $requeriment; ?>
			</ul><?php	
		}
		?>
		<h3>Temario del curso</h3>
		<?php
		// Obtenemos los capitulos del curso
		$result1 = $db->mysqli_select("SELECT id_capitulo,titulo FROM capitulos WHERE id_curso='$id_curso' AND visibility='YES' ORDER BY orden");

		$i = 1; // Contador para los capitulos

		// Mostramos los capitulos del curso
		while($row1 = $result1->fetch_assoc()){
			$ii = 1; // Contador para temas
			$cap_titulo = $row1['titulo'];
			$id_capitulo = $row1['id_capitulo'];

			?>
			<div class="capitulo">Capitulo <?php echo $i; ?> - <?php echo $cap_titulo; ?></div>
			<?php
			// Obtenemos los temas del capitulo
			$result2 = $db->mysqli_select("SELECT video,titulo,id_tema FROM temas WHERE id_curso='$id_curso' AND id_capitulo='$id_capitulo' AND visibility='YES' ORDER BY orden");

			// Mostramos los temas de cada capitulo
			while($row2 = $result2->fetch_assoc()){
				$tema_video = $row2['video'];
				$tema_titulo = $row2['titulo'];
				$tema_id = $row2['id_tema'];

				// Obtenemos el id del tema de introduccion
				if($i == 1 && $ii == 1){
					$video = $tema_video;
				}

				// Generamos la url del tema
				$tema_url = "/".strtolower($categoria)."/".strtolower($fnc->Url($tema_titulo))."/$tema_id/";

				?>
				<div class="temas"><a href="<?php echo $tema_url; ?>">Tema <?php echo $ii; ?> - <?php echo $tema_titulo; ?></a></div>
				<?php
				$ii++;
			}
			$result2->close();
			$i++;
		}
		$result1->close();
		?>
	</div>
	<div id="detalles_2">
		<div class="center">
			<img src="/img_curso/<?php echo $img; ?>" alt="logo" title="<?php echo $titulo; ?>">
		</div>

		<h3>Video de Introducción</h3>
		<div id='video'>
			<iframe width='560' height='315' src='//www.youtube.com/embed/<?php echo $video; ?>?rel=0&showinfo=0&theme=light&iv_load_policy=3&color=white' frameborder='0' allowfullscreen></iframe>
		</div>
		<h3>Unete al curso</h3>
		<?php
		// Obtenemos el totla de estudiantes del curso
		$result4 = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE id_curso='$id_curso'");
		$count4 = $result4->fetch_row();
		$result4->close();
		?>

		<p>Cuenta con una comunidad de <?php echo $count4[0]; ?> estudiantes, los cuales estaran dispuestos a ayudarte a resolver tus dudas y hacer crecer el conocimiento adquirido en el curso.</p>
		<div class="boton center">
		<?php
		// Mostramos boton de ingreso solo si inicio sesion el usuario
		if($_SESSION['logged_in']){
			// Verificamos si esta suscrito al curso
			$result_temp = $db->mysqli_select("SELECT Count(id_suscripcion) FROM suscripcion WHERE id_curso='$id_curso' AND user='$autor'");
			$count = $result_temp->fetch_row();
			$result_temp->close();

			if($count[0] > 0){
				?>
					<button class="icon-platform button login_in large left" onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</button>
				</div>
				<?php
			} else {
				?>
					<button id="boton_suscribir_<?php echo $id_curso; ?>" class="icon-platform button login_in large left" onclick="javascript:suscribir(<?php echo $id_curso; ?>)">Suscribirse al curso</button>
					<button id="boton_ingreso_<?php echo $id_curso; ?>" class="icon-platform button login_in large left boton_none" onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</button>
				</div>
				<?php
			}
		} else {
				?><p>Para unirte al curso debes iniciar sesion</p>
				<p><button id="login_button" class="icon-usuario button login_button large left">Inicio de sesion</button></p>
				<p><button id="boton_ingreso_<?php echo $id_curso; ?>" class="icon-platform button login_in large left boton_none" onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</button></p>
			</div><?php
		}
		?>
		<h3>Acerca del instructor</h3>
		<?php
		// Obtenemos los datos del instructor
		$result3 = $db->mysqli_select("SELECT fbid,avatar,nombre,bio,google,twitter FROM usuarios WHERE id='$autor' LIMIT 1");
		while($row3 = $result3->fetch_assoc()){
			$autor_name = $row3['nombre'];
			$autor_bio = (empty($row3['bio'])) ? '' : $row3['bio'];
			$autor_google = (empty($row3['google'])) ? '' : $row3['google'];
			$autor_twitter = (empty($row3['twitter'])) ? '' : $row3['twitter'];
			$autor_avatar = (empty($row3['avatar'])) ? '' : $row3['avatar'];
			$autor_fbid = (empty($row3['fbid'])) ? '' : $row3['fbid'];
		}
		$result3->close();
		?>
		<div id="instructor">
			<?php
			// Verificamos si iniciamos sesion con facebook
			if(!empty($autor_fbid)){
				?><img src="http://graph.facebook.com/<?php echo $autor_fbid; ?>/picture?type=large" alt="avatar"><?php
			} else {
				?><img src="/avatar/<?php echo $autor_avatar; ?>" id="header_avatar" alt="avatar"><?php
			}
			?>
			<p><?php echo $autor_name; ?></p>
		</div>
		<p><?php echo $autor_bio; ?></p>
		<div class="center">
			<?php
			// Si no esta vacio el ID de gogole mostramos
			if(!empty($autor_google)){
				?><div class="g-person" style="max-width:100%;" data-href="//plus.google.com/u/0/<?php echo $autor_google; ?>" data-theme="dark" data-layout="landscape" data-rel="author"></div><?php
			}
			// Si no esta vacio el ID de twitter mostramos
			if(!empty($autor_twitter)){
				?><div>
					<a href="https://twitter.com/<?php echo $autor_twitter; ?>" class="twitter-follow-button" data-show-count="false" data-lang="es" data-size="large">Seguir a @paulo_866</a>
				</div><?php
			}
			?>
		</div>
	</div>
</div>

<!-- Codeando_5 -->
<ins class="adsbygoogle"
     style="display:block;margin-top:10px;"
     data-ad-client="ca-pub-0593566584451788"
     data-ad-slot="9871034417"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>