<?php
/************************************************
Archivo para preparar un parametro para insertar en base de datos

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

// Incluimos la conexion a la base de datos
require_once '../include/Db.php';

class Secure
{
    private $_db;

    public function __construct()
    {
        // Creamos el objeto a la base de datos
        $this->_db = new Db();
    }
    // Hacemos seguros los parametros tipo GET
    function secureSuperGlobalGET(&$value, $key)
    {
        $_GET[$key] = htmlspecialchars(stripslashes($_GET[$key]));
        $_GET[$key] = str_ireplace("script", "blocked", $_GET[$key]);
        //$_GET[$key] = mysqli_escape_string($_GET[$key]);
        $_GET[$key] = $this->_db->mysqli_secure($_GET[$key]);
        return $_GET[$key];
    }
    // Hacemos seguros los parametros tipo POST
    function secureSuperGlobalPOST(&$value, $key)
    {
        $_POST[$key] = htmlspecialchars(stripslashes($_POST[$key]));
        $_POST[$key] = str_ireplace("script", "blocked", $_POST[$key]);
        //$_POST[$key] = mysqli_escape_string($_POST[$key]);
        $_POST[$key] = $this->_db->mysqli_secure($_POST[$key]);
        return $_POST[$key];
    }
    // Aplicamos los metodos a todos los parametros
    function secureGlobals()
    {
        array_walk($_GET, array($this, 'secureSuperGlobalGET'));
        array_walk($_POST, array($this, 'secureSuperGlobalPOST'));
    }
} 