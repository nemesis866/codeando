<?php
/************************************************
Archivo para mostrar los temas de cada curso

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Obtenemos las variables
$id_tema = (empty($_GET['id_tema'])) ? '' : $_GET['id_tema'];

// Seguridad
if(empty($_SESSION['logged_in'])){
	$_SESSION['logged_in'] = false;
}

// Declaramos los objetos
$db = new Db();
$fnc = new Fnc();
$template = new Template();
$social = new Social();

// Obtenemos los detalles del tema
$result = $db->mysqli_select("SELECT id_curso,titulo,autor,info,doc,video,github FROM temas WHERE id_tema='$id_tema'");
while($row = $result->fetch_assoc()){
	$titulo = $row['titulo'];
	$autor = $row['autor'];
	$id_curso = $row['id_curso'];
	$info = (empty($row['info'])) ? '' : $row['info'];
	$doc = (empty($row['doc'])) ? '' : $row['doc'];
	$video = (empty($row['video'])) ? '' : $row['video'];
	$github = (empty($row['github'])) ? '' : $row['github'];
}
$result->close();

// Obtenemos los detalles del autor
$result1 = $db->mysqli_select("SELECT nombre,avatar,fbid,bio,google,twitter FROM usuarios WHERE id='$autor' LIMIT 1");
while($row1 = $result1->fetch_assoc()){
	$autor_nombre = $row1['nombre'];
	$autor_fbid = (empty($row1['fbid'])) ? '' : $row1['fbid'];
	$autor_avatar = (empty($row1['avatar'])) ? '' : $row1['avatar'];
	$autor_bio = (empty($row1['bio'])) ? '' : $row1['bio'];
	$autor_google = (empty($row1['google'])) ? '' : $row1['google'];
	$autor_twitter = (empty($row1['twitter'])) ? '' : $row1['twitter'];
	$autor_img = '';
}	
$result1->close();

// Generamos avatar para el autor
if(empty($autor_fbid) || $autor_fbid == 0){
	// Generamos avatar normal
	$autor_img = "<img src='/avatar/$autor_avatar' alt='avatar'>";
} else {
	// Generemoas avatar de fb
	$autor_img = "<img src='http://graph.facebook.com/$autor_fbid/picture?type=large' alt='avatar'>";
}

// Obtenemos los detalles del curso
$result2 = $db->mysqli_select("SELECT titulo,categoria FROM cursos WHERE id_curso='$id_curso'");
while($row2 = $result2->fetch_assoc()){
	$curso_titulo = $row2['titulo'];
	$curso_categoria = $row2['categoria'];
}
$result2->close();

$tema_video = '';
$tema_info = '';
$tema_doc = '';
$tema_github = '';

// Si hay video lo configuramos
if(!empty($video)){
	$tema_video = "<div id='video'>
				<iframe width='560' height='315' src='//www.youtube.com/embed/$video?rel=0&showinfo=0&theme=light&iv_load_policy=3&color=white' frameborder='0' allowfullscreen></iframe>
			</div>";
}

// Verificamos si hay informacion sobre el tema
if(!empty($info)){
	$tema_info = "<h2>Información:</h2><p>".$fnc->url_replace($fnc->mostrar_html($info))."</p>";
}

// Verificamos si hay informacion sobre el tema
if(!empty($doc)){
	$tema_doc = "<h2>Documentación:</h2><p>".$fnc->url_replace($fnc->mostrar_html($doc))."</p>";
}

// Verificamos si hay enlace a github
if(!empty($github)){
	$tema_github = "<h2>Repositorio en github</h2>
			<a href='$github' id='github_a' target='_blank'>
				<div id='github' class='icon-github'>
					Accede al repositorio del tema en github
				</div>
			</a>";
}
?>

<div id="informacion" class="parallax">
	<h1><span><?php echo strtoupper($titulo); ?></span></h1>
	<p><span><strong>Autor</strong>: <?php echo $autor_nombre; ?></span></p>
	<p><span><strong>Curso</strong>: <?php echo $curso_titulo; ?></span></p>
	<p><span><strong>Categoria</strong>: <?php echo $curso_categoria; ?></span></p>
</div>

<div id="tomar_curso">
	<img src="/img/curso.png">
	<p>Ingresa a.- "<strong><?php echo $curso_titulo; ?></strong>" es gratuito 
		<?php if(!$_SESSION['logged_in']){
			?>
			<span id="login_button" class="icon-usuario login_button">Inicie sesion para ingresar</span>
			<span id="login_init" class="none login_in" onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</span></p>
			<?php
		} else {
			?>
			<span class="login_in" onclick="javascript:router('plataforma',<?php echo $id_curso; ?>)">Ingresar al curso</span></p>
			<?php
		}
		?>
</div>

<section id="wrapper">
<div id="content">

<div id="rutas">
	<p class="icon-home"><a href="/">Inicio</a> / <a href="/cursos/">Cursos</a> / <a href="/curso/<?php echo strtolower($fnc->Url($curso_titulo)); ?>/<?php echo $id_curso; ?>/"><?php echo $curso_titulo; ?></a> / <a href="/<?php echo strtolower($curso_categoria); ?>/<?php echo strtolower($fnc->Url($titulo)); ?>/<?php echo $id_tema; ?>/"><?php echo $titulo; ?></a><span id="rutas_info">Usted esta aqui</span></p>
</div>

<?php
// Mostramos redes
$template->mostrar_redes();
?>

<div id="temas">
	<!-- Codeando_1 -->
	<ins class="adsbygoogle"
	     style="display:block;margin-bottom:15px;"
	     data-ad-client="ca-pub-0593566584451788"
	     data-ad-slot="7755172814"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>

	<div id="fondo">
		<?php echo $tema_video; ?>
	</div>

	<!-- Codeando_3 -->
	<ins class="adsbygoogle"
	     style="display:block;margin-top:15px;"
	     data-ad-client="ca-pub-0593566584451788"
	     data-ad-slot="8952704417"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>

	<div id="doc">
		<?php echo $tema_doc; ?>
	</div>

	<!-- codeando_2 -->
	<ins class="adsbygoogle"
	     style="display:block;margin-top:15px;"
	     data-ad-client="ca-pub-0593566584451788"
	     data-ad-slot="4522504814"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>

	<div id="info">
		<?php echo $tema_info; ?>
	</div>
	<div>
		<?php echo $tema_github ?>
	</div>

	<div id="perfil" class="center">
		<div id="perfil_1">
			<?php echo $autor_img; ?>
		</div>
		<div id="perfil_2">
			<p><?php echo $autor_bio; ?></p>
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

	<div class="center">
		<h2>Comentarios</h2>
		<?php
		// Incluimos comentarios con ayuda de facebook
		$social->button_fb_comment();
		?>
	</div>

	<div id="foro">
		<p><a href="http://programacionazteca.mx/foro/inicio/">Tienes dudas? puedes pedir ayuda en nuestro foro.</a></p>
		<p><img src="/img/forum.png"></p>
	</div>
</div>