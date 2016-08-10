<?php
/************************************************
Archivo servidor para interactuar con API 1.1 twitter
Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
************************************************/

session_start();

set_time_limit(0);

// Ajustamos la zona horaria
date_default_timezone_set('America/Mexico_City');

require_once '../config.php';
require_once 'Fnc.php';
require_once 'Db.php';

$fnc = new Fnc();
$db = new Db();

if(empty($_POST['type'])){ $type = '';} else { $type = addslashes($_POST['type']);}

switch($type){
	case 'twitter_hashtag':
		twitter_hashtag($fnc, $db);
		break;
}

// Obtenemos los hashtag del curso
function twitter_hashtag($fnc, $db)
{
	$hashtag = $fnc->secure_sql($_POST['hashtag']);
	$limit = 10;

	// Obtenemos los hashtag
	$json = getJsonTweets($hashtag, $limit);

	echo $json;
	exit();
}

/********************************************************************
Funciones predefinidas
********************************************************************/

// Realizamos una busqueda en twitter
function getJsonTweets($query, $num_tweets){
    require_once 'TwitterAPIExchange.php';

    // incluimos los datos de la app de twitter
    $settings = array(
        'oauth_access_token' => "2850118343-LxgVcg3Znr7gNg5G5AbMXijFi0EGkqzC0gFRZFn",
        'oauth_access_token_secret' => "QwgqtyplHIEIzfYIOnMyVvRk8wsKR8UzQR1Dtiv9NZ4LR",
        'consumer_key' => "8ZB12EQl2fpNZsNxxIOQgSsO6",
        'consumer_secret' => "59G4VmM7YElqDcwMEYSJq8NbbOBKLDyAZoC6rY5f0CdIytfu9h"
    );
    
    if($num_tweets>100) $num_tweets = 100;
  
    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    // Para buscar hashtag ?$23= para busqueda normal ?q=
    $getfield = '?q=#'.$query.'&count='.$num_tweets;

    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange($settings);
    $json =  $twitter->setGetfield($getfield)
                 ->buildOauth($url, $requestMethod)
                 ->performRequest();
    return $json;
}