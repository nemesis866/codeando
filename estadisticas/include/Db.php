<?php
/************************************************
Clase para la conexion a la base de datos

Proyecto: Codeando.org
Author: Paulo Andrade
Web: http://www.pauloandrade1.com
Email: source.compu@gmail.com
************************************************/

class Db
{
	private $_server;
	private $_user;
	private $_pass;
	private $_db;

	// Datos de conexion por defecto
	public function __construct()
	{
		// Incluimos el archivo de configuracion
		// Ubicado en el directorio raiz
		include $_SERVER['DOCUMENT_ROOT'].'/estadisticas/config.php';

		// Añadimos los parametros de conexion
		$this->_server = $data_db['server'];
		$this->_user = $data_db['user'];
		$this->_pass = $data_db['pass'];
		$this->_db = $data_db['db'];
	}
	// Funcion para obtener informacion (SELECT)
	public function mysqli_select($sql)
	{
		// Creamos un nuevo objeto mysqli
		$mysqli = new mysqli($this->_server,$this->_user,$this->_pass,$this->_db);

		// Probamos la conexion
		if($mysqli->connect_errno){
			// Si nos da error al conectar mostramos mensaje
			die('Error al conectar a la base de datos: '.$mysqli->connect_errno);
		}

		// Si no hay exito en la  consulta mostramos el error
		if(!$result = $mysqli->query($sql)){
			// Error en la consulta
			die('Error en la consulta: '.$mysqli->error);
		}

		// Cerramos la conexion a la base de datos
		$mysqli->close();

		return $result;
	}
	// Funcion para insertar, actualizar o eliminar registros 
	// (INSERT, DELETE, UPDATE)
	public function mysqli_action($sql)
	{
		// Creamos un nuevo objeto mysqli
		$mysqli = new mysqli($this->_server,$this->_user,$this->_pass,$this->_db);

		// Probamos la conexion
		if($mysqli->connect_errno){
			// Si nos da error al conectar mostramos mensaje
			die('Error al conectar a la base de datos: '.$mysqli->connect_errno);
		}
		// Si hay error al insertar el registro lo mostramos
		if(!$result = $mysqli->query($sql)){
			// Error al insertar
			die('Error en la consulta: '.$mysqli->error);
		}

		// Obtenemos el ID del registro que acabamos de insertar
		$id = $mysqli->insert_id;

		// Obtenemos el total de filas afectadas
		$affected_rows = $mysqli->affected_rows;

		// Cerramos la conexion a la base de datos
		$mysqli->close();

		// Retornamos el ID del registro que acabamos de insertar
		return $id;
	}
	// Esta funcion obtiene registros 
	public function mysqli_result($res, $row, $field=0)
	{
	    $res->data_seek($row);
	    $datarow = $res->fetch_array();
	    
	    return $datarow[$field];
	}
}