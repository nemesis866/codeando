<?php
/************************************************
Clase para mostrar plugins de las redes sociales mas populares
__Nombre del proyecto__
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
************************************************/

class Social
{
	// Los parametros de la mayoria de las funciones estaran definidos de manera predeterminada, por si se olvida utilizarlos

	public function button_gplus($size = null, $view = null)
	{
		/*
		######### Configuraciones para el boton de google plus #########
		*
		* Para modificar el tamaño modificamos el valor del atributo data-size
		* >> small
		* >> medium
		* >> standard
		* >> tall
		* Para modificar el aspecto modificamos el valor del atributo data-annotation
		* >> inline (en la misma linea)
		* >> none (no la mostramos)
		* >> Eliminamos el atributo (lo mostramos arriba)
		*/

		// Convertimos a minusculas los parametros
		$size = strtolower($size);
		$view = strtolower($view);

		// Si el parametro $size no corresponde a ninguno de los valores establecidos, utilizamos el valor por defecto
		switch($size){
			case 'small':
				break;
			case 'medium':
				break;
			case 'standard':
				break;
			case 'tall':
				break;
			default:
				$size = null;
				break;
		}
		
		// Mostramos el boton segun la configuracion
		if($size == null && $view == null){
			// Si los dos parametros son nulos
			?><div class="g-plusone"></div><?php
		} else if($size == null) {
			// Solo el parametro size es nulo
			?><div class="g-plusone" data-annotation="<?php echo $view; ?>"></div><?php
		} else if($view == null){
			// Solo el parametro view es nulo
			?><div class="g-plusone" data-size="<?php echo $size; ?>"></div><?php
		} else {
			// Ninguno de los parametros es nulo
			?><div class="g-plusone" data-size="<?php echo $size; ?>" data-annotation="<?php echo $view; ?>"></div><?php
		}
	}
	public function button_twitter_share($size = null, $via = false, $keyword = false)
	{
		/*
		######### Configuraciones para el boton de twitter share (compartir) #########
		*
		* Para mostrarlo en ingles eliminamos el atributo data-lang="es"
		* Para mostrarlo de forma pequeña eliminamos el atributo data-size="large"
		* Para mostrar mensaje "via @usuario" añadimos el atributo data-via="usuario_twitter"
		* Para mostrar mensaje "recomended @usuario" añadimos el atributo data-related="usuario_twitter"
		* Para mostrar mensaje "#keyword" añadimos el atributo data-hashtags="keyword"
		*/

		// Convertimos a minusculas los parametros
		$size = strtolower($size);
		$via = strtolower($via);

		// En caso de que se utilize el @ junto el username de twitter lo eliminamos
		$via = str_replace('@', '', $via);

		// En caso de que se utilize el hashtag con numeral (#) lo eliminamos
		$keyword = str_replace('#', '', $keyword);

		// Si el parametro $size no corresponde a ninguno de los valores establecidos, utilizamos el valor por defecto
		if($size != 'large'){
			$size = null;
		}

		?>
		<a href="https://twitter.com/share" class="twitter-share-button" data-lang="es"
		<?php
		// configuramos las opciones
		if($size != null){
			echo "data-size='large'";
		}
		if($via != false){
			echo "data-via='$via'";
		}
		if($keyword != false){
			echo "data-hashtags='$keyword'";
		}
		?>
		>Twittear</a>
		<?php
	}
	public function button_twitter_follow($userName = 'programacionweb', $size = 'large', $name = true)
	{
		/*
		########## Configuraciones para el boton de twitter follow (seguir) #########
		*
		* Para mostrarlo en ingles eliminamos el atributo data-lang="es"
		* Para mostrarlo de forma pequeña eliminamos el atributo data-size="large"
		* Para no mostrar el usuario a seguir añadimos el atributo data-show-screen-name="false"
		*/

		// Convertimos a minusculas los parametros
		$userName = strtolower($userName);
		$size = strtolower($size);

		// En caso de que se utilize el @ junto el username de twitter lo eliminamos
		$userName = str_replace('@', '', $userName);

		// Si el parametro $size no corresponde a ninguno de los valores establecidos, utilizamos el valor por defecto
		switch($size){
			case 'large':
				break;
			case 'small':
				break;
			default:
				$size = null;
				break;
		}

		?>
		<a href="https://twitter.com/<?php echo $userName; ?>" class="twitter-follow-button" data-show-count="false" data-lang="es" 
		<?php
		if($size != null){
			// Mostramos el tamaño por defecto
			?>data-size="large"<?php
		}
		if($name == false){
			// No mostramos el nombre del usuario
			?>data-show-screen-name="false"<?php
		}
		?>
		></a>
		<?php
	}
	public function button_fb_share($type = 'button_count')
	{
		// Tipo de boton
		switch ($type) {
			case 'button_count':
				break;
			case 'button':
				break;
			case 'box_count':
				break;
			case 'link':
				break;
			case 'icon_link':
				break;
			case 'icon':
				break;
			default:
				$type = 'button_count';
				break;
		}

		// Utilizamos la url actual para comentar
		$url = '';
		if (isset($_SERVER['HTTPS'])) {
			// Codigo a ejecutar si se navega bajo entorno seguro.
			$url .= 'https://';
		} else {
			// Codigo a ejecutar si NO se navega bajo entorno seguro.
			$url .= 'http://';
		} 
		$url .= $_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];

