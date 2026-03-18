<?php

$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "planeacion_seduc_911";

$conexion = new mysqli($host,$usuario,$password,$base_datos,3307);

if($conexion->connect_error){
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");