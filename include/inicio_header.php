<?php
/************************************************
Archivo que contiene el header para inicio

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

$id_tema = (empty($_GET['id_tema'])) ? '' : $_GET['id_tema'];
?>

<header>
	<div id="header">
		<div id="head_1">
			<a href="/"><img src="/img/logo_opt.png"></a>
			<?php
			if(empty($id_tema)){
				?>
				<h1>Codeando.org</h1>
				<h2>Cursos de programación gratuitos</h2>
				<?php
			} else {
				?>
				<p>Codeando.org</p>
				<span>Cursos de programación gratuitos</span>
				<?php
			}
			?>
		</div>
		<div id="head_2">
			<a href="http://programacionazteca.mx"><img src="/img/logo_azteca.png"></a>
			<div id="header_text">
				<p>Un proyecto de<br>
				<a href="http://programacionazteca.mx">PROGRAMACION AZTECA</a></p>
			</div>
		</div>
	</div>
</header>