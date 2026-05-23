<?php
$servidor = "127.0.0.1"; 
$usuario = "root";
$password = "";
$bd = "db_zomai_fep";
$puerto = 3308; // PRUEBA CON 3306 PRIMERO, si no, pon 3308

// La conexión con todos los parámetros
$conn = new mysqli($servidor, $usuario, $password, $bd, $puerto);

// Si falla con 127.0.0.1, intentamos con localhost
if ($conn->connect_error) {
    $conn = new mysqli("localhost", $usuario, $password, $bd, $puerto);
}

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Esto es vital para que los nombres de los vestidos no salgan con símbolos raros
$conn->set_charset("utf8");
?>