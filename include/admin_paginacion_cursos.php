<?php
/************************************************
Archivo de paginacion para cursos

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compugmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Inicializamos base de datos
$db = new db();

// Obtenemos variables
if(empty($_GET['sub'])){ $sub = '';} else { $sub = addslashes($_GET['sub']);}
$autor = $_SESSION['id'];

// Verificamos si se trata del preview
if($sub == 'review'){
	$comment_url = '/admin-co/?category=course&sub=review&_pagi_pg';
	$_pagi_sql="SELECT * FROM cursos WHERE revicion='YES' ORDER BY fecha DESC";
} else {
	$comment_url = '/admin-co/?category=course&_pagi_pg';
	$_pagi_sql="SELECT * FROM cursos WHERE autor='$autor' ORDER BY fecha DESC";
}

$_pagi_cuantos=20;
$_pagi_mostrar_errores=true;
$_pagi_conteo_alternativo=false;
$_pagi_separador=" ";
$_pagi_nav_num_enlaces=10;
if(isset($_pagi_nav_estilo)){
	$_pagi_nav_estilo_mod = "class=\'$_pagi_nav_estilo\'";
 }else{
 	$_pagi_nav_estilo_mod = "";
 }
$_pagi_nav_anterior="Anterior";
$_pagi_nav_siguiente="Siguiente";
$_pagi_nav_primera="Primera";
$_pagi_nav_ultima = "Ultima";
 if (empty($_GET['_pagi_pg'])){
	$_pagi_actual = 1;
 }else{
    $_pagi_actual = $_GET['_pagi_pg'];
 }
 if($_pagi_conteo_alternativo == false){
 	$_pagi_sqlConta = @eregi_replace("select[[:space:]](.*)[[:space:]]from", "SELECT COUNT(*) FROM", $_pagi_sql);
 	$_pagi_result2 = $db->mysqli_select($_pagi_sqlConta);
 	if($_pagi_result2 == false && $_pagi_mostrar_errores == true){
		die (" Error en la consulta de conteo de registros: $_pagi_sqlConta. Mysql dijo: <b>".mysqli_error()."</b>");
 	}
 	$_pagi_totalReg = $db->mysqli_result($_pagi_result2,0,0);
 }else{
	$_pagi_result3 = $db->mysqli_select($_pagi_sql);
 	if($_pagi_result3 == false && $_pagi_mostrar_errores == true){
		die (" Error en la consulta de conteo alternativo de registros: $_pagi_sql. Mysql dijo: <b>".mysqli_error()."</b>");
 	}
	$_pagi_totalReg = $_pagi_result3->num_rows;
 }
 $_pagi_totalPags = ceil($_pagi_totalReg / $_pagi_cuantos);
 $_pagi_enlace = $_SERVER['PHP_SELF'];
 $_pagi_query_string = "?";
 if(!isset($_pagi_propagar)){
	if (isset($_GET['_pagi_pg'])) unset($_GET['_pagi_pg']);
	$_pagi_propagar = array_keys($_GET);
 }elseif(!is_array($_pagi_propagar)){
	die("<b>Error Paginator : </b>La variable \$_pagi_propagar debe ser un array");
 }
 foreach($_pagi_propagar as $var){
 	if(isset($GLOBALS[$var])){
		$_pagi_query_string.= $var."=".$GLOBALS[$var]."&";
	}elseif(isset($_REQUEST[$var])){
		$_pagi_query_string.= $var."=".$_REQUEST[$var]."&";
	}
 }
 $_pagi_enlace .= $_pagi_query_string;
 $_pagi_navegacion_temporal = array();
 if ($_pagi_actual != 1){
	$_pagi_url = 1;
	$_pagi_navegacion_temporal[] = "<a class='responsive' ".$_pagi_nav_estilo_mod." href='".$comment_url."=".$_pagi_url."'>$_pagi_nav_primera</a>";
	$_pagi_url = $_pagi_actual - 1;
	$_pagi_navegacion_temporal[] = "<a ".$_pagi_nav_estilo_mod." href='".$comment_url."=".$_pagi_url."'>$_pagi_nav_anterior</a>";
 }
 if(!isset($_pagi_nav_num_enlaces)){
	$_pagi_nav_desde = 1;
	$_pagi_nav_hasta = $_pagi_totalPags;
 }else{
	$_pagi_nav_intervalo = ceil($_pagi_nav_num_enlaces/2) - 1;
	$_pagi_nav_desde = $_pagi_actual - $_pagi_nav_intervalo;
	$_pagi_nav_hasta = $_pagi_actual + $_pagi_nav_intervalo;
	if($_pagi_nav_desde < 1){
		$_pagi_nav_hasta -= ($_pagi_nav_desde - 1);
		$_pagi_nav_desde = 1;
	}
	if($_pagi_nav_hasta > $_pagi_totalPags){
		$_pagi_nav_desde -= ($_pagi_nav_hasta - $_pagi_totalPags);
		$_pagi_nav_hasta = $_pagi_totalPags;
		if($_pagi_nav_desde < 1){
			$_pagi_nav_desde = 1;
		}
	}
 }
 for ($_pagi_i = $_pagi_nav_desde; $_pagi_i<=$_pagi_nav_hasta; $_pagi_i++){
	if ($_pagi_i == $_pagi_actual) {
		$_pagi_navegacion_temporal[] = "<span class='responsive'".$_pagi_nav_estilo_mod.">$_pagi_i</span>";
	}else{
		$_pagi_navegacion_temporal[] = "<a class='responsive' ".$_pagi_nav_estilo_mod." href='".$comment_url."=".$_pagi_i."'>".$_pagi_i."</a>";
	}
 }
 if ($_pagi_actual < $_pagi_totalPags){
	$_pagi_url = $_pagi_actual + 1;
	$_pagi_navegacion_temporal[] = "<a ".$_pagi_nav_estilo_mod." href='".$comment_url."=".$_pagi_url."'>$_pagi_nav_siguiente</a>";
	$_pagi_url = $_pagi_totalPags;
	$_pagi_navegacion_temporal[] = "<a class='responsive' rel='nofollow' ".$_pagi_nav_estilo_mod." href='".$comment_url."=".$_pagi_url."'>$_pagi_nav_ultima</a>";
 }
 $_pagi_navegacion = implode($_pagi_separador, $_pagi_navegacion_temporal);
 $_pagi_inicial = ($_pagi_actual-1) * $_pagi_cuantos;
 $_pagi_sqlLim = $_pagi_sql." LIMIT $_pagi_inicial,$_pagi_cuantos";
 $_pagi_result = $db->mysqli_select($_pagi_sqlLim);
 if($_pagi_result == false && $_pagi_mostrar_errores == true){
 	die ("Error en la consulta limitada: $_pagi_sqlLim. Mysql dijo: <b>".mysqli_error()."</b>");
 }
 $_pagi_desde = $_pagi_inicial + 1;
 $_pagi_hasta = $_pagi_inicial + $_pagi_cuantos;
 if($_pagi_hasta > $_pagi_totalReg){
 	$_pagi_hasta = $_pagi_totalReg;
 }
$_pagi_info = 'Mostrando cursos del '.$_pagi_desde.' al '.$_pagi_hasta.' de un total de '.$_pagi_totalReg.' cursos';
?>