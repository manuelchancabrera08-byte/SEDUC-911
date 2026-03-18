<?php

include "includes/generar_vista_horizontal.php";
include "includes/conexion.php";
include "includes/crear_tabla_ciclo.php";
include "includes/procesar_txt.php";
include "includes/registrar_carga.php";
include "includes/finalizar_carga.php";
include "includes/extraer_zip.php";

$ciclo = $_POST['ciclo'];
$formato = $_POST['formato'];
$archivo = $_POST['archivo'];
$usuario = $_POST['usuario'];

$extension = strtolower(pathinfo($archivo,PATHINFO_EXTENSION));

$rutaArchivo = "uploads/".$archivo;

if($extension == "zip"){

$txt_extraido = extraerZip($rutaArchivo);

if(!$txt_extraido){
echo json_encode(["estado"=>"error"]);
exit;
}

$rutaArchivo = $txt_extraido;

}

$carga_id = registrarCarga($conexion,$ciclo,$formato,$archivo,$usuario);

$tabla = crearTablaCiclo($conexion,$ciclo);

procesarTXT($conexion,$tabla,$rutaArchivo,$ciclo,$formato,$carga_id);

finalizarCarga($conexion,$carga_id,0,0,0);
generarVistaHorizontal($conexion,$tabla,$formato,$ciclo);

echo json_encode([
"estado"=>"ok",
"ciclo"=>$ciclo
]);