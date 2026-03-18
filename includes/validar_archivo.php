<?php

function normalizarTexto($texto){

$texto = strtoupper($texto);

/* quitar acentos */

$buscar = ['Á','É','Í','Ó','Ú','Ü','Ñ'];
$reemplazar = ['A','E','I','O','U','U','N'];

$texto = str_replace($buscar,$reemplazar,$texto);

/* convertir guiones y guion bajo a espacio */

$texto = str_replace(['_','-'],' ',$texto);

/* eliminar caracteres raros */

$texto = preg_replace('/[^A-Z0-9 ]/',' ',$texto);

/* limpiar espacios duplicados */

$texto = preg_replace('/\s+/',' ',$texto);

return trim($texto);

}

function validarArchivoFormato($archivo,$formato,$catalogoFormatos){

$archivo = normalizarTexto($archivo);

/* validar que exista en catálogo */

if(!isset($catalogoFormatos[$formato])){
return false;
}

/* obtener nombre del formato */

$nombreFormato = normalizarTexto($catalogoFormatos[$formato]);

/* validar contra nombre del formato */

if(strpos($archivo,$nombreFormato)!==false){
return true;
}

return false;

}

function obtenerEntidadDesdeTXT($ruta_archivo){

$handle = fopen($ruta_archivo,"r");

if(!$handle){
return null;
}

/* leer encabezado */

$header = fgets($handle);
$columnas = explode("|",$header);

/* limpiar espacios */

$columnas = array_map('trim',$columnas);

/* buscar columna */

$posicion = array_search("ENT_ADMINISTRATIVA",$columnas);

if($posicion === false){
fclose($handle);
return null;
}

/* leer primera fila de datos */

$linea = fgets($handle);
$datos = explode("|",$linea);

$entidad = trim($datos[$posicion]);

fclose($handle);

return $entidad;

}

?>