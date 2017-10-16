<?php
/************************************************
Clase principal (base) para la plataforma

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Configuramos la zona horaria
date_default_timezone_set('America/Mexico_City');

// Incluimos archivo de configuracion
require_once 'config.php';
// Incluimos las clases en caso de ser necesario
require_once 'Fnc.php';
require_once 'Social.php';

class Page
{
	/************************************************
	* Variables que utilizamos en el sistema
	************************************************/

	private $_analytics;
	private $_appId;
	private $_description;
	private $_email;
	private $_fbPermissions;
	private $_page;
	private $_premium;
	private $_return_url;
	private $_site_name;
	private $_scope;
	private $_title;

	public function __construct()
	{
		// Incluimos el archivo de  configuracion
		include $_SERVER['DOCUMENT_ROOT'].'/config.php';

		// Incluimos algunas variables de configuración
		$this->_email = $email;
		$this->_premium = $premium;

		// Vemos si el proyecto esta en desarrollo o produccion
		if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == $localhost){
			$this->_scope = 'desarrollo';
		} else {
			$this->_scope = 'produccion';
		}
	}

	/************************************************
	* Funciones para ingresar datos
	************************************************/

	// Datos API facebook connect
	public function set_fb($appId, $return_url, $fbPermissions)
	{
		$this->_appId = $appId;
		$this->_return_url = $return_url;
		$this->_fbPermissions = $fbPermissions;
	}
	public function set_analytics($var){ $this->_analytics = $var; }
	public function set_description($var){ $this->_description = $var; }
	public function set_page($var){ $this->_page = $var; }
	// Muestra el titulo de una pagina, si no es asignado muestra uno por defecto
	public function set_title($var)
	{
		if(empty($var)){
			$this->_title = 'Plataforma de cursos - Open source';
		} else {
			$this->_title = $var;
		}
	}
	// Muestra el nombre del sitio web, si no es asignado muestra uno por defecto
	public function set_site_name($var)
	{
		if(empty($var)){
			$this->_site_name = 'Sistema de plantilla php';
		} else {
			$this->_site_name = $var;
		}
	}

	public function display_head()
	{
		// Obtenemos parametros
		$page = (empty($_GET['page'])) ? '' : $_GET['page'];
		$id_tema = (empty($_GET['id_tema'])) ? '' : $_GET['id_tema'];

		// Seguridad
		if(empty($_SESSION['logged_in'])){
			$_SESSION['logged_in'] = false;
		}

		$db = new Db();
		$fnc = new Fnc();
		?>

		<!DOCTYPE html>
		<html lang="es">
	    <head>
	        <meta charset="utf-8">
	        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	        <title><?php echo $this->_title; ?></title>
	        <?php
        	// Si no tenemos una descripcion para la pagina, omitimos la meta etiqueta
        	if(!empty($this->_description)){
        		?>
        		<meta name="description" content="<?php echo $this->_description; ?>">
        		<?php
        	}
        	// Creamos etiquetas meta para los articulos
        	if(!empty($id_tema)){
        		// Obtenemos los datos del tema
        		$result = $db->mysqli_select("SELECT info,doc,titulo,id_curso FROM temas WHERE id_tema='$id_tema' LIMIT 1");
        		while($row = $result->fetch_assoc()){
        			$info = (empty($row['info'])) ? '' : $row['info'];
        			$doc = (empty($row['doc'])) ? '' : $row['doc'];
        			$titulo = $row['titulo'];
        			$id_curso = $row['id_curso'];
        		}
        		$result->close();

        		// Obtenemos los detalles del curso
				$result2 = $db->mysqli_select("SELECT categoria,img FROM cursos WHERE id_curso='$id_curso'");
				while($row2 = $result2->fetch_assoc()){
					$categoria = $row2['categoria'];
					$img = $row2['img'];
				}
				$result2->close();
      			?>
      			<meta name="keywords" content="<?php echo $fnc->keywords(strtolower($fnc->sanear_string($doc))) ;?>">

      			<!-- Twitter Card data //-->
				<meta name="twitter:card" value="summary">

      			<!-- Open Graph data //-->
      			<meta property="og:title" content="<?php echo $titulo; ?> | Codeando.org">
      			<meta property="og:type" content="article">
				<meta property="og:url" content="http://codeando.org/<?php echo strtolower($categoria); ?>/<?php echo strtolower($fnc->Url($titulo)); ?>/<?php echo $id_tema; ?>/">
				<meta property="og:image" content="http://codeando.org/img_curso/<?php echo $img; ?>">
				<meta property="og:description" content="<?php echo $this->_description; ?>">

				<link rel="canonical" href="http://codeando.org/<?php echo strtolower($categoria); ?>/<?php echo strtolower($fnc->Url($titulo)); ?>/<?php echo $id_tema; ?>/">
      			<?php
        	}
        	?>

        	<meta name="author" content="Paulo.Andrade.1">
			<meta name="owner" content="Codeando">
			<meta name="robots" content="index, follow">

			<link rel="stylesheet" type="text/css" href="/css/min/main.css">
			<?php
			// Cargamos los archivos css necesarios segun la pagina en la que estemos
			if($page == 'platform'){
				?><link rel="stylesheet" type="text/css" href="/css/min/platform.css"><?php	
			} else {
				?><link rel="stylesheet" type="text/css" href="/css/min/inicio.css"><?php
			}
			?>
			<link rel="SHORTCUT ICON" href="/favicon.ico">
			<base href="/">
	    </head>
	    <body id="<?php echo $this->_page; ?>" ng-app="platform">
		<?php
		// Mostramos pantalla cargando solo al ingresar a la Plataform
		 
		if($this->_page == 'plataforma'){
			?>
			<div id='intro'>
				<div class="loader loader-white"></div>
				<div id="intro_text"><p>Please wait</p><p><span>Loading workspace</span></p></div>
			</div>
			<?php
		}
	
    	// Si existe un App ID Facebook cargamos el SDK Facebook
    	if(!empty($this->_appId)){
	    	?>
	    	<div id="fb-root"></div>
	    	<script>
	    	function checkLoginState(page) {
				FB.login(function(response) {
					if (response.status === 'connected') {
						// Logueado en facebook y en la APP
						testAPI(page);
					}
				},{
					scope: 'email,user_friends,public_profile'
				});
			}	

			window.fbAsyncInit = function() {
				FB.init({
					status     : true,
					appId      : '<?php echo $this->_appId; ?>',
					cookie     : true,
					xfbml      : true,
					version    : 'v2.0'
				});
				/*
				FB.getLoginStatus(function(response) {
					statusChangeCallback(response);
					});
				*/
			};

			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/es_LA/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
			</script>
			<?php 
		}
	}
	// Mostramos el menu de la plataforma
	public function display_nav()
	{
		?>
		<nav>
			<ul>
				<li><a href="/">Inicio</a></li>
				<li><a href="/quienes_somos/">Quienes somos?</a></li>
				<li><a href="/servicios/">Servicios</a></li>
				<li><a href="/contactanos/">Contactanos</a></li>
			</ul>
		</nav>
		<?php
	}
	// Mostramos la cabecera de la plataforma
	public function display_header()
	{
		?>
		<div id="page_1">
			<header>
				<a href="/cursos/" title="Codeando.org">
					<img class="logo" src="/img/logo_opt.png" alt="codeando" title="Codeando.org">
				</a>
				<div id="header_1">
					<p>Codeando.org</p>
				</div>
				<div id="header_2">
					<a href="http://programacionazteca.mx" title="Programación Azteca" target="_blank">
						<img class="logo_azteca" src="/img/logo_azteca.png" alt="Programacion Azteca" title="Programacion Azteca">
					</a>
					<p>Un proyecto de Programación Azteca</p>
				</div>
				<div id="header_3">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" title="Te sirvio la plataforma!, puedes comprarnos un caffe :)">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="T46F9NWENQ86A">
						<input type="image" src="https://www.paypalobjects.com/es_XC/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal, la forma más segura y rápida de pagar en línea.">
						<img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
					</form>

				</div>
				<div id="header_4">
					<?php
					// Obtenemos el avatar del usuario
					if($_SESSION['logged_fb'] == true){
						?><img id="img_autor" class="avatar" src="http://graph.facebook.com/<?php echo $_SESSION['user_id']; ?>/picture?type=large" alt="avatar" title="<?php echo $_SESSION['nombre']; ?>" onclick="javascript:user_mostrar(<?php echo $_SESSION['id']; ?>)"><?php
					} else {
						?><img id="img_autor" class="avatar" src="/avatar/<?php echo $_SESSION['avatar']; ?>" alt="avatar" title="<?php echo $_SESSION['nombre']; ?>" onclick="javascript:user_mostrar(<?php echo $_SESSION['id']; ?>)"><?php
					}
					?>
					<span id="menu" class="icon icon-menu"></span>
					<span class="icon icon-alerta" title="Notificaciones" onclick="javascript:alerta()">
						<span id="notificacion"></span>
					</span>
				</div>
			</header>
			<div id="menu_principal">
				<div class="labels">
					<ul>
						<li title="Documentacion" class="items" data-type="1" onclick="javascript:menu_router(1)"><span class="icon-doc"></span></li><!--
						--><li title="Discusiones" class="items" data-type="2" onclick="javascript:menu_router(2)"><span class="icon-foro"></span></li><!--
						--><li title="Notas" class="items" data-type="3" onclick="javascript:menu_router(3)"><span class="icon-notas"></span></li><!--
						--><li title="Usuarios" class="items" data-type="4" onclick="javascript:menu_router(4)"><span class="icon-user"></span></li>
					</ul>
				</div>
				<div id="menu_content">
				</div>
			</div>
			<div id="menu_discucion">
				<div class="w_back icon-right-dis" onclick="javascript:dis_back()">Regresar</div>
				<div class="cargando"></div>
				<div id="dis_content"></div>
				<div id="res_content"></div>
				<div id='res_form' style='display:none;'>
					<form id='form_res'>
						<div id='resp_toolbox'>
							<span onclick='javascript:toolbox(1,"content_res")' class='icon-code' title='Insertar codigo'>CODE</span>
							<span onclick='javascript:toolbox(2,"content_res")' class='icon-bold' title='Negrita'><strong></strong></span>
							<span onclick='javascript:toolbox(3,"content_res")' class='icon-italic' title='Cursiva'><i></i></span>
							<span onclick='javascript:toolbox(4,"content_res")' class='icon-underline' title='Subrayado'><u></u></span>
							<span onclick='javascript:toolbox(5,"content_res")' class='icon-strike' title='Tachado'></span>
						</div>
						<textarea id='content_res' placeholder='Tienes alguna respuesta? publicala aqui' onclick='javascript:mostrar_res()'></textarea>
					</form>
					<p>
						<button id='submit' onclick='javascript:res_publicar()'>Publicar</button>
						<span id='form_res_info'></span>
					</p>
				</div>
			</div>
			<div id="menu_notas">
				<div class="w_back icon-right-dis" onclick="javascript:nota_back()">Regresar</div>
				<div class="cargando"></div>
				<div id="nota_content"></div>
			</div>
			<div id="alertas">
				<div id="alert"></div>
				<div id='not_cargar'>
					<button onclick='javascript:notificacion_cargar()' class='icon-reload'>Notificaciones leidas</button>
					<div class='cargando'></div>
				</div>
			</div>
			<div id="mostrar_menu">
				<span class="icon-right"></span>
			</div>
			<div class="beta">
				BETA
			</div>
		</div>
		<?php
	}
	public function display_content()
	{
		?>
		<!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <p>Hello world! This is HTML5 Boilerplate.</p>
		<?php
		// Mostramos el boton de login de facebook
		self::login_fb();

		// ######## Cargamos la clase social para los plugins relacionados con redes sociales #########
		$social = new Social();
		// Mostramos el boton de gplus
		$social->button_gplus();
		// Mostramos el boton de twitter
		$social->button_twitter_share();
		// Mostramos el boton de twitter seguir
		$social->button_twitter_follow();
		// Mostramos el boton de facebook like
		$social->button_fb_like();
		// Mostramos el boton de facebook share
		$social->button_fb_follow();
		// Mostramos area de comentarios para facebook
		$social->button_fb_comment();
		// Mostramos area para like a paginas
		$social->button_fb_page_like();
	}
	public function display_footer()
	{
		if(empty($_GET['page'])){ $page = '';} else { $page = addslashes($_GET['page']);}

		$db = new Db();
		$fnc = new Fnc();
		?>
		</section>
		<?php
		if($page != 'platform'){
			?>
			<footer>
				<div id="footer_1">
					<h3>Secciones:</h3>
					<ul>
						<li><a href="/">Inicio</a></li>
						<li><a href="/cursos/">Cursos</a></li>
						<li><a href="http://blog.codeando.org">Blog</a></li>
						<?php
						// Verificamos si esta activada la zona premium
						if($this->_premium){
							?>
							<li><a href="/premium/">Contenido exclusivo</a></li>
							<?php
						}
						?>
						<li><a href="/contacto/">Contactanos</a></li>
					</ul>
					<h3>Sitios de interes:</h3>
					<ul>
						<li><a href="http://programacionazteca.mx">Programación Azteca</a></li>
						<li><a href="http://youtube.com/channel/UCS5t7Ynr2sPoWgUfsYHrksA">Youtube</a></li>
						<li><a href="http://github.com/programacionazteca">Github</a></li>
					</ul>
				</div>
				<div id="footer_2">
					<?php
					// Obtenemos los cursos disponibles en la plataforma
					$result = $db->mysqli_select("SELECT * FROM cursos WHERE public='YES' ORDER BY titulo");
					$count = $result->num_rows;

					// Verificamos si hay cursos
					if($count > 0){
						?><h3>Cursos disponibles:</h3>
						<ul><?php

						while($row = $result->fetch_assoc()){
							// Obtenemos las variables a utilizar
							$id_curso = $row['id_curso'];
							$img = $row['img'];
							$titulo = $row['titulo'];

							// Creamos la url del curso
							$url_temp = strtolower($fnc->Url($titulo));
							$url = "/curso/$url_temp/$id_curso/";
							?><li><a href="<?php echo $url; ?>"><?php echo $titulo; ?></a></li><?php
						}
						?></ul><?php
					}
					?>
				</div>
				<div id="footer_3">
					<a href="http://programacionazteca.mx" rel="nofollow"><img src="/img/logo_azteca.png"></a>
					<div id="footer_3_text">
						<p>Un proyecto de<br>
						<a href="http://programacionazteca.mx" rel="nofollow">PROGRAMACION AZTECA</a></p>
					</div>
				</div>
				<div>
					<p> <?php echo $this->_site_name.' '.date('Y'); ?></p>
					<p>Un proyecto de <a href="http://programacionazteca.mx" rel="nofollow">Programación Azteca</a></p>
				</div>
				<div id="img_count"></div>
			</footer>
			<div id="login_box">
				<div id="login_close" title="Cerrar">X</div>
				<?php require_once 'include/html_login.php'; ?>
			</div>
			<div id="menu-contenido">
				<ul>
					<li><a href="/">Inicio</a></li>
					<li><a href="/cursos/">Cursos</a></li>
					<li><a href="http://blog.codeando.org">Blog</a></li>
					<?php
					// Verificamos si esta activada la zona premium
					if($this->_premium){
						?>
						<li><a href="/premium/">Contenido exclusivo</a></li>
						<?php
					}
					?>
					<li><a href="/contacto/">Contactanos</a></li>
					<?php
					// Mostramos url al admin
					if($_SESSION['logged_in']){
						?>
						<li><a href="/admin-co/">Admin</a></li>
						<?php
					}
					?>
					<div class="center">
						<li><a id="menu_cerrar">Cerrar</a></li>
					</div>
				</ul>
			</div>
			<?php
		}
		?>
		<div class="error"></div>
		<div class="success"></div>

		<script type="text/javascript" src="/js/vendor/modernizr-2.6.2.min.js"></script>
		<script type="text/javascript" src="/js/vendor/prefixfree-1.0.7.min.js"></script>
		<!--[if lt IE 9]>
			<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php
		// Verificamos si estamos en producción
		if($this->_scope == 'produccion'){
			?>
			<script type="text/javascript" src="/js/min/fnc.js"></script>
			<script type="text/javascript" src="/js/vendor/facebook.js"></script>
			<script type="text/javascript" src="/js/min/files.js"></script>
			<script type="text/javascript" src="/js/min/files_dis.js"></script>
			<?php
			if($page == 'platform'){
				?><script type="text/javascript" src="/js/min/main.js"></script><?php
			} else {
				?><script type="text/javascript" src="/js/min/inicio.js"></script><?php
			}
			if(!empty($this->_appId)){ ?>
			<?php } if(!empty($this->_analytics)){
				// Si se ejecuta en local, se desactiva google analytics
				if($_SERVER['SERVER_NAME'] != "127.0.0.1" || $_SERVER['SERVER_NAME'] != 'localhost' || $_SERVER['SERVER_NAME'] == 'codeando.dev'){
					?><script src="/js/min/analytics.js"></script><?php
				}
			} ?>
			<script src="/js/min/social.js"></script>
		<?php } else { ?>
			<script type="text/javascript" src="/js/fnc.js"></script>
			<script type="text/javascript" src="/js/vendor/facebook.js"></script>
			<script type="text/javascript" src="/js/files.js"></script>
			<script type="text/javascript" src="/js/files_dis.js"></script>
			<?php
			if($page == 'platform'){
				?><script type="text/javascript" src="/js/main.js"></script><?php
			} else {
				?><script type="text/javascript" src="/js/inicio.js"></script><?php
			}
			?>
			<script src="/js/social.js"></script>
		<?php } ?>
		<script>
		window.___gcfg = {lang: 'es'};
		
		(function() {
			var js,
				s = document.getElementsByTagName("script")[0],
				add = function(url,id){
				if(document.getElementById(id)){ return; }
					js = document.createElement("script");
					js.async = true;
					js.src = url;
					s.parentNode.insertBefore(js, s);
				};

			add("//apis.google.com/js/platform.js","perfil");
			add("//apis.google.com/js/plusone.js","plus");
			add("//platform.twitter.com/widgets.js","twitter-wjs");
		})();

		// Google analytics
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo $this->_analytics; ?>', 'auto');
		ga('send', 'pageview');
		</script>
		</body>
		</html>
		<?php
	}
	public function display()
	{
		self::display_head();
		$this->display_nav();
		$this->display_header();
		?>
		<section id="wrapper">
		<div id="content"><?php
		$this->display_content();
		?></div><?php
		self::display_footer();
	}
	public function login_fb()
	{
		if($_SESSION['logged_in'] == false){ ?>
			<div id="results"></div>
			<div id="LoginButton">
			    <div class="fb-login-button" onlogin="javascript:CallAfterLogin();" size="large" scope="<?php echo $this->_fbPermissions; ?>">Iniciar sesion con Facebook</div>
			</div>
			<?php
		} else {
			if($_SESSION['gender'] == 'male'){
				$return = 'BIENVENIDO';
			} else {
				$return = 'BIENVENIDA';
			}
			?>
			<div id="header_2_a">
			<img src="http://graph.facebook.com/<?php echo $_SESSION['user_id']; ?>/picture?type=large" alt="avatar">
			</div>
			<div id="header_2_b">
			<p><?php echo $return.' '.strtoupper($_SESSION['user_name']); ?></p>
			<p><a href="?logout=1">Salir</a>
			<?php if($_SESSION['nivel'] == 10){
				?>
				<a href="/admin/">Admin</a>
				<?php
			}
			?>
			</p>
			</div>
			<?php
		}
	}
}