<?php

set_time_limit(0);
ini_set('memory_limit','1024M');

include "insert_masivo.php";

function procesarTXT($conexion,$tabla,$archivo,$ciclo,$formato,$carga_id){

$handle=fopen($archivo,"r");

if(!$handle){
die("No se pudo abrir el archivo TXT");
}

/* LEER ENCABEZADO */
$encabezado=fgetcsv($handle,0,"|");

if(!$encabezado){
die("Error al leer encabezado");
}

/* detectar columna entidad */
$col_entidad=-1;
$total_columnas = count($encabezado);

for($i=0;$i<$total_columnas;$i++){

$col=trim($encabezado[$i]);

if($col=="ENT_ADMINISTRATIVA"){
$col_entidad=$i;
break;
}

}

$bloque=[];
$contador=0;
$limite=5000;

$fila_id=0;
$entidad_global="";

/* 🔥 TOTAL ESTIMADO REAL */
$columnas_utiles = max(1, $total_columnas - 1);
$total_estimado = 0;

/* 🔥 CONTADOR REAL */
$insertados = 0;

while(($fila=fgetcsv($handle,0,"|"))!==false){

$fila_id++;

/* 🔥 SUMAR TOTAL ESPERADO */
$total_estimado += $columnas_utiles;

/* 🔥 PROGRESO REAL CADA 500 FILAS */
if($fila_id % 500 == 0){

if($total_estimado > 0){
$progreso = round(($insertados / $total_estimado) * 100);
}else{
$progreso = 0;
}

/* evitar que llegue a 100 antes de tiempo */
if($progreso >= 100){
$progreso = 99;
}

$conexion->query("
UPDATE cargas 
SET progreso=$progreso
WHERE id=$carga_id
");

}

$cv_cct = isset($fila[0]) ? str_replace("'","\'",$fila[0]) : '';

$entidad="";

if($col_entidad!=-1 && isset($fila[$col_entidad])){
$entidad=str_replace("'","\'",trim($fila[$col_entidad]));
}

if($entidad_global=="" && $entidad!=""){
$entidad_global=$entidad;
}

for($i=1;$i<$total_columnas;$i++){

$variable=trim($encabezado[$i]);

if($variable=="") continue;

$valor = isset($fila[$i]) ? str_replace("'","\'",$fila[$i]) : '';

$bloque[]="(
'$entidad',
'$ciclo',
'$formato',
'$cv_cct',
'$variable',
'$valor',
$carga_id,
$fila_id
)";

$contador++;

if($contador >= $limite){

insertarBloque($conexion,$tabla,$bloque,$carga_id);

/* 🔥 SUMAR INSERTADOS REALES */
$insertados += $contador;

/* 🔥 ACTUALIZAR PROGRESO JUSTO DESPUÉS DEL INSERT */
if($total_estimado > 0){
$progreso = round(($insertados / $total_estimado) * 100);
}else{
$progreso = 0;
}

if($progreso >= 100){
$progreso = 99;
}

$conexion->query("
UPDATE cargas 
SET progreso=$progreso
WHERE id=$carga_id
");

$bloque=[];
$contador=0;

}

}

}

/* insertar restante */
if(!empty($bloque)){
insertarBloque($conexion,$tabla,$bloque,$carga_id);

/* 🔥 SUMAR RESTANTE */
$insertados += $contador;
}

/* actualizar entidad */
if($entidad_global!=""){

$sql="UPDATE cargas 
SET entidad='$entidad_global'
WHERE id=$carga_id";

$conexion->query($sql);

}

/* 🔥 PROGRESO FINAL REAL */
$conexion->query("
UPDATE cargas 
SET progreso=100
WHERE id=$carga_id
");

fclose($handle);

}
?>