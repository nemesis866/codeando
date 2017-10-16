<?php
/********************************************************************
Avisos para los cursos ya creados

Proyecto: Codeando.org
Author: Paulo Andrade
Email: paulo_866@hotmail.com
Web: http://www.pauloandrade1.com
********************************************************************/

$db = new Db();
$user = $_SESSION['id'];
?>

<div class="alert">
	<img src="/img/alert.png">
	<h1>Quiere dar algun aviso a los usuarios suscritos en uno de sus cursos?</h1>
	<p>1.-Es muy facil, seleccione el curso donde quiere dar el aviso.<br>
		2.- Redacte el mensaje.<br>
		3.- Al enviar el aviso, cada uno de los usuarios suscritos al curso lo recibira via email.</p>
</div>

<form id="form_aviso">
	<p><label>Seleccione el curso:</label></p>
	<p><select id="id_curso" class="input" required>
		<option></option>
		<?php
		// Obtenemos los cursos del usuario
		$result = $db->mysqli_select("SELECT id_curso,titulo FROM cursos WHERE autor='$user' AND public='YES' ORDER BY titulo");
		while($row = $result->fetch_assoc()){
			?><option value="<?php echo $row['id_curso']; ?>"><?php echo $row['titulo']; ?></option><?php
		}
		$result->close();
		?>
	</select></p>
	<p><label>Escriba el contenido del aviso:</label></p>
	<p><textarea id="aviso" class="input"></textarea></p>
	<p><input type="submit" class="submit" value="Enviar aviso"></p>
</form>
<div class="cargando"></div>