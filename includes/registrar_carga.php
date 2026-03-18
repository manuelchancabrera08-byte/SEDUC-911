<?php

function registrarCarga($conexion,$ciclo,$formato,$archivo,$usuario,$entidad){

/* ELIMINAR PROCESOS COLGADOS */
$sql="DELETE FROM cargas
WHERE estado='procesando'
AND ciclo='$ciclo'
AND formato='$formato'
AND entidad='$entidad'";

$conexion->query($sql);

/* 🔥 INSERT INICIAL CORRECTO */
$sql = "INSERT INTO cargas
(ciclo,formato,archivo_original,usuario,estado,entidad,progreso,registros)
VALUES
('$ciclo','$formato','$archivo','$usuario','procesando','$entidad',0,0)";

$conexion->query($sql);

return $conexion->insert_id;

}

?>