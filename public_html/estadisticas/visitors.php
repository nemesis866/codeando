<?php
/************************************************
Archivo que muestras las estadisticas (visitas)

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

require_once 'include/header.php';
?>

<div id="referencia" class="middle">
	<!-- Top 10 paginas de referencia //-->
</div>
<div id="pagina" class="middle">
	<!-- Top 10 paginas mas visitadas //-->
</div>
<div style="clear:both"></div>
<div id="keyword" class="middle">
	<!-- Top 10 palabras de busqueda //-->
</div>
<div id="lenguaje" class="middle">
	<!-- Top 10 lenguajes de los visitantes //-->
</div>
<div style="clear:both"></div>

<script>visitors();</script>
<?php
require_once 'include/footer.php';
?>