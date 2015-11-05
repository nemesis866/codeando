<?php
include 'config.php';
require_once 'include/Db.php';

$db = new Db();

//
// initialization and visitor Information
//

// Date Time
$time = time();
$day = date("Y.m.d",$time); // YYYY.MM.DD
$month = date("Y.m",$time); // YYYY.MM

// IP adress
$ip = $_SERVER['REMOTE_ADDR'];

if(empty($_SERVER['HTTP_REFERER'])){
	$_SERVER['HTTP_REFERER'] = '';
}

// Get Referrer and Page
if(!empty($_GET["ref"])){
	// from javascript
	$referer = (empty($_GET['href'])) ? '' : $_GET["ref"];
	$page = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);	
} else {
	// from php
	$referer = $_SERVER['HTTP_REFERER'];
	$page = $_SERVER['PHP_SELF']; // with include via php		
}

// cleanup
if(basename($page) == basename(__FILE__)){
	$page="" ; // count not counter.php
}

$server_host=$_SERVER["HTTP_HOST"]; // Server Host
if(substr($server_host,0,4) == "www.") $server_host=substr($server_host,4); // Server Host without www.

$referer_host=parse_url($referer, PHP_URL_HOST); // Referrer Host
if(substr($referer_host,0,4) == "www.") $referer_host=substr($referer_host,4); // Referer Host without www.

// adjust search engines 
if(strstr($referer_host, "google.")){
	$referer_query=parse_url($referer, PHP_URL_QUERY);
	$referer_query.="&";
	preg_match('/q=(.*)&/UiS', $referer_query, $keys);
	
	$keyword=urldecode($keys[1]); // These are the search terms
	$referer_host="Google"; // adjust host
}
if(strstr($referer_host, "yahoo.")){
	$referer_query=parse_url($referer, PHP_URL_QUERY);
	$referer_query.="&";
	preg_match('/p=(.*)&/UiS', $referer_query, $keys);
	
	$keyword=urldecode($keys[1]); // These are the search terms
	$referer_host="Yahoo"; // adjust host
}
if(strstr($referer_host, "bing.")){
	$referer_query=parse_url($referer, PHP_URL_QUERY);
	$referer_query.="&";
	preg_match('/q=(.*)&/UiS', $referer_query, $keys);
	
	$keyword=urldecode($keys[1]); // These are the search terms
	$referer_host="Bing"; // adjust host
}
		
// Language
$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);

//
// Counter
//

// delete old IPs
$anfangGestern = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - 48*60*60 ; // 48*60*60 => after 48 hours
$delete = $db->mysqli_action("DELETE FROM stats_IPs WHERE time<'$anfangGestern'");

// delete old page,referrer,language and keywords
$old_day = date("Y.m.d", mktime(0, 0, 0, date("n"), date("j") - $oldentries, date("Y"))); // delete older than $oldentries(config.php) days
$delete=$db->mysqli_action("DELETE FROM stats_Page WHERE day<='$old_day'");
$delete=$db->mysqli_action("DELETE FROM stats_Referer WHERE day<='$old_day'");
$delete=$db->mysqli_action("DELETE FROM stats_Keyword WHERE day<='$old_day'");
$delete=$db->mysqli_action("DELETE FROM stats_Language WHERE day<='$old_day'");

// insert a new day
$neuerTag=$db->mysqli_select("SELECT id FROM stats_Day WHERE day='$day'");
if ($neuerTag->num_rows==0){ 
	$insert = $db->mysqli_action("INSERT INTO stats_Day (day, user, view) VALUES ('$day', '0', '0')");
}
	
// check reload and set online time
$newuser=0;
$oldreload = $time-$reload;
$gesperrt=$db->mysqli_select("SELECT id FROM stats_IPs WHERE ip='$ip' AND time>'$oldreload' ORDER BY id DESC LIMIT 1");
if ($gesperrt->num_rows==0){
	// new visitor
	$newuser=1;
	$insert = $db->mysqli_action("INSERT INTO stats_IPs (ip, time, online) VALUES ('$ip', '$time', '$time')");
	$update = $db->mysqli_action("UPDATE stats_Day SET user=user+1, view=view+1 WHERE day='$day'");
} else {
	// reload visitor
	$gesperrtID=$db->mysqli_result($gesperrt,0,0);
	$update = $db->mysqli_action("UPDATE stats_IPs SET online='$time' WHERE id='$gesperrtID'");
	$update = $db->mysqli_action("UPDATE stats_Day SET view=view+1 WHERE day='$day'");
}

// Page
if($page <> "") {
	$ergebnis = $db->mysqli_select("SELECT id from stats_Page WHERE page='$page' AND day='$day'");
	if ($ergebnis->num_rows==0){
		$insert = $db->mysqli_action("INSERT INTO stats_Page (day, page, view) VALUES ('$day', '$page', '1')");
	} else { 
		$pageid=$db->mysqli_result($ergebnis,0,0);
		$update = $db->mysql_action("UPDATE stats_Page SET view=view+1 WHERE id='$pageid'");
	}
}
// Referer
if(stristr($server_host, $referer_host) === FALSE AND $referer_host<>"" AND $newuser == 1) {
	$ergebnis = $db->mysqli_select("SELECT id from stats_Referer WHERE referer='$referer_host' AND day='$day'");
	if ($ergebnis->num_rows==0){
		$insert = $db->mysqli_action("INSERT INTO stats_Referer (day, referer, view) VALUES ('$day', '$referer_host', '1')");
	} else { 
		$refererid=$db->mysqli_result($ergebnis,0,0);
		$update = $db->mysql_action("UPDATE stats_Referer SET view=view+1 WHERE id='$refererid'");
	}
}

