<?PHP
/*
	--------------------------------------------------------
	ChiliStats der neue Statistik Counter 
	basierend auf dem Statistik Counter von pawlita.de
	-------------------------------------------------------
	Das Skript unterliegt dem Urheberschutz Gesetz. Alle Rechte und
	copyrights liegen bei dem Autor:
	Adam Pawlita, http://www.chiliscripts.com
	Dies Skript darf frei verwendet und weitergegeben werden, solange
	die angegebenen Copyrightvermerke in allen Teilen des Scripts vor-
	handen bleiben. Für den fehlerfreien Betrieb, oder Schäden die durch
	den Betrieb dieses Skriptes entstehen, übernimmt der Autor keinerlei
	Gewährleistung. Die Inbetriebnahme erfolgt in jedem Falle 
	auf eigenes Risiko des Betreibers.
	-------------------------------------------------------
*/


//
// !! These settings must be changed
//

$email = 'source.compu@gmail.com';
$localhost = 'codeando.dev';
$db_prefix = 'stats_'; // database prefix

// Database Connection
// Datos para conectar la base de datos
if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == $localhost){
	// En local
	$data_db = array(
		'server'=>'localhost', // Servidor
		'user'=>'root', // Ususario
		'pass'=>'', // Password
		'db'=>'codeando_estadisticas', // Base de datos
		'email'=>$email // Email de contacto del admin
		);
} else {
	// En servidor
	$data_db = array(
		'server'=>'localhost', // Servidor
		'user'=>'progra11_paulo86', // Usuario
		'pass'=>'@,!CExNOF-dh', // Password
		'db'=>'progra11_estadisticas', // Base de datos
		'email'=>$email // Email de contacto del admin
		);
}

//
// Optional settings
//

$style = "dark"; // Counter Style "dark" or "light"
$show = "totally"; // Counter shows "totally"  or "last24h"  visitors
$size = "small"; // Size of the counter "small" or "big"

$reload=3*60*60; // Reload lock in seconds (3 * 60 * 60 => 3 hours)
$online=3*60; // online time in seconds (3 * 60 => 3 minutes)
$oldentries=7; // delete Visitor infos after x days (7 => 7 days)

//
// End of settings
//