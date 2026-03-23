<?php

require_once __DIR__ . '/includes/conexion.php';

$carga_id = intval($_GET['carga_id']);

if(!$carga_id){
    die("❌ ID inválido");
}

/* 🔹 OBTENER DATOS DE LA CARGA */
$res = $conexion->query("SELECT * FROM cargas WHERE id = $carga_id");

if(!$res || $res->num_rows == 0){
    die("❌ Carga no encontrada");
}

$carga = $res->fetch_assoc();

/* 🔹 DETECTAR TABLA */
$tabla = "datos_crudos_" . strtolower($carga['ciclo']);

/* 🔴 BORRAR CONCENTRADO */
$conexion->query("
    DELETE FROM concentrado_general 
    WHERE CARGA_ID = $carga_id
");

/* 🔴 BORRAR DATOS CRUDOS */
$conexion->query("
    DELETE FROM $tabla 
    WHERE carga_id = $carga_id
");

/* 🔴 BORRAR REGISTRO DE CARGA */
$conexion->query("
    DELETE FROM cargas 
    WHERE id = $carga_id
");

echo "✅ Carga eliminada correctamente";