$keyword = (empty($keyword)) ? '' : $keyword;
// keywords 
if($keyword<>"" AND $newuser == 1) {
	$ergebnis = $db->mysqli_select("SELECT id from stats_Keyword WHERE keyword='$keyword' AND day='$day'");
	if ($ergebnis->num_rows==0){
		$insert = $db->mysqli_action("INSERT INTO stats_Keyword (day, keyword, view) VALUES ('$day', '$keyword', '1')");
	} else { 
		$keywordid=$db->mysqli_result($ergebnis,0,0);
		$update->mysqli_action("UPDATE stats_Keyword SET view=view+1 WHERE id='$keywordid'");
	}
}
// LanDELETE
if($language <> "" AND $newuser == 1) {
	$ergebnis = $db->mysqli_select("SELECT id FROM stats_Language WHERE language='$language'");
	if ($ergebnis->num_rows==0){
		$insert = $db->mysqli_action("INSERT INTO stats_Language (day, language, view) VALUES ('$day', '$language', '1')");
	} else { 
		$languageid = $db->mysqli_result($ergebnis,0,0);
		$update = $db->mysqli_action("UPDATE stats_Language SET view=view+1 WHERE id='$languageid'");
	}
}

//
// Generate Image
//

// Get Value from DB
if($show == "last24h"){
	// Last24h
	$islast = $time-24*60*60;
	$abfrage = $db->mysqli_select("SELECT Count(id) FROM stats_IPs WHERE time>='$islast'");
	$value = $db->mysqli_result($abfrage,0,0);
	$title = "Last 24 hours";
	$abfrage->close();
} else {
	// Totally Visitors	
	$abfrage=$db->mysqli_select("SELECT sum(user) FROM stats_Day");
	$value=$db->mysqli_result($abfrage,0,0);
	$title="Totally Visitors";
	$abfrage->close();
}

$einheit = '';
// short value
if($value > 999){
	$value = $value / 1000;
	$einheit = "k";
}
if($value > 999){
	$value = $value / 1000;
	$einheit = "m";
}
if($value > 999){
	$value = ">999";
	$einheit = "m";
} else { 
	if ($value >= 10){
		$value = round($value, 0);
	} else {
		$value = round($value, 1);
	}
}

$value .= $einheit;

// Variables
$title_font = "OpenSans-Regular.ttf";
$value_font = "OpenSans-Bold.ttf";

if($size == "small"){
	$width = 90;
	$height = 20;
	$title_font_size = 8;
	$value_font_size = 9;
	$title_pos_y = 15;
	$value_pos_y = 16;	
	
	// short title
	if($show == "last24h"){
		$title = "Last24h";
	} else {
		$title = "Visitors";
	}

	// left title
	$size = imagettfbbox($title_font_size, 0, $title_font, $title);
	$titleWidth = $size[2] - $size[0];
	$title_pos_x = 8;

	// right center value
	$size = imagettfbbox($value_font_size, 0, $value_font, $value);
	$valueWidth = $size[2] - $size[0];
	$space_left = $title_pos_x + $titleWidth;
	$value_pos_x = $space_left + ((($width - $space_left) / 2) - ($valueWidth / 2));
} else {
	$width = 90;
	$height = 55;
	$title_font_size = 8;
	$value_font_size = 24;
	$title_pos_y = 15;
	$value_pos_y = 48;
	
	// center title
	$size = imagettfbbox($title_font_size, 0, $title_font, $title);
	$textWidth = $size[2] - $size[0];
	$title_pos_x = ($width / 2) - ($textWidth / 2);
	
	// center value
	$size = imagettfbbox($value_font_size, 0, $value_font, $value);
	$textWidth = $size[2] - $size[0];
	$value_pos_x = ($width / 2) - ($textWidth / 2);
}

//  Create a blank image
$im = imagecreatetruecolor($width, $height);	
	
// Colors
if($style == "light"){
	$bg_color = imagecolorallocatealpha($im, 235,235,235,0);
	$title_color = imagecolorallocate($im, 50,50,50);
	$value_color = imagecolorallocate($im, 25,25,25);	
} else {
	$bg_color = imagecolorallocatealpha($im, 50,50,50,0);
	$title_color = imagecolorallocate($im, 255,255,255);
	$value_color = imagecolorallocate($im, 255,255,255);
}

$shadow_color = imagecolorallocatealpha($im, 0,0,0,115);
$red = imagecolorallocate($im, 223,1,1);

// Fill BG color
imagefill($im, 0, 0, $bg_color);
// Red line
imageline($im, 0, 0, $width, 0, $red);
imageline($im, 0, 1, $width, 1, $red);
// title
imagettftext($im, $title_font_size, 0, $title_pos_x+2, $title_pos_y+2, $shadow_color, $title_font, $title); 
imagettftext($im, $title_font_size, 0, $title_pos_x, $title_pos_y, $title_color, $title_font, $title); 
// value
imagettftext($im, $value_font_size, 0, $value_pos_x+2, $value_pos_y+2, $shadow_color, $value_font, $value);
imagettftext($im, $value_font_size, 0, $value_pos_x, $value_pos_y, $value_color, $value_font, $value);

// image output
header("Content-type: image/png");
// create PNG
imagepng($im);
// destroy temp image
imagedestroy($im);
