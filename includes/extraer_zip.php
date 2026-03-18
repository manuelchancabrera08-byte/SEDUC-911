<?php

function extraerZip($rutaZip){

$zip = new ZipArchive;

if($zip->open($rutaZip) === TRUE){

$carpeta = dirname($rutaZip);

$txt_encontrado = null;

for($i=0; $i < $zip->numFiles; $i++){

$nombre = $zip->getNameIndex($i);

if(strtolower(pathinfo($nombre,PATHINFO_EXTENSION)) == "txt"){

$txt_encontrado = $nombre;

break;

}

}

if(!$txt_encontrado){
return false;
}

$zip->extractTo($carpeta,$txt_encontrado);

$zip->close();

return $carpeta."/".$txt_encontrado;

}

return false;

}