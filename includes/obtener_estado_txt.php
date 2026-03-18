<?php

function obtenerEstadoTXT($archivo){

$handle=fopen($archivo,"r");

if(!$handle){
return "";
}

$encabezado=fgetcsv($handle,0,"|");

$col_estado=-1;

for($i=0;$i<count($encabezado);$i++){

$col=trim($encabezado[$i]);

if($col=="ENT_ADMINISTRATIVA"){
$col_estado=$i;
break;
}

}

$fila=fgetcsv($handle,0,"|");

$estado="";

if($col_estado!=-1 && isset($fila[$col_estado])){
$estado=trim($fila[$col_estado]);
}

fclose($handle);

return $estado;

}

?>