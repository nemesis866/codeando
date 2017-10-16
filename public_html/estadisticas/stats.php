<?php
/************************************************
Archivo que muestras las estadisticas (resumen)

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

require_once 'include/header.php';
?>

<div id="resumen" class="middle">
  	<!-- /Resumen de estadisticas -->
</div>
<div id="horas" class="middle">
  	<!-- /Estadisticas de las ultimas 24 Hrs -->
</div>
<div style="clear:both"></div>
<div id="mes" class="full">
  	<!-- /Estadisticas mensuales -->
</div>
<div style="clear:both"></div>
<script>stats();</script>
<?php
require_once 'include/footer.php';