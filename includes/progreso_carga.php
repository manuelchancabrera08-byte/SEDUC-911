<?php

include "includes/conexion.php";

header('Content-Type: application/json');

$sql = "SELECT progreso 
FROM cargas 
WHERE estado = 'procesando'
ORDER BY id DESC 
LIMIT 1";

$res = $conexion->query($sql);

if(!$res || $res->num_rows == 0){
    echo json_encode(["progreso"=>100]);
    exit;
}

$row = $res->fetch_assoc();

echo json_encode([
    "progreso" => (int)$row['progreso']
]);