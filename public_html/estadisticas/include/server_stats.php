<?php
/************************************************
Archivo servidor del sistema de estadisticas

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
require_once 'Db.php';

$db = new Db();

$type = (empty($_POST['type'])) ? '' : $_POST['type'];

switch($type){
	case 'show_month':
		show_month($db);
		break;
	case 'show_year':
		show_year($db);
		break;
	case 'stats_resumen':
		stats_resumen($db);
		break;
	case 'stats_visitas':
		stats_visitas($db);
		break;
	case 'stats_historial':
		stats_historial($db);
		break;
}

// Cargamos las estadisticas para un mes en concreto
function show_month($db)
{
	$show_month = $_POST['show_month'];
	$show_year = $_POST['show_year'];
	$resultado = '';

	$resultado .= "<h3>".date("F Y",mktime(0, 0, 0, $show_month, 1, $show_year)); 
	
	$back_month=date("n",mktime(0, 0, 0, $show_month-1, 1, $show_year));
	$back_yaer=date("Y",mktime(0, 0, 0, $show_month-1, 1, $show_year));
	$next_month=date("n",mktime(0, 0, 0, $show_month+1, 1, $show_year));
	$next_yaer=date("Y",mktime(0, 0, 0, $show_month+1, 1, $show_year));
	
	$resultado .= "<span><a onclick='javascript:show_month($back_month,$back_yaer)'><</a>&nbsp;<a onclick='javascript:show_month($next_month,$next_yaer)'>></a></span></h3>
				<table height='230' width='100%' cellpadding='0' cellspacing='0' align='right'>
					<tr valign='bottom' height='210'>";
	
	// Ausgewählten Monat anzeigen
	$bar_nr=0;
	$month_days=date('t',mktime(0,0,0,$show_month,1,$show_year));
	for($day=1; $day<=$month_days; $day++){
		$sel_timestamp = mktime(0, 0, 0, $show_month, $day, $show_year);
		$sel_tag = date("Y.m.d",$sel_timestamp);
		$abfrage=$db->mysqli_select("SELECT sum(user) FROM stats_Day WHERE day='$sel_tag'");
		$User=$db->mysqli_result($abfrage,0,0);
		$abfrage->close();
		
		$bar[$bar_nr]=$User; // Im Array Speichern
		$bar_title[$bar_nr] = date("j.M.Y",$sel_timestamp);
		
		if (date("j")-$day == 1) $bar_mark = $bar_nr;
		if ( date("w", $sel_timestamp) == 6 OR date("w", $sel_timestamp)== 0) {$weekend[$bar_nr]=true;}
		else {$weekend[$bar_nr]=false;}
		
		$bar_nr++;
	}

	// Diagramm 		
	for($i=0; $i<$bar_nr; $i++){
		$value=$bar[$i];
		if ($value == "") $value = 0;
		if (max($bar) > 0) {$bar_hight=round((200/max($bar))*$value);} else $bar_hight = 0;
		if ($bar_hight == 0) $bar_hight = 1;	
		$resultado .= "<td width='30'>
				<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'></div></td>";
	}
	
    $resultado .= "</tr><tr height='20'>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 1, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 7, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 13, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 19, $show_year))."</td>
						<td colspan='7' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 25, $show_year))."</td>
					</tr>
				</table>";

	echo json_encode(array('resultado'=>$resultado));
	exit();
}

// Cargamos las estadisticas para un año en concreto
function show_year($db)
{
	$show_month = $_POST['show_month'];
	$show_year = $_POST['show_year'];
	$resultado_1 = '';
	$resultado_2 = '';

	$resultado_1 .= "<h3>A&ntilde;o ".date("Y",mktime(0, 0, 0, $show_month, 1, $show_year)); 
	
	$back_month=date("n",mktime(0, 0, 0, $show_month, 1, $show_year-1));
	$back_yaer=date("Y",mktime(0, 0, 0, $show_month, 1, $show_year-1));
	$next_month=date("n",mktime(0, 0, 0, $show_month, 1, $show_year+1));
	$next_yaer=date("Y",mktime(0, 0, 0, $show_month, 1, $show_year+1));
	
	$resultado_1 .= "<span><a onclick='javascript:show_year($back_month,$back_yaer)'><</a>&nbsp;<a onclick='javascript:show_year($next_month,$next_yaer)'>></a></span></h3>
		<table height='200' width='100%' cellpadding='0' cellspacing='0' align='right'>
			<tr valign='bottom' height='180'>";

	// Max Month
	$abfrage=$db->mysqli_SELECT("SELECT LEFT(day,7) AS month, sum(user) AS user_month FROM stats_Day GROUP BY month ORDER BY user_month DESC LIMIT 1");
	$max_month=$db->mysqli_result($abfrage,0,1);

	// Monat abfragen
	$bar_nr=0;
	for($month=1; $month<=12; $month++){
		$sel_timestamp = mktime(0, 0, 0, $month, 1, $show_year);
		$sel_month = date("Y.m.%",$sel_timestamp);
		$abfrage=$db->mysqli_select("SELECT sum(user) FROM stats_Day WHERE day LIKE '$sel_month'");
		$User=$db->mysqli_result($abfrage,0,0);
		$abfrage->close();
		
		$bar[$bar_nr]=$User; // Im Array Speichern
		$bar_title[$bar_nr] = date("M.Y",$sel_timestamp);
		$bar_month[$bar_nr]=$month;
		
		$bar_nr++;
	}

	// Diagramm 		
	for($i=0; $i<$bar_nr; $i++){
		$value=$bar[$i];
		if ($value == "") $value = 0;
		if ($max_month > 0) {$bar_hight=round((170/$max_month)*$value);} else $bar_hight = 0;
		if ($bar_hight == 0) $bar_hight = 1;	

		$resultado_1 .= "<td width='38'>
			<a onclick='javascript:show_month(".$bar_month[$i].",$show_year)'>
				<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'></div>
			</a></td>";
	}
	
    $resultado_1 .="</tr><tr height='20'>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 1, 1, $show_year))."</td>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 4, 1, $show_year))."</td>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 7, 1, $show_year))."</td>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 10, 1, $show_year))."</td>
					</tr>
				</table>";
  
    $resultado_2 .= "<h3>".date("F Y",mktime(0, 0, 0, $show_month, 1, $show_year)); 
	
	$back_month=date("n",mktime(0, 0, 0, $show_month-1, 1, $show_year));
	$back_yaer=date("Y",mktime(0, 0, 0, $show_month-1, 1, $show_year));
	$next_month=date("n",mktime(0, 0, 0, $show_month+1, 1, $show_year));
	$next_yaer=date("Y",mktime(0, 0, 0, $show_month+1, 1, $show_year));
	
	$resultado_2 .= "<span><a onclick='javascript:show_month($back_month,$back_yaer)'><</a>&nbsp;<a onclick='javascript:show_month($next_month,$next_yaer);'>></a></span></h3>
				<table height='230' width='100%' cellpadding='0' cellspacing='0' align='right'>
					<tr valign='bottom' height='210'>";
	
	// Ausgewählten Monat anzeigen
	$bar_nr=0;
	$month_days=date('t',mktime(0,0,0,$show_month,1,$show_year));
	for($day=1; $day<=$month_days; $day++){
		$sel_timestamp = mktime(0, 0, 0, $show_month, $day, $show_year);
		$sel_tag = date("Y.m.d",$sel_timestamp);
		$abfrage=$db->mysqli_select("SELECT sum(user) FROM stats_Day WHERE day='$sel_tag'");
		$User=$db->mysqli_result($abfrage,0,0);
		$abfrage->close();
		
		$bar[$bar_nr]=$User; // Im Array Speichern
		$bar_title[$bar_nr] = date("j.M.Y",$sel_timestamp);
		
		if (date("j")-$day == 1) $bar_mark = $bar_nr;
		if ( date("w", $sel_timestamp) == 6 OR date("w", $sel_timestamp)== 0) {$weekend[$bar_nr]=true;}
		else {$weekend[$bar_nr]=false;}
		
		$bar_nr++;
	}

	// Diagramm 		
	for($i=0; $i<$bar_nr; $i++){
		$value=$bar[$i];
		if ($value == "") $value = 0;
		if (max($bar) > 0) {$bar_hight=round((200/max($bar))*$value);} else $bar_hight = 0;
		if ($bar_hight == 0) $bar_hight = 1;	
		$resultado_2 .= "<td width='30'>
				<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'></div></td>";
	}
	
    $resultado_2 .= "</tr><tr height='20'>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 1, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 7, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 13, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 19, $show_year))."</td>
						<td colspan='7' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 25, $show_year))."</td>
					</tr>
				</table>";

	echo json_encode(array('resultado_1'=>$resultado_1,'resultado_2'=>$resultado_2));
	exit();
}

// Cargamos las estadisticas del resumen [pagina stats.php]
function stats_resumen($db)
{
	$resumen = '';
	$horas = '';
	$mes = '';

	// Obtenemos total de vitantes y visitas
	$result = $db->mysqli_select("SELECT sum(user),sum(view) FROM stats_Day");
	$visitors = $db->mysqli_result($result,0,0);
	$visits = $db->mysqli_result($result,0,1);
	$result->close();

	$resumen .= "<h3>Resumen</h3>
	<table width='100%' border='0' cellpadding='5' cellspacing='0' class='oneview'>
    	<tr valign='top'>      
			<td width='30%'>Visitantes</td><td width='20%'>$visitors</td>
			<td width='30%'>Visitas</td><td width='20%'>$visits</td>
		</tr>
		<tr valign='top'>";

	// Obtenemos estadisticas online
	$time = time();
	$isonline = $time-(3*60);  // 3 Minuten Online Zeit
	$result1 = $db->mysqli_select("SELECT Count(id) FROM stats_IPs WHERE online>='$isonline'");
	$online = $db->mysqli_result($result1,0,0);
	$result1->close();

	$resumen .= "<td>En Linea</td><td>$online</td>
		<td>&nbsp;</td><td>&nbsp;</td>\n
	</tr>
	<tr valign='top'>
		<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	</tr>
	<tr valign='top'>";

	// Obtenemos el porcentaje de rebotes
	$result2 = $db->mysqli_select("SELECT Count(id) FROM stats_IPs");
	$total = $db->mysqli_result($result2,0,0);
	$result2->close();
	$result3 = $db->mysqli_select("SELECT Count(id) FROM stats_IPs WHERE online=time");
	$onepage = $db->mysqli_result($result3,0,0);
	$result3->close();

	$resumen .= "<td>Rebotes</td><td>".round(($onepage/$total)*100,2)."%</td>";

	// Estadisticas de paginas y usuarios de los ultimos 7 dias
	$from_day = date("Y.m.d",$time - (7*24*60*60));
	$to_day = date("Y.m.d",$time  - (24*60*60)); // <= ohne heute
	$result4 = $db->mysqli_select("SELECT AVG(user),(sum(view)/sum(user)) FROM stats_Day WHERE day>='$from_day' AND day<='$to_day'");
	$avg_7 = round($db->mysqli_result($result4,0,0),2);
	$page_user = round($db->mysqli_result($result4,0,1),1);
	$result4->close();

	$resumen .= "<td>Paginas/Visitantes</td><td>$page_user</td>
	</tr>
	<tr valign='top'>
		<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	</tr>
	<tr valign='top'>
		<td>&Oslash; 7 dias</td>
		<td>$avg_7</td>";

	// Obtenemos estadisticas de los ultimos 30 dias
	$from_day = date("Y.m.d",$time - (30*24*60*60));
	$to_day = date("Y.m.d",$time - (24*60*60)); // <= ohne heute
	$result5 = $db->mysqli_select("SELECT AVG(user) FROM stats_Day WHERE day>='$from_day' AND day<='$to_day'");
	$avg_30 = round($db->mysqli_result($result5,0,0),2);
	$result5->close();

	$resumen .= "<td>&Oslash; 30 dias</td>
		<td>$avg_30</td>
	</tr>
	<tr valign='top'>";

	// Gesamt User Heute
	$sel_timestamp = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
	$sel_tag = date("Y.m.d",$sel_timestamp);
	$result5 = $db->mysqli_select("SELECT sum(user) FROM stats_Day WHERE day='$sel_tag'");
	$today = $db->mysqli_result($result5,0,0);
	if ($today=="") $today=0;
	$result5->close();

	$resumen .= "<td>Hoy</td><td>$today</td>";

	// gestern zur gleichen Zeit
	$anfangTag = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - 24*60*60 ;
	$endeTag = $time - 24*60*60 ;
	$result6 = $db->mysqli_select("SELECT Count(id) FROM stats_IPs WHERE time>='$anfangTag' AND time<=$endeTag");
	$yesterday = $db->mysqli_result($result6,0,0);
	$result6->close();

	$resumen .= "<td>Ayer (".date("G:i",$time).")</td><td>$yesterday</td>
	</tr>	
    </table>";

    $horas .= "<h3>Ultimas 24 horas</h3>
	<table height='200' width='100%' cellpadding='0' cellspacing='0' align='right'>
		<tr valign='bottom' height='180'>";

	// Cargamos estadisticas de las ultimas 24 hrs
	$bar_nr = 0;
	$bar_mark = '';
	for($Stunde = 23; $Stunde >= 0; $Stunde--){
		$anfangStunde = mktime(date("H") - $Stunde, 0, 0, date("n"), date("j"), date("Y"));
		$endeStunde = mktime(date("H") - $Stunde, 59, 59, date("n"), date("j"), date("Y"));

		$result7 = $db->mysqli_select("SELECT Count(id) FROM stats_IPs WHERE time>='$anfangStunde' AND time<=$endeStunde");
		$User = $db->mysqli_result($result7,0,0);
		$result7->close();

		// Preparacion del diagrama, creando el array
		$bar[$bar_nr] = $User; 
		$bar_title[$bar_nr] = date('G:i',$anfangStunde)." - ".date('G:i',$endeStunde);			
		if(date('H') - $Stunde == 0){ $bar_mark = $bar_nr; }
		$bar_nr++;
	}
	// Diagrama 		
	for($i = 0; $i < $bar_nr; $i++){
		$value = $bar[$i];
		if ($value == '') $value = 0;
		if (max($bar) > 0) { $bar_hight=round((170/max($bar))*$value); } else { $bar_hight = 0; }
		if ($bar_hight == 0) $bar_hight = 1;	
		if ($bar_mark == '$i' ) { $horas .= "<td style='border-left: #FF0000 1px dotted;' width='19'>";}
		else { $horas .= "<td width='19'>"; }
		$horas .= "<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'><span>$value</span></div></td>";
	}

	$dato1 = date('G:i',mktime(date('H')-23, 0, 0, date('n'), date('j'), date('Y')));
	$dato2 = date('G:i',mktime(date('H')-17, 0, 0, date('n'), date('j'), date('Y')));
	$dato3 = date('G:i',mktime(date('H')-11, 0, 0, date('n'), date('j'), date('Y')));
	$dato4 = date('G:i',mktime(date('H')-5, 0, 0, date('n'), date('j'), date('Y')));	
			
    $horas .= "</tr><tr height='20'>
		<td colspan='6' width='25%' class='timeline'>$dato1</td>
		<td colspan='6' width='25%' class='timeline'>$dato2</td>
		<td colspan='6' width='25%' class='timeline'>$dato3</td>
		<td colspan='6' width='25%' class='timeline'>$dato4</td>
	</tr></table>";

	$mes .= "<h3>Ultimos 30 dias</h3>
	<table height='230' width='100%' cellpadding='0' cellspacing='0' align='right'>
		<tr valign='bottom' height='210'>";
	
	// Estadisticas de usuario de los ultimos 30 dias
	$bar_nr = 0;
	$bar_mark = '';
	for($day = 29; $day >= 0; $day--){
		$sel_timestamp = mktime(0, 0, 0, date('n'), date('j')-$day, date('Y'));
		$sel_tag = date('Y.m.d',$sel_timestamp);

		$result8 = $db->mysqli_select("SELECT sum(user) from stats_Day WHERE day='$sel_tag'");
		$User = $db->mysqli_result($result8,0,0);
		$result8->close();
		
		$bar[$bar_nr] = $User; // Im Array Speichern
		$bar_title[$bar_nr] = date('j.M.Y',$sel_timestamp);
		
		if(date('j') - $day == 1){ $bar_mark = $bar_nr; }
		if(date('w', $sel_timestamp) == 6 OR date('w', $sel_timestamp)== 0){ $weekend[$bar_nr]=true; }
		else { $weekend[$bar_nr] = false; }
		
		$bar_nr++;
	}

	// Diagrama
	for($i = 0; $i < $bar_nr; $i++){
		$value = $bar[$i];
		if($value == ''){ $value = 0; }
		if(max($bar) > 0){ $bar_hight = round((200/max($bar))*$value); } else { $bar_hight = 0; }
		if($bar_hight == 0){ $bar_hight = 1; }
		if ($bar_mark == '$i' ){ $mes .= "<td style='border-left: #FF0000 1px dotted;' width='31'>"; }
		else { $mes .= "<td width='31'>"; }

		$mes .= "<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'>$value</div></td>";
	}

	$date1 = date('j.M',mktime(0, 0, 0, date('n'), date('j')-29, date('Y')));
	$date2 = date('j.M',mktime(0, 0, 0, date('n'), date('j')-23, date('Y')));
	$date3 = date('j.M',mktime(0, 0, 0, date('n'), date('j')-17, date('Y')));
	$date4 = date('j.M',mktime(0, 0, 0, date('n'), date('j')-11, date('Y')));
	$date5 = date('j.M',mktime(0, 0, 0, date('n'), date('j')-5, date('Y')));
    
    $mes .= "</tr><tr height='20'>
		<td colspan='6' class='timeline'>$date1</td>
		<td colspan='6' class='timeline'>$date2</td>
		<td colspan='6' class='timeline'>$date3</td>
		<td colspan='6' class='timeline'>$date4</td>
		<td colspan='6' class='timeline'>$date5</td>
	</tr></table>";

    echo json_encode(array('status'=>'Datos cargados correctamente','resumen'=>$resumen,'horas'=>$horas,'mes'=>$mes));
    exit();
}

// Cargamos las estadisticas de la pagina de visitas
function stats_visitas($db)
{
	$referencia = '';
	$pagina = '';
	$keyword = '';
	$lenguaje = '';

    $referencia .= "<h3>Referencias Top 10</h3>
		<table width='100%' border='0' cellpadding='5' cellspacing='0'>
			<tr>
      			<td width='30'><strong>N.</strong></td>
      			<td width='280'><strong>Referencia</strong></td>
      			<td width='120'><strong>Porcentaje</strong></td>
    		</tr>";
    
	// gesammt Referrer	
	$abfrage=$db->mysqli_select("SELECT sum(view) FROM stats_Referer");
	$ges_referer=$db->mysqli_result($abfrage,0,0);
	$abfrage->close();

	// Top Refferrer
	$nr = 1;
	$abfrage=$db->mysqli_select("SELECT referer, SUM(view) AS views from stats_Referer GROUP BY referer ORDER BY views DESC LIMIT 0, 10");
	while($row=$abfrage->fetch_assoc()){
		$referer=htmlspecialchars($row['referer']);
		if(strlen($referer) > 35){
			$shortreferer=substr($referer,0,30)."<a href=\"#\" title=\"$referer\">...</a>";
		} else {
			$shortreferer=$referer;
		}		
		$views=$row['views'];
		$prozent = (100/$ges_referer)*$views;
		if ($prozent < 0.1 ){
			$prozent = round($prozent,2);
		} else {
			$prozent = round($prozent,1);
		}
		$bar_width = round((100/$ges_referer)*$views);
		$referencia .= "<tr>
							<td>$nr</td>
							<td>$shortreferer</td>
							<td nowrap><div class='vbar' style='width:".$bar_width."px;' title='$views Visitors'>&nbsp;$prozent%</div></td>
						</tr>";
		$nr++;
		}
	$abfrage->close();
    $referencia .= "</table>";

	$pagina .= "<h3>Páginas Top10</h3>
		<table width='100%' cellpadding='5' cellspacing='0'>
  			<tr>
	    		<td width='30'><strong>N.</strong></td>
	    		<td width='280'><strong>Páginas</strong></td>
	    		<td width='120'><strong>Porcentaje</strong></td>
	  		</tr>";

	// gesammt Pages
	$abfrage=$db->mysqli_select("SELECT sum(view) FROM stats_Page");
	$ges_page=$db->mysqli_result($abfrage,0,0);
	$abfrage->close();

	// Top Pages
	$nr = 1;
	$abfrage=$db->mysqli_select("SELECT page, SUM(view) AS views from stats_Page GROUP BY page ORDER BY views DESC LIMIT 0, 10");
	while($row=$abfrage->fetch_assoc()){
		$page=htmlspecialchars($row['page']);
		if(strlen($page) > 35){
			$shortpage="<a href=\"#\" title=\"$page\">...</a>".substr($page,strlen($page)-30,strlen($page));
		} else {
			$shortpage=$page;
		}
		$views=$row['views'];
		$prozent = (100/$ges_page)*$views;
		if($prozent < 0.1 ){
			$prozent = round($prozent,2);
		} else {
			$prozent = round($prozent,1);
		}
		$bar_width = round((100/$ges_page)*$views);

		$pagina .= "<tr>
						<td>$nr</td>
						<td>$shortpage</td>
						<td nowrap><div class='vbar' style='width:".$bar_width."px;' title='$views Visits'>&nbsp;$prozent%</div></td>
					</tr>";
		$nr++;
	}
	$abfrage->close();
	$pagina .= "</table>";
  
    $keyword .= "<h3>Keywords Top 10</h3>
				<table width='100%' border='0' cellpadding='5' cellspacing='0'>
    				<tr>
        				<td width='30'><strong>N.</strong></td>
        				<td width='280'><strong>Keywords</strong></td>
        				<td width='120'><strong>Porcentaje</strong></td>
      				</tr>";
	
	// gesammt keywords	
	$abfrage=$db->mysqli_select("SELECT sum(view) FROM stats_Keyword");
	$ges_keyword=$db->mysqli_result($abfrage,0,0);
	$abfrage->close();

	// Top Keywords
	$nr = 1;
	$abfrage=$db->mysqli_select("SELECT keyword, SUM(view) AS views from stats_Keyword GROUP BY keyword ORDER BY views DESC LIMIT 0, 10");
	while($row=$abfrage->fetch_assoc()){
		$keyword=urldecode($row['keyword']);
		if(strlen($keyword) > 35){$shortkeyword=substr($keyword,0,30)."<a href=\"#\" title=\"$keyword\">...</a>";}
		else {$shortkeyword=$keyword;}
		$views=$row['views'];
		$prozent = (100/$ges_keyword)*$views;
		if ($prozent < 0.1 ) $prozent = round($prozent,2);
		else $prozent = round($prozent,1);
		$bar_width = round((100/$ges_keyword)*$views);
		$keyword .= "<tr>
						<td>$nr</td>
						<td>$shortkeyword</td>
						<td nowrap><div class='vbar' style='width:".$bar_width."px;' title='$views Visitors' >&nbsp;$prozent%</div></td>
					</tr>";
		$nr++;
	}
	$abfrage->close();
	$keyword .= "</table>";
	
    $lenguaje = "<h3>Lenguajes Top 10</h3>
				<table width='100%' border='0' cellpadding='5' cellspacing='0'>
    				<tr>
        				<td width='30'><strong>N.</strong></td>
        				<td width='280'><strong>Lenguaje</strong></td>
        				<td width='120'><strong>Porcentaje</strong></td>
      				</tr>";
	
	// gesammt Languages	
	$abfrage=$db->mysqli_select("SELECT sum(view) FROM stats_Language");
	$ges_language=$db->mysqli_result($abfrage,0,0);
	$abfrage->close();

	// Code to Language
	$code2lang = array(
		'ar'=>'Arabic',
		'bn'=>'Bengali',
		'bg'=>'Bulgarian',
		'zh'=>'Chinese',
		'cs'=>'Czech',
		'da'=>'Danish',
		'en'=>'English',
		'et'=>'Estonian',
		'fi'=>'Finnish',
		'fr'=>'French',
		'de'=>'German',
		'el'=>'Greek',
		'hi'=>'Hindi',
		'id'=>'Indonesian',
		'it'=>'Italian',
		'ja'=>'Japanese',
		'kg'=>'Korean',
		'nb'=>'Norwegian',
		'nl'=>'Nederlands',
		'pl'=>'Polish',
		'pt'=>'Portuguese',
		'ro'=>'Romanian',
		'ru'=>'Russian',
		'sr'=>'Serbian',
		'sk'=>'Slovak',
		'es'=>'Spanish',
		'sv'=>'Swedish',	
		'th'=>'Thai',
		'tr'=>'Turkish',
		''=>'');

	// Top Languages
	$nr = 1;
	$abfrage=$db->mysqli_select("SELECT language, SUM(view) AS views from stats_Language GROUP BY language ORDER BY views DESC LIMIT 0, 10");
	while($row=$abfrage->fetch_assoc()){
		$language=$row['language'];
		if (array_key_exists($language,$code2lang)) $language=$code2lang[$language];
		$views=$row['views'];
		$prozent = (100/$ges_language)*$views;
		if ($prozent < 0.1 ) $prozent = round($prozent,2);
		else $prozent = round($prozent,1);
		$bar_width = round((100/$ges_language)*$views);
		$lenguaje .= "<tr>
						<td>$nr</td>
						<td>$language</td>
						<td nowrap><div class='vbar' style='width:".$bar_width."px;' title='$views Visitors'>&nbsp;$prozent%</div></td>
					</tr>";
		$nr++;
		}
	$abfrage->close();
	$lenguaje .= "</table>";


	echo json_encode(array('referencia'=>$referencia,'pagina'=>$pagina,'keyword'=>$keyword,'lenguaje'=>$lenguaje));
	exit();
}

// Mostramos historial de estadisticas
function stats_historial($db)
{
	$show_month = $_POST['show_month'];
	$show_year = $_POST['show_year'];
	$historial = '';
	$historial_1 = '';
	$historial_2 = '';

	// Gesamt Besucher ermitteln
	$abfrage=$db->mysqli_select("SELECT sum(user),sum(view),min(day),avg(user) FROM stats_Day");
	$visitors=$db->mysqli_result($abfrage,0,0);
	$visits=$db->mysqli_result($abfrage,0,1);
	$since=$db->mysqli_result($abfrage,0,2);
	$since=str_replace(".", "-", $since);
	$since=strtotime($since);
	$since=date("d F Y",$since);
	$total_avg=round($db->mysqli_result($abfrage,0,3),2);
	$abfrage->close();

	$historial .= "<h3>Historial</h3>
				<table width='100%' border='0' cellpadding='5' cellspacing='0'>
      				<tr valign='top'>
	  					<td colspan='4'><strong>Total desde $since</strong></td>
	  				</tr>
	  				<tr valign='top'>
	  					<td width='30%'>Visitantes</td>
	  					<td width='20%'>$visitors</td>
	  					<td width='30%'>Visitas</td>
	  					<td width='20%'>$visits</td>
	  				</tr>
	  				<tr valign='top'>
	  					<td width='30%'>&Oslash; Dia</td>
	  					<td width='20%'>$total_avg</td>
	  					<td width='30%'>&nbsp;</td>
	  					<td width='20%'>&nbsp;</td>
	  				</tr>
				</table>
				<br />";

	// selected Moth
	$sel_timestamp = mktime(0, 0, 0, $show_month, 1, $show_year);
	$sel_month = date("Y.m.%",$sel_timestamp);
	$abfrage=$db->mysqli_select("SELECT sum(user), sum(view), avg(user) FROM stats_Day WHERE day LIKE '$sel_month'");
	$visitors=$db->mysqli_result($abfrage,0,0);
	$visits=$db->mysqli_result($abfrage,0,1);
	$day_avg=round($db->mysqli_result($abfrage,0,2),2);
	$abfrage->close();	  
	  
	$historial .="<table width='100%' border='0' cellpadding='5' cellspacing='0'>
	  				<tr valign='top'>
						<td colspan='4'><strong>Fecha seleccionada ".date("F Y",mktime(0, 0, 0, $show_month, 1, $show_year))."</strong></td>
	  				</tr>
	  				<tr valign='top'>
	    				<td>Visitantes</td>
	    				<td>$visitors</td>
	    				<td>Visitas</td>
	    				<td>$visits</td>
	  				</tr>
	  				<tr valign='top'>
						<td>&Oslash; Dia</td>
						<td>$day_avg</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
	  				</tr>
    			</table>";
  
    $historial_1 .= "<h3>A&ntilde;o ".date("Y",mktime(0, 0, 0, $show_month, 1, $show_year)); 
	
	$back_month=date("n",mktime(0, 0, 0, $show_month, 1, $show_year-1));
	$back_yaer=date("Y",mktime(0, 0, 0, $show_month, 1, $show_year-1));
	$next_month=date("n",mktime(0, 0, 0, $show_month, 1, $show_year+1));
	$next_yaer=date("Y",mktime(0, 0, 0, $show_month, 1, $show_year+1));
	
	$historial_1 .= "<span><a onclick='javascript:show_year($back_month,$back_yaer)'><</a>&nbsp;<a onclick='javascript:show_year($next_month,$next_yaer)'>></a></span></h3>
		<table height='200' width='100%' cellpadding='0' cellspacing='0' align='right'>
			<tr valign='bottom' height='180'>";

	// Max Month
	$abfrage=$db->mysqli_SELECT("SELECT LEFT(day,7) AS month, sum(user) AS user_month FROM stats_Day GROUP BY month ORDER BY user_month DESC LIMIT 1");
	$max_month=$db->mysqli_result($abfrage,0,1);

	// Monat abfragen
	$bar_nr=0;
	for($month=1; $month<=12; $month++){
		$sel_timestamp = mktime(0, 0, 0, $month, 1, $show_year);
		$sel_month = date("Y.m.%",$sel_timestamp);
		$abfrage=$db->mysqli_select("SELECT sum(user) FROM stats_Day WHERE day LIKE '$sel_month'");
		$User=$db->mysqli_result($abfrage,0,0);
		$abfrage->close();
		
		$bar[$bar_nr]=$User; // Im Array Speichern
		$bar_title[$bar_nr] = date("M.Y",$sel_timestamp);
		$bar_month[$bar_nr]=$month;
		
		$bar_nr++;
	}

	// Diagramm 		
	for($i=0; $i<$bar_nr; $i++){
		$value=$bar[$i];
		if ($value == "") $value = 0;
		if ($max_month > 0) {$bar_hight=round((170/$max_month)*$value);} else $bar_hight = 0;
		if ($bar_hight == 0) $bar_hight = 1;	

		$historial_1 .= "<td width='38'>
			<a onclick='javascript:show_month(".$bar_month[$i].",$show_year)'>
				<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'></div>
			</a></td>";
	}
	
    $historial_1 .="</tr><tr height='20'>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 1, 1, $show_year))."</td>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 4, 1, $show_year))."</td>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 7, 1, $show_year))."</td>
						<td colspan='3' width='25%' class='timeline'>".date("M.Y",mktime(0, 0, 0, 10, 1, $show_year))."</td>
					</tr>
				</table>";
  
    $historial_2 .= "<h3>".date("F Y",mktime(0, 0, 0, $show_month, 1, $show_year)); 
	
	$back_month=date("n",mktime(0, 0, 0, $show_month-1, 1, $show_year));
	$back_yaer=date("Y",mktime(0, 0, 0, $show_month-1, 1, $show_year));
	$next_month=date("n",mktime(0, 0, 0, $show_month+1, 1, $show_year));
	$next_yaer=date("Y",mktime(0, 0, 0, $show_month+1, 1, $show_year));
	
	$historial_2 .= "<span><a onclick='javascript:show_month($back_month,$back_yaer)'><</a>&nbsp;<a onclick='javascript:show_month($next_month,$next_yaer);'>></a></span></h3>
				<table height='230' width='100%' cellpadding='0' cellspacing='0' align='right'>
					<tr valign='bottom' height='210'>";
	
	// Ausgewählten Monat anzeigen
	$bar_nr=0;
	$month_days=date('t',mktime(0,0,0,$show_month,1,$show_year));
	for($day=1; $day<=$month_days; $day++){
		$sel_timestamp = mktime(0, 0, 0, $show_month, $day, $show_year);
		$sel_tag = date("Y.m.d",$sel_timestamp);
		$abfrage=$db->mysqli_select("SELECT sum(user) FROM stats_Day WHERE day='$sel_tag'");
		$User=$db->mysqli_result($abfrage,0,0);
		$abfrage->close();
		
		$bar[$bar_nr]=$User; // Im Array Speichern
		$bar_title[$bar_nr] = date("j.M.Y",$sel_timestamp);
		
		if (date("j")-$day == 1) $bar_mark = $bar_nr;
		if ( date("w", $sel_timestamp) == 6 OR date("w", $sel_timestamp)== 0) {$weekend[$bar_nr]=true;}
		else {$weekend[$bar_nr]=false;}
		
		$bar_nr++;
	}

	// Diagramm 		
	for($i=0; $i<$bar_nr; $i++){
		$value=$bar[$i];
		if ($value == "") $value = 0;
		if (max($bar) > 0) {$bar_hight=round((200/max($bar))*$value);} else $bar_hight = 0;
		if ($bar_hight == 0) $bar_hight = 1;	
		$historial_2 .= "<td width='30'>
				<div class='bar' style='height:".$bar_hight."px;' title='".$bar_title[$i]." - $value Visitantes'></div></td>";
	}
	
    $historial_2 .= "</tr><tr height='20'>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 1, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 7, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 13, $show_year))."</td>
						<td colspan='6' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 19, $show_year))."</td>
						<td colspan='7' class='timeline'>".date("j.M",mktime(0, 0, 0, $show_month, 25, $show_year))."</td>
					</tr>
				</table>";

	echo json_encode(array('historial'=>$historial,'historial_1'=>$historial_1,'historial_2'=>$historial_2));
	exit();
}