<?php
/************************************************
Archivo que muestras las estadisticas (Historial)

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Get Month and Year
$time = time();

if(empty($_GET['m'])){ $m = '';} else { $m = addslashes($_GET['m']);}
if(empty($_GET['y'])){ $y = '';} else { $y = addslashes($_GET['y']);}

if (is_numeric($m) AND $m >= 1 AND $m <= 12 ){
	$show_month = $m;
} else {
	$show_month=date("n",$time);
}
if (is_numeric($y) AND $y >= 1 AND $y <= 9999 ){
	$show_year = $y;
} else {
	$show_year=date("Y",$time);
}

require_once 'include/header.php';
?>

<input type="hidden" id="show_month" value="<?php echo $show_month; ?>">
<input type="hidden" id="show_year" value="<?php echo $show_year; ?>">
<div id="historial" class="middle">
		<!-- Historial //-->
</div>
<div id="historial_1" class="middle">
	<!--  //-->
</div>
<div style="clear:both"></div>
<div id="historial_2" class="full">
	<!--  //-->
</div>
<div style="clear:both"></div>
<script>history();</script>
<?php
require_once 'include/footer.php';