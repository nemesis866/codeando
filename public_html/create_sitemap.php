<?php
/********************************************************************
Script ejecutado por un cron job para la creacion del sitemap de
Codeando.org

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

// Indicamos que no exista limite de tiempo de ejecucion en este script
set_time_limit(0);

// Incluimos librerias para conectarnos ala base de datos
require_once 'config.php';
require_once 'include/Db.php';
require_once 'include/Fnc.php';

$db = new Db();
$fnc = new Fnc();

// Obtenemos el total de cursos
$result = $db->mysqli_select("SELECT titulo,categoria,id_curso FROM cursos WHERE public='YES'");
$count = $result->num_rows;

// Obtenemos el total de temas
$result1 = $db->mysqli_select("SELECT id_tema,id_curso,titulo FROM temas WHERE visibility='YES'");
$count1 = $result1->num_rows;

// Obtenemos la suma de cursos e items
$total = $count + $count1;

// Verificamos si existe la tabla si no la creamos
$insert = $db->mysqli_action("CREATE TABLE IF NOT EXISTS `sitemap` (`id` int(10) NOT NULL AUTO_INCREMENT,`registro` int(10) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0");

// Consultamos que exista un cambio en el total
$result2 = $db->mysqli_select("SELECT registro FROM sitemap WHERE id='1'");
$count2 = $result2->num_rows;

// Verificamos si ya existe
if($count2 == 0){
	// Si no existe registro lo insertamos
	$insert1 = $db->mysqli_action("INSERT INTO sitemap (registro) VALUES ('$total')");
} else {
	// Si existe consultamos el registro
	while($row2 = $result2->fetch_assoc()){
		$count2 = $row2['registro'];
	}
	$result2->close();

	// Verificamos los totales
	if($count2 != $total){
		// Actualizamos el registro
		$update = $db->mysqli_action("UPDATE sitemap SET registro='$total' WHERE id='1'");
	}
}

if($count2 != $total){
	// Procedemos a crear el sitemap
	$fp = fopen( "sitemap.xml", "w" );

	// Colocamos las url estaticas
	fwrite($fp,"<?xml version='1.0' encoding='UTF-8'?>\n");
	fwrite($fp,"<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'>\n");
	fwrite($fp,"<url><loc>http://codeando.org/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>\n");
	fwrite($fp,"<url><loc>http://codeando.org/cursos/</loc><changefreq>daily</changefreq><priority>0.9</priority></url>\n");
	fwrite($fp,"<url><loc>http://codeando.org/contacto/</loc><changefreq>daily</changefreq><priority>0.9</priority></url>\n");

	// Integramos las url dinamicas de los cursos
	while($row = $result->fetch_assoc()){
		$curso_titulo = $row['titulo'];
		$curso_categoria = $row['categoria'];
		$curso_id = $row['id_curso'];

		// Creamos la url del curso
		$curso_url = "curso/".strtolower($fnc->Url($curso_titulo))."/$curso_id/";
		
		// Agregamos a sitemap
		fwrite($fp,"<url><loc>http://codeando.org/$curso_url</loc><changefreq>daily</changefreq><priority>.7</priority></url>\n");
	}
	$result->close();

	// Integramos las url dinamicas de los temas
	while($row1 = $result1->fetch_assoc()){
		$tema_id = $row1['id_tema'];
		$tema_titulo = $row1['titulo'];
		$tema_curso = $row1['id_curso'];

		// Obtenemos la categoria del curso
		$result3 = $db->mysqli_select("SELECT categoria FROM cursos WHERE id_curso='$tema_curso'");
		while($row3 = $result3->fetch_assoc()){
			$tema_categoria = $row3['categoria'];

			// Modificamos la categoria moviles
			if($tema_categoria == 'moviles'){
				$tema_categoria = 'lungojs';
			}
		}
		$result3->close();

		// Creamos la url del curso
		$tema_url = "$tema_categoria/".strtolower($fnc->Url($tema_titulo))."/$tema_id/";
		
		// Agregamos a sitemap
		fwrite($fp,"<url><loc>http://codeando.org/$tema_url</loc><changefreq>daily</changefreq><priority>.5</priority></url>\n");
	}
	$result1->close();

	fwrite($fp,"</urlset>");

	// Comprimimos el sitemap
	$origen = 'sitemap.xml';
	$destino = 'sitemap.gz';

	$fp = fopen($origen, "r");
	$data = fread ($fp, filesize($origen));
	fclose($fp); $zp = gzopen($destino, "w9");
	gzwrite($zp, $data);
	gzclose($zp);

	// Hacemos ping a google
	$sitemapUrl = "http://codeando.org/sitemap.xml";
	$pingUrl="http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($sitemapUrl);

	$sitemapUrl_2 = "http://codeando.org/sitemap.gz";
	$pingUrl_2 = "http://www.google.com/webmasters/sitemaps/ping?sitemap=" . urlencode($sitemapUrl_2);

	echo 'Create sitemap codeando.org succefull!!!';
}