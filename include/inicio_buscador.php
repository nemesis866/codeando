<?php
/************************************************
Archivo para mostrar resultados de busqueda

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

$template = new Template();

if(empty($_GET['q'])){ $q = '';} else { $q = addslashes($_GET['q']);}
?>

<h2>Resultados de busqueda para "<?php echo $q; ?>"</h2>

<div id="cse-search-results"></div>
<script type="text/javascript">
  var googleSearchIframeName = "cse-search-results";
  var googleSearchFormName = "cse-search-box";
  var googleSearchFrameWidth = 610;
  var googleSearchDomain = "www.google.com.mx";
  var googleSearchPath = "/cse";
</script>
<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
