<?php
/************************************************
Archivo index del admin

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

session_start();

require_once 'config.php';
require_once 'include/Fnc.php';
require_once 'include/Healt.php';
require_once 'include/Db.php';

$index = new Admin();

$index->verificacion();
$index->set_title('Administración - Codeando.org');
$index->mostrar_contenido();