<?php

function generarVistaHorizontal($conexion,$tabla,$formato,$ciclo){

$ciclo_tabla=str_replace("-","_",$ciclo);

$nombreVista="vista_".strtolower($formato)."_".$ciclo_tabla;

/* permitir concatenaciones grandes */

$conexion->query("SET SESSION group_concat_max_len = 10000000");

/* generar columnas dinámicas */

$sql="

SELECT GROUP_CONCAT(
DISTINCT
CONCAT(
'MAX(CASE WHEN variable = ''',
variable,
''' THEN valor END) AS `',
variable,
'`'
)
ORDER BY variable
SEPARATOR ','
) AS columnas

FROM `$tabla`
WHERE formato='$formato'
AND variable NOT IN ('CV_CCT','FILA_ID','ENTIDAD')

";

$res=$conexion->query($sql);

$row=$res->fetch_assoc();

$columnas=$row['columnas'];

/* columnas base */

$columnas="entidad, fila_id, cv_cct,".$columnas;

/* crear vista */

$sqlVista="

CREATE OR REPLACE VIEW `$nombreVista` AS

SELECT
$columnas

FROM `$tabla`

WHERE formato='$formato'

GROUP BY entidad, fila_id, cv_cct

ORDER BY fila_id

";

$resVista=$conexion->query($sqlVista);

if(!$resVista){
die("Error creando vista: ".$conexion->error);
}

}

?>