		?>
		<div class="fb-share-button" data-href="<?php echo $url; ?>" data-layout="<?php echo $type; ?>"></div>
		<?php
	}
	public function button_fb_like($size = 'standard')
	{
		/*
		######## Configuraciones del boton de facebook like ########

		* Para mostrar imagenes de usuarios que les gusto modificamos el atributo data-show-faces a true
		* Para modificar el tamaño modificamos el valor del atributo data-layout
		* >> standard (descripcion a un lado)
		* >> box_count (Mostramos contador arriba)
		* >> button_count (Mostramos contador a un lado)
		* >> button (No mostramos contador y descripcion)
		*/

		// Convertimos a minusculas los parametros
		$size = strtolower($size);

		// Si el parametro $size no corresponde a ninguno de los valores establecidos, utilizamos el valor por defecto
		switch ($size) {
			case 'standard':
				break;
			case 'box_count':
				break;
			case 'button_count':
				break;
			case 'button':
				break;
			default:
				$size = 'standard';
				break;
		}

		?>
		<div class="fb-like" data-width="450" data-layout="<?php echo $size; ?>" data-show-faces="false" data-send="false"></div>
		<?php
	}
	public function button_fb_follow($user = 'paulo.andrade.5891', $size = 'standard')
	{
		/*
		######## Configuraciones del boton de facebook follow ########

		* El usuario a seguir lo colocamos como valor del atributo data-href
		* Para mostrar imagenes de usuarios que les gusto modificamos el atributo data-show-faces a true
		* Para modificar el tamaño modificamos el valor del atributo data-layout
		* >> standard (descripcion a un lado)
		* >> box_count (Mostramos contador arriba)
		* >> button_count (Mostramos contador a un lado)
		* >> button (No mostramos contador y descripcion)
		*/

		// Si el parametro $user contiene la direccion completa de la pagina la corregimos
		$user = str_replace('http://www.facebook.com/', '', $user);
		$user = str_replace('http://facebook.com/', '', $user);
		$user = str_replace('www.facebook.com/', '', $user);
		$user = str_replace('facebook.com/', '', $user);

		// Convertimos a minusculas los parametros
		$size = strtolower($size);

		// Si el parametro $size no corresponde a ninguno de los valores establecidos, utilizamos el valor por defecto
		switch ($size) {
			case 'standard':
				break;
			case 'box_count':
				break;
			case 'button_count':
				break;
			case 'button':
				break;
			default:
				$size = 'standard';
				break;
		}

		?>
		<div class="fb-follow" data-href="https://www.facebook.com/<?php echo $user; ?>" data-colorscheme="light" data-layout="<?php echo $size; ?>" data-show-faces="false"></div>
		<?php
	}
	public function button_fb_comment($size = '100%', $movil = 'Auto-detected', $comments = 5)
	{
		/*
		######### Configuraciones para mostrar el panel de comentarios en facebook #########

		* Con el atributo data-mobile lo configuramos para mostrar en formato movil con valor true o false
		* En el atributo data-numposts indicamos el numero de comentarios a mostrar
		* Con el atributo data-width indicamos el tamaño del plugin, si lo omitimos el tamaño sera fluido (100%)
		*/

		// Utilizamos la url actual para comentar
		$url = '';
		if (isset($_SERVER['HTTPS'])) {
			// Codigo a ejecutar si se navega bajo entorno seguro.
			$url .= 'https://';
		} else {
			// Codigo a ejecutar si NO se navega bajo entorno seguro.
			$url .= 'http://';
		} 
		$url .= $_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];

		// Si el parametro $comments no contiene un valor numerico, utilizamos el valor por defecto
		if(!is_numeric($comments)){
			$comments = 5;
		}
		?>

		<div class="fb-comments" data-href="<?php echo $url; ?>" data-numposts="<?php echo $comments; ?>" data-colorscheme="light" data-width="<?php echo $size; ?>" data-version="v2.3">
		</div>
		<?php
	}
	public function button_fb_page_like($user = 'programacion.azteca', $face = 'true', $border = 'true', $stream = 'true')
	{
		/*
		########  Configuraciones del plugin like para paginas (tipo box) ########

		* Mostramos usuarios que les gusta con el atributo data-show-faces con valor booleano
		* Mostramos cabecera del plugin con el atributo data-header con valor booleano
		* Mostramos el borde del plugin con el atributo data-show-border con valor booleano
		* Mostramos las ultimas publicaciones con el atributo data-stream con valor booleano
		*/

		// Si el parametro $user contiene la direccion completa de la pagina la corregimos
		$user = str_replace('http://www.facebook.com/', '', $user);
		$user = str_replace('http://facebook.com/', '', $user);
		$user = str_replace('www.facebook.com/', '', $user);
		$user = str_replace('facebook.com/', '', $user);

		// Si el parametro $face no corresponde a ninguno de los valores establecidos, utilizamos el valor por defecto
		switch($face){
			case 'true':
				break;
			case 'false':
				break;
			default:
				$face = 'false';
				break;
		}
		?>
		<div class="fb-like-box" data-href="http://www.facebook.com/<?php echo $user; ?>" data-colorscheme="light" data-show-faces="<?php echo $face; ?>" data-header="false" data-stream="<?php echo $stream; ?>" data-show-border="<?php echo $border; ?>"></div>
		<?php
	}
}