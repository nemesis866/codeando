<?php
/************************************************
Clase que contiene funciones escenciales

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

class Fnc
{
	public function date_display($fecha)
	{
		// Funcion que da formato a fecha ingresada por medio del calendario
		$data = explode("-", $fecha);
		echo $data[2]." / ".$data[1]." / ".$data[0];
	}
	public function date_display_jornada($fecha)
	{
		// Funcion que da formato a fecha ingresada por medio del calendario
		$data = explode("-", $fecha);
		echo $data[2]."/".$data[1]."/".$data[0];
	}
	public function tema_replace($content)
	{
		// Funcion que regresa a su estado original la informacion del tema
		$codigo = array("&lt;","&gt;","<br>","<pre class='code'>","</pre>","<b>","</b>","<i>","</i>","<u>","</u>","<strike>","</strike>");
		$replace = array("<", ">","\n", "[code]", "[/code]", "[b]", "[/b]", "[I]", "[/I]","[u]", "[/u]","[t]","[/t]");
		
		for($i = 0; $i < count($codigo); $i++){
			$content = str_replace($codigo[$i], $replace[$i], $content);
		}

		return $content;
	}
	public function youtube_url($url)
	{
    	preg_match('#^(?:https?://)?(?:www\.)?(?:youtube\.com(?:/embed/|/v/|/watch\?v=))([\w-]{11})(?:.+)?$#x', $url, $r); 
    	//print_r($r);

    	return (isset($r[1]) && !empty($r[1])) ? $r[1] : false; 
	}
	public function github_url($url)
	{
		// Comprobamos si la url pertece a github

		$result = false;
		$protocolos = array('http://', 'https://', 'ftp://', 'www.');
	    $dominio = explode('/', str_replace($protocolos, '', $url));
	    
	    // Comprobamos si la url pertenece a github
	    if($dominio[0] == 'github.com'){
	    	// Comprobamos si la url existe
	    	$handle = @fopen($url, "r");
			if ($handle == false){
			    $result = false;
			}
			@fclose($handle);
	    	$result = true;
	    }

		// Regresamos true si se trata de una url de github
		return $result;
	}
	public function youtube_video($id)
	{
		// Url del video
		$url = "http://gdata.youtube.com/feeds/api/videos/$id";

		// obtenemos el feed y lo convertimos en objeto
		$data = simplexml_load_file($url);

		// Obtenemos los datos del video
		$title = $data->title;

		// Regresamos datos en formato json y dejamos de ejecutar el script
		return $title;
	}
	public function FechaCOM ($fecha)
	{
		//Funcion que combierte la hora de ingles a español para los comentarios
		$data = explode("-",$fecha);
		$data1 = explode(" ",$data[2]);
		$data2 = explode(":",$data1[1]);
		$retval = $data2[0].":".$data2[1]." ".$data1[0]."/".$data[1]."/".$data[0];
		return $retval;
	}
	public function FechaUSER ($fecha)
	{
		//Funcion que combierte la hora de ingles a español para los comentarios
		$data = explode("-",$fecha);
		$data1 = explode(" ",$data[2]);
		$data2 = explode(":",$data1[1]);
		switch ($data[1]) {
			case 1: $data[1] = 'Enero'; break;
			case 2: $data[1] = 'Febrero'; break;
			case 3: $data[1] = 'Marzo'; break;
			case 4: $data[1] = 'Abril'; break;
			case 5: $data[1] = 'Mayo'; break;
			case 6: $data[1] = 'Junio'; break;
			case 7: $data[1] = 'Julio'; break;
			case 8: $data[1] = 'Agosto'; break;
			case 9: $data[1] = 'Septiembre'; break;
			case 10: $data[1] = 'Octubre'; break;
			case 11: $data[1] = 'Noviembre'; break;
			case 12: $data[1] = 'Diciembre'; break;
		}
		$retval = $data1[0]." de ".$data[1]." ".$data[0]." a las ".$data2[0].":".$data2[1]." Hrs.";
		return $retval;
	}
	public function html_replace($content)
	{
		$codigo = array("[-","-]","&-","--|","-&","text/htm","--&gt;");
		$replace = array("&lt;","&gt;","(","))",")","text/html","-->");
		
		for($i = 0; $i < count($codigo); $i++){
			$content = str_replace($codigo[$i], $replace[$i], $content);
		}

		return $content;
	}
	public function mostrar_html($content)
	{
		$codigo = array("[-","-]","[code]", "[/code]", ")gt;","-&gt;", "-(&gt;", "--&gt;", "&-", "-&");
		$replace = array("&lt;","&gt;","<pre class='code'>","</pre>", "->", "->", ")->","-->", "(", ")");

		// Ejecutamos solo si no esta vacio $content
		if(!empty($content)){
			for($i = 0; $i < count($codigo); $i++){
				$content = str_replace($codigo[$i], $replace[$i], $content);
			}
		}

		return $content;
	}
	public function code($content)
	{	
		$codigo = array("[code]","[cod]","[co","[/code]","[/code","[/cod","[/co","[/c","[/");
		$replace = array(" "," "," "," "," "," "," "," "," ");

		for($i = 0; $i < count($codigo); $i++){
			$content = str_replace($codigo[$i], $replace[$i], $content);
		}

		return $content;
	}
	// Funcion para las url dinamicas
	public function Url($url)
	{
		$url = $this->sanear_string($url);

		$data = explode(' ', $url);
		$count = count($data);
		$mostrar = '';

		for($i=0; $i < $count; $i++){
			if($i == ($count - 1)){
				$mostrar.= $data[$i];
			} else {
				$mostrar.= $data[$i].'-';
			}
		}

		return $mostrar;
	}
	// Funcion que reemplaza los saltos de linea
	public function mostrar_curso($contenido)
	{
		$contenido = str_replace('<br>', '\n', $contenido);

		return $contenido;
	}
	// Funcion que quita acentos y caracteres especiales
	public function sanear_string($string)
	{
		//$string = trim($string);
		$string = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string);
		$string = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string);
		$string = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string);
		$string = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string);
		$string = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string);
		$string = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string );

		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(array("\\", "¨", "º", "-", "~", "#", "@", "|", "!", "\"", "·", "$", "%", "&", "/", "(", ")", "?", "'", "¡", "¿", "[", "^", "`", "]", "+", "}", "{", "¨", "´", ">“, “< ", ";", ",", ":", "."), '', $string);

		return $string;
	}
	// Funcion que reemplaza los enlaces planos por enlaces html
	public function url_replace($text)
	{
	    $regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';

	    return preg_replace_callback($regex, function ($matches) {
	        return "<a href='{$matches[0]}' target='_blank'>{$matches[0]}</a>";
	    }, $text);
	}
	// Funcion para desinfectar peticiones sql
	public function secure_sql($valor)
	{
		$valor = htmlspecialchars(stripslashes($valor));
        // $valor = str_ireplace("script", "blocked", $valor);
        $valor = mysql_escape_string($valor);

		return $valor;
	}
	// Funcion que genera las palabras claves
	public function keywords($texto,$cantidad=15)
	{
		$keywords = array();
		$palabrasNoConsideradas = 'que,qué,cuán,cuan,los,las,una,unos,unas,donde,dónde,como,cómo,cuando,porque,por,para,según,sin,con,mas,más,pero,del,sus,esta,cambio,cada,solo,desde,nosotros,cual,nos,les,tal,tener,nuestra,algo,esto,puedes,sea,podemos,poco,fue,necesitamos,necesito,necesita,los,han,an,dicha,ver,0,dado,somos,ajena,tan,muy,ser,esto,ello,puede,este,tiene';
		$palabrasNoConsideradas = explode(',',$palabrasNoConsideradas);

		$texto = strip_tags(html_entity_decode($texto,ENT_NOQUOTES,'UTF-8'));
		$texto = preg_replace(array('/\s+/'),' ',$texto);
		$texto = preg_replace(array('/\r/', '/\n/','/[¿!¡;,:\.\?#@()"]/'),'',$texto);
		$texto = explode(' ',$texto);
		$palabras = array();

		foreach($texto as $palabra){
			if(ctype_upper($palabra) and count($keywords)<$cantidad and !in_array($palabra,$keywords)){
				array_push($keywords,$palabra); //las palabras en mayúscula son keywords automáticamente
			} else {
				if(strlen($palabra)>2 and !in_array($palabra,$palabrasNoConsideradas)){
					if(!empty($palabras[$palabra])){
						$palabras[$palabra]++;
					} else {
						$palabras[$palabra]=1;
					}
				}
			}
		}

		arsort($palabras);
		$palabras = array_keys(array_slice($palabras,0,$cantidad-count($keywords)));
		$keywords = array_merge($palabras,$keywords);

		return implode(' ,',$keywords);
	}